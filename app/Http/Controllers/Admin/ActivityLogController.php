<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\SupplierPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')->latest()->paginate(30);

        $openOrders = Order::with('customer')
            ->whereNotIn('status', [Order::STATUS_ENTREGADO, Order::STATUS_CANCELADO])
            ->latest()
            ->take(20)
            ->get();

        return view('admin.activity.index', [
            'activeTab' => $request->string('tab', 'notificaciones')->toString(),
            'logs' => $logs,
            'whatsappOrders' => $openOrders->where('origin', Order::ORIGIN_WHATSAPP)->values(),
            'webOrders' => $openOrders->where('origin', Order::ORIGIN_WEB)->values(),
            'orderStatuses' => [
                Order::STATUS_PENDIENTE => 'Pendiente',
                Order::STATUS_PROCESANDO => 'Procesando',
                Order::STATUS_ENVIADO => 'Enviado',
                Order::STATUS_ENTREGADO => 'Entregado',
                Order::STATUS_CANCELADO => 'Cancelado',
            ],
            'lowStock' => Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get(),
            'outOfStock' => Product::where('active', true)->where('stock', '<=', 0)->get(),
            'pendingSupplierPayments' => $this->pendingSupplierPayments(),
            'pendingRefunds' => $this->pendingRefunds(),
            'dueExpenses' => $this->dueExpenses(),
            'ingresoCategories' => ExpenseCategory::where('type', Expense::TYPE_INGRESO)->orderBy('name')->get(),
            'otrosIngresos' => Expense::with('expenseCategory')->where('type', Expense::TYPE_INGRESO)->orderByDesc('incurred_on')->get(),
            'inventoryProducts' => Product::orderBy('name')->get(['id', 'name', 'stock']),
            'stockReasons' => StockMovement::REASONS,
            'stockMovements' => StockMovement::with(['product', 'user'])->latest()->take(20)->get(),
        ]);
    }

    /** Compras a proveedores con saldo pendiente de pago (deuda > 0). */
    private function pendingSupplierPayments()
    {
        return SupplierPurchase::with('supplier')
            ->whereColumn('paid_amount', '<', 'amount')
            ->orderBy('purchased_at')
            ->get();
    }

    /** Pedidos cancelados que ya se habían cobrado: falta reembolsar al cliente. */
    private function pendingRefunds()
    {
        return Order::with('customer')
            ->where('status', Order::STATUS_CANCELADO)
            ->where('payment_status', Order::PAYMENT_STATUS_PAGADO)
            ->latest()
            ->get();
    }

    /** Gastos recurrentes (luz, agua, etc.) cuyo próximo pago ya venció. */
    private function dueExpenses()
    {
        return Expense::where('type', Expense::TYPE_GASTO)
            ->whereNotNull('frequency')
            ->get()
            ->groupBy('description')
            ->map(fn ($group) => $group->sortByDesc('incurred_on')->first())
            ->map(function (Expense $expense) {
                $expense->next_due_on = $this->nextDueDate($expense);

                return $expense;
            })
            ->filter(fn (Expense $expense) => $expense->next_due_on && now()->greaterThanOrEqualTo($expense->next_due_on))
            ->sortBy('next_due_on')
            ->values();
    }

    private function nextDueDate(Expense $expense): ?Carbon
    {
        $base = $expense->incurred_on->copy();

        return match ($expense->frequency) {
            'dia' => $base->addDay(),
            'semana' => $base->addWeek(),
            'quincena' => $base->addDays(15),
            'mes' => $base->addMonth(),
            'trimestre' => $base->addMonths(3),
            'semestre' => $base->addMonths(6),
            'anio' => $base->addYear(),
            default => null,
        };
    }
}
