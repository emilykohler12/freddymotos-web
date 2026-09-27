<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Mechanic;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
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
            'activeTab' => $request->string('tab', 'web')->toString(),
            'orderStatuses' => $orderStatuses,
            'paymentStatuses' => $paymentStatuses,
            'realPaymentMethods' => Order::REAL_PAYMENT_METHODS,

            // Ventas Web: pedidos abiertos (no entregados ni cancelados), para hacer seguimiento del envío.
            'webOrders' => Order::with('customer')
                ->where('origin', Order::ORIGIN_WEB)
                ->whereNotIn('status', [Order::STATUS_ENTREGADO, Order::STATUS_CANCELADO])
                ->latest()
                ->get(),

            // Ventas WhatsApp: coordinados a mano, seguimiento de pago y envío.
            'whatsappOrders' => Order::with('customer')
                ->where('origin', Order::ORIGIN_WHATSAPP)
                ->whereNotIn('status', [Order::STATUS_ENTREGADO, Order::STATUS_CANCELADO])
                ->latest()
                ->get(),

            // Consultas nuevas (pendientes de responder).
            'inquiries' => WorkshopInquiry::latest()->paginate(15, ['*'], 'inquiries_page')->withQueryString(),

            // Pedidos (web + whatsapp) que faltan enviar: no entregados ni cancelados.
            'toShip' => Order::with('customer')
                ->whereIn('origin', $trackedOrigins)
                ->whereIn('status', [Order::STATUS_PENDIENTE, Order::STATUS_ENVIADO])
                ->latest()
                ->get(),

            // Pedidos (web + whatsapp) que falta que el cliente pague.
            'toCollect' => Order::with('customer')
                ->whereIn('origin', $trackedOrigins)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)
                ->latest()
                ->get(),

            // Pagos pendientes: deuda de mecánicos + pedidos de WhatsApp sin pagar.
            'mechanicsDebt' => Mechanic::withTrashed()
                ->with(['jobs' => fn ($q) => $q->where('pagado', false)])
                ->get()
                ->map(fn (Mechanic $m) => ['mechanic' => $m, 'total' => (float) $m->jobs->sum('monto_a_pagar')])
                ->filter(fn ($m) => $m['total'] > 0)
                ->sortByDesc('total')
                ->values(),
            'whatsappUnpaid' => Order::with('customer')
                ->where('origin', Order::ORIGIN_WHATSAPP)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)
                ->latest()
                ->get(),
            'pendingSupplierPayments' => SupplierPurchase::with('supplier')
                ->whereColumn('paid_amount', '<', 'amount')
                ->orderBy('purchased_at')
                ->get(),
            'pendingRefunds' => Order::with('customer')
                ->where('status', Order::STATUS_CANCELADO)
                ->where('payment_status', Order::PAYMENT_STATUS_PAGADO)
                ->latest()
                ->get(),

            // Stock bajo / sin stock.
            'lowStock' => Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get(),
            'outOfStock' => Product::where('active', true)->where('stock', '<=', 0)->get(),

            // Gastos recurrentes que ya vencieron (toca pagar de nuevo).
            'dueExpenses' => Expense::due(),

            // Promociones activas que vencen dentro de los próximos 7 días.
            'expiringPromotions' => Promotion::where('active', true)
                ->whereNotNull('ends_at')
                ->whereDate('ends_at', '>=', now())
                ->whereDate('ends_at', '<=', now()->addDays(7))
                ->orderBy('ends_at')
                ->get(),
        ]);
    }
}
