<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingCompany;
use App\Models\ShippingZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $date = $request->query('date', '');
        $sort = $request->query('sort', 'date_desc');

        $orders = Order::with('customer')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($date !== '', fn ($q) => $q->whereDate('created_at', $date))
            ->when($sort === 'date_desc', fn ($q) => $q->latest())
            ->when($sort === 'date_asc', fn ($q) => $q->oldest())
            ->when($sort === 'customer_asc', fn ($q) => $q->orderBy(Customer::select('name')->whereColumn('customers.id', 'orders.customer_id')))
            ->when($sort === 'customer_desc', fn ($q) => $q->orderByDesc(Customer::select('name')->whereColumn('customers.id', 'orders.customer_id')))
            ->when($sort === 'total_desc', fn ($q) => $q->orderByDesc('total'))
            ->when($sort === 'total_asc', fn ($q) => $q->orderBy('total'))
            ->paginate(15)
            ->withQueryString();

        $paidOrdersQuery = Order::with('customer')->paid();

        $income = [
            'orders' => (clone $paidOrdersQuery)->latest('paid_at')->paginate(15, ['*'], 'ingresos_page')->withQueryString(),
            'total' => (clone $paidOrdersQuery)->sum('total'),
        ];

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => $this->statuses(),
            'paymentStatuses' => $this->paymentStatuses(),
            'income' => $income,
            'search' => $search,
            'date' => $date,
            'sort' => $sort,
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::where('active', true)->orderBy('name')->get(),
            'companies' => ShippingCompany::where('active', true)->with('zones')->orderBy('name')->get(),
            'realPaymentMethods' => Order::REAL_PAYMENT_METHODS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'new_customer_name' => ['required_without:customer_id', 'nullable', 'string', 'max:150'],
            'new_customer_phone' => ['required_without:customer_id', 'nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]+$/'],
            'new_customer_email' => ['nullable', 'email', 'max:160'],
            'delivery_method' => ['required', 'in:retiro,envio'],
            'shipping_zone_id' => ['nullable', 'required_if:delivery_method,envio', 'exists:shipping_zones,id'],
            'shipping_address' => ['nullable', 'required_if:delivery_method,envio', 'string', 'max:255'],
            'product_id' => ['required', 'array', 'min:1'],
            'product_id.*' => ['exists:products,id'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['nullable', 'integer', 'min:1'],
            'price' => ['required', 'array'],
            'price.*' => ['nullable', 'numeric', 'min:0'],
            'real_payment_method' => ['required', 'in:' . implode(',', array_keys(Order::REAL_PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = DB::transaction(function () use ($data) {
            if ($data['customer_id'] ?? null) {
                $customer = Customer::findOrFail($data['customer_id']);
            } else {
                $customer = Customer::updateOrCreate(
                    ['phone' => $data['new_customer_phone']],
                    ['name' => $data['new_customer_name'], 'email' => $data['new_customer_email'] ?? null],
                );
            }

            $selectedProducts = Product::whereIn('id', $data['product_id'])->get()->keyBy('id');

            $lines = collect($data['product_id'])->map(function ($productId) use ($data, $selectedProducts) {
                $product = $selectedProducts->get((int) $productId);
                $quantity = max(1, (int) ($data['quantity'][$productId] ?? 1));
                $price = $data['price'][$productId] !== '' && $data['price'][$productId] !== null
                    ? (float) $data['price'][$productId]
                    : (float) $product->price;

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $quantity,
                ];
            });

            $subtotal = (float) $lines->sum('subtotal');
            $shippingZone = ($data['shipping_zone_id'] ?? null) ? ShippingZone::find($data['shipping_zone_id']) : null;
            $shippingCost = $shippingZone ? (float) $shippingZone->price : 0;

            /** @var Order $order */
            $order = $customer->orders()->create([
                'status' => Order::STATUS_PENDIENTE,
                'payment_status' => Order::PAYMENT_STATUS_PAGADO,
                'delivery_method' => $data['delivery_method'],
                'payment_method' => $data['real_payment_method'],
                'origin' => Order::ORIGIN_WHATSAPP,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_zone_id' => $shippingZone?->id,
                'total' => $subtotal + $shippingCost,
                'shipping_address' => $data['delivery_method'] === 'envio' ? ($data['shipping_address'] ?? null) : null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => now(),
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                ]);

                $line['product']->decrement('stock', min($line['quantity'], $line['product']->stock));
            }

            return $order;
        });

        ActivityLog::log('pedido_whatsapp', "Pedido #{$order->id} cargado a mano (WhatsApp) para {$order->customer->name} ({$order->formatted_total})", $order);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Pedido cargado.');
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'customer');

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => $this->statuses(),
            'paymentStatuses' => $this->paymentStatuses(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $needsRealMethod = $order->payment_method === Order::PAYMENT_WHATSAPP
            && $request->input('payment_status') === Order::PAYMENT_STATUS_PAGADO;

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys($this->statuses()))],
            'payment_status' => ['required', 'in:' . implode(',', array_keys($this->paymentStatuses()))],
            'real_payment_method' => [$needsRealMethod ? 'required' : 'nullable', 'in:' . implode(',', array_keys(Order::REAL_PAYMENT_METHODS))],
        ]);

        if (! empty($data['real_payment_method'])) {
            $data['payment_method'] = $data['real_payment_method'];
        }
        unset($data['real_payment_method']);

        $order->update($data);

        if ($data['payment_status'] === Order::PAYMENT_STATUS_PAGADO && ! $order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return back()->with('status', 'Pedido actualizado.');
    }

    private function statuses(): array
    {
        return [
            Order::STATUS_PENDIENTE => 'Pendiente',
            Order::STATUS_ENVIADO => 'Enviado',
            Order::STATUS_ENTREGADO => 'Entregado',
            Order::STATUS_CANCELADO => 'Cancelado',
        ];
    }

    private function paymentStatuses(): array
    {
        return [
            Order::PAYMENT_STATUS_PENDIENTE => 'Pendiente',
            Order::PAYMENT_STATUS_PAGADO => 'Pagado',
            Order::PAYMENT_STATUS_RECHAZADO => 'Rechazado',
        ];
    }
}
