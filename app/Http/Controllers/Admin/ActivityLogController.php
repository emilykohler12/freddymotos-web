<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Mechanic;
use App\Models\Order;
use App\Models\SupplierPurchase;
use App\Models\WorkshopInquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        // Los pedidos cargados a mano (Local) no necesitan seguimiento acá: ya están cerrados al cargarlos.
        $trackedOrigins = [Order::ORIGIN_WEB, Order::ORIGIN_WHATSAPP];

        $orderStatuses = [
            Order::STATUS_PENDIENTE => 'Pendiente',
            Order::STATUS_ENVIADO => 'Enviado',
            Order::STATUS_ENTREGADO => 'Entregado',
            Order::STATUS_CANCELADO => 'Cancelado',
        ];

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDIENTE => 'Pendiente',
            Order::PAYMENT_STATUS_PAGADO => 'Pagado',
            Order::PAYMENT_STATUS_RECHAZADO => 'Rechazado',
        ];

        return view('admin.activity.index', [
            'activeTab' => $request->string('tab', 'ventas')->toString(),
            'orderStatuses' => $orderStatuses,
            'paymentStatuses' => $paymentStatuses,
            'realPaymentMethods' => Order::REAL_PAYMENT_METHODS,

            // Ventas (Web + WhatsApp unificadas): pedidos abiertos, para marcar pagado y enviado.
            'orders' => Order::with('customer')
                ->whereIn('origin', $trackedOrigins)
                ->whereNotIn('status', [Order::STATUS_ENTREGADO, Order::STATUS_CANCELADO])
                ->latest()
                ->get(),

            // Consultas nuevas (pendientes de responder).
            'inquiries' => WorkshopInquiry::latest()->paginate(15, ['*'], 'inquiries_page')->withQueryString(),

            // Pagos pendientes: deuda de mecánicos + pedidos (web/whatsapp) que el cliente no pagó.
            'mechanicsDebt' => Mechanic::withTrashed()
                ->with(['jobs' => fn ($q) => $q->where('pagado', false)])
                ->get()
                ->map(fn (Mechanic $m) => ['mechanic' => $m, 'total' => (float) $m->jobs->sum('monto_a_pagar')])
                ->filter(fn ($m) => $m['total'] > 0)
                ->sortByDesc('total')
                ->values(),
            'unpaidOrders' => Order::with('customer')
                ->whereIn('origin', $trackedOrigins)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)
                ->latest()
                ->get(),

            // Reembolsos: pedidos que el admin marcó para reembolsar.
            'refundOrders' => Order::with('customer')
                ->where('refund_status', Order::REFUND_STATUS_PENDIENTE)
                ->latest()
                ->get(),

            // Vencimiento de gastos: gastos recurrentes vencidos + deuda a proveedores.
            'dueExpenses' => Expense::due(),
            'pendingSupplierPayments' => SupplierPurchase::with('supplier')
                ->where('status', SupplierPurchase::STATUS_PENDIENTE)
                ->orderBy('purchased_at')
                ->get(),
        ]);
    }
}
