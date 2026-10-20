<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ShippingZone;
use App\Models\SiteSetting;
use App\Services\MercadoPagoService;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(Cart $cart): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'))
                ->with('status', 'Iniciá sesión para ver tu carrito y poder comprar.');
        }

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('checkout.index', [
            'lines' => $cart->lines(),
            'subtotal' => $cart->subtotal(),
            'checkoutToken' => (string) Str::uuid(),
            'shippingZones' => ShippingZone::with('company')
                ->whereHas('company', fn ($q) => $q->where('active', true))
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(CheckoutRequest $request, Cart $cart): RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'))
                ->with('status', 'Iniciá sesión para ver tu carrito y poder comprar.');
        }

        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $data = $request->validated();

        // Cada formulario trae un token único. Si llega el mismo dos veces (doble clic, reenvío),
        // solo el primero crea el pedido: Cache::add es atómico y devuelve false si ya existía.
        $token = $data['checkout_token'] ?? null;

        if ($token && ! Cache::add("checkout:{$token}", true, now()->addMinutes(10))) {
            return redirect()->route('cart.index')->with(
                'status',
                'Ya estamos procesando tu pedido. Si no te llevó al pago, volvé a intentarlo en unos minutos.',
            );
        }

        $order = DB::transaction(function () use ($data, $lines) {
            $customer = Customer::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'] ?? null,
                    'address' => $data['address'] ?? null,
                ],
            );

            $subtotal = (float) $lines->sum('subtotal');

            $isEnvio = $data['delivery_method'] === Order::DELIVERY_ENVIO;
            $shippingZone = $isEnvio && ! empty($data['shipping_zone_id'])
                ? ShippingZone::find($data['shipping_zone_id'])
                : null;
            $shippingCost = $shippingZone ? (float) $shippingZone->price : 0;

            /** @var Order $order */
            $order = $customer->orders()->create([
                'status' => Order::STATUS_PENDIENTE,
                'delivery_method' => $data['delivery_method'],
                'payment_method' => $data['payment_method'],
                'origin' => $data['payment_method'] === Order::PAYMENT_WHATSAPP ? Order::ORIGIN_WHATSAPP : Order::ORIGIN_WEB,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_zone_id' => $shippingZone?->id,
                'total' => $subtotal + $shippingCost,
                'shipping_address' => $isEnvio ? ($data['address'] ?? null) : null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_name' => $line->product->name,
                    'unit_price' => $line->unit_price,
                    'quantity' => $line->quantity,
                    'subtotal' => $line->subtotal,
                ]);
            }

            // Falla (y revierte todo) si algún producto no tiene stock suficiente.
            $order->discountStock();

            return $order;
        });

        if ($data['payment_method'] === Order::PAYMENT_WHATSAPP) {
            $cart->clear();

            return redirect()->away($this->whatsappLink($order));
        }

        // Mercado Pago
        try {
            $preference = app(MercadoPagoService::class)->createPreference($order);
            $order->update(['mp_preference_id' => $preference['id']]);
            $cart->clear();

            return redirect()->away($preference['init_point']);
        } catch (\Throwable $e) {
            report($e);

            // El pedido nunca se cobró: se cancela y vuelve el stock, para no dejar pedidos
            // huérfanos ni duplicados cuando el cliente reintenta.
            $order->abandon();

            return redirect()->route('checkout.show')->with(
                'error',
                'No pudimos iniciar el pago con Mercado Pago. Revisá la configuración o elegí coordinar por WhatsApp.',
            );
        }
    }

    /** Pantalla de "gracias" — también es la URL de retorno de Mercado Pago. */
    public function success(Order $order): View
    {
        $order->load('items', 'customer');

        return view('checkout.success', [
            'order' => $order,
            'mpStatus' => request('status'), // success | failure | pending (si viene de MP)
            'whatsappLink' => $order->payment_method === Order::PAYMENT_WHATSAPP
                ? $this->whatsappLink($order)
                : null,
        ]);
    }

    /** Arma el link de WhatsApp con el detalle completo del pedido. */
    private function whatsappLink(Order $order): string
    {
        $order->loadMissing('items', 'customer');
        $settings = SiteSetting::current();
        $to = preg_replace('/\D+/', '', (string) $settings->whatsapp);

        $lines = ['Hola, me gustaría hacer un pedido', '', "*Nuevo pedido* #{$order->id}", ''];

        foreach ($order->items as $item) {
            $lines[] = sprintf(
                '• %d x %s — $ %s',
                $item->quantity,
                $item->product_name,
                number_format((float) $item->subtotal, 0, ',', '.'),
            );
        }

        $lines[] = '';
        $lines[] = '*Total: ' . $order->formatted_total . '*';
        $lines[] = '';
        $lines[] = "Cliente: {$order->customer->name}";
        $lines[] = "Teléfono: {$order->customer->phone}";

        if ($order->customer->email) {
            $lines[] = "Email: {$order->customer->email}";
        }

        $lines[] = 'Entrega: ' . ($order->delivery_method === Order::DELIVERY_ENVIO ? 'Envío' : 'Retiro en el local');

        if ($order->shippingZone) {
            $lines[] = "Zona de envío: {$order->shippingZone->name} (\$ " . number_format((float) $order->shipping_cost, 0, ',', '.') . ')';
        }

        if ($order->shipping_address) {
            $lines[] = "Dirección: {$order->shipping_address}";
        }

        if ($order->notes) {
            $lines[] = "Aclaraciones: {$order->notes}";
        }

        $text = rawurlencode(implode("\n", $lines));

        return $to
            ? "https://wa.me/{$to}?text={$text}"
            : "https://wa.me/?text={$text}";
    }
}
