<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with('customer')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => $this->statuses(),
            'paymentStatuses' => $this->paymentStatuses(),
        ]);
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
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys($this->statuses()))],
            'payment_status' => ['required', 'in:' . implode(',', array_keys($this->paymentStatuses()))],
        ]);

        $order->update($data);

        if ($data['payment_status'] === Order::PAYMENT_STATUS_PAGADO && ! $order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        ActivityLog::log('pedido', "Pedido #{$order->id} actualizado: {$data['status']} / pago {$data['payment_status']}", $order);

        return back()->with('status', 'Pedido actualizado.');
    }

    private function statuses(): array
    {
        return [
            Order::STATUS_PENDIENTE => 'Pendiente',
            Order::STATUS_PROCESANDO => 'Procesando',
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
