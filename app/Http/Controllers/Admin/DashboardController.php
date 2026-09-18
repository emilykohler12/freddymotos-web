<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const PERIODS = [
        'dia' => 'Hoy',
        'semana' => 'Esta semana',
        'mes' => 'Este mes',
        'anio' => 'Este año',
    ];

    public function index(Request $request): View
    {
        $period = $request->string('period')->toString();
        $period = array_key_exists($period, self::PERIODS) ? $period : 'mes';
        $start = $this->periodStart($period);

        $paidOrdersInPeriod = fn () => Order::query()->paid()->where('created_at', '>=', $start);

        // ---- KPIs ----
        $productsSold = (int) OrderItem::query()
            ->whereIn('order_id', $paidOrdersInPeriod()->pluck('id'))
            ->sum('quantity');

        $otrosIngresosTotal = (float) Expense::query()
            ->where('type', Expense::TYPE_INGRESO)
            ->where('incurred_on', '>=', $start)
            ->sum('amount');

        $revenue = (float) $paidOrdersInPeriod()->sum('total') + $otrosIngresosTotal;

        $shippedAndPaidCount = Order::query()
            ->paid()
            ->whereIn('status', [Order::STATUS_ENVIADO, Order::STATUS_ENTREGADO])
            ->where('created_at', '>=', $start)
            ->count();

        $expensesTotal = (float) Expense::query()
            ->where('type', Expense::TYPE_GASTO)
            ->where('incurred_on', '>=', $start)
            ->sum('amount');

        // ---- Gráficos ----
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty')
            ->whereIn('order_id', $paidOrdersInPeriod()->pluck('id'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();
        $topProductsMax = max(1, (int) $topProducts->max('total_qty'));

        $lowStock = Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get();
        $outOfStock = Product::where('active', true)->where('stock', '<=', 0)->get();

        $pendingOrders = Order::with('customer')
            ->where('status', Order::STATUS_PENDIENTE)
            ->where('created_at', '>=', $start)
            ->latest()
            ->get();

        $pendingPayments = Order::with(['customer', 'items'])
            ->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)
            ->where('created_at', '>=', $start)
            ->latest()
            ->get();

        $latestOrders = Order::with('customer')
            ->where('created_at', '>=', $start)
            ->latest()
            ->take(8)
            ->get();

        $expensesByCategory = Expense::with('expenseCategory')
            ->where('type', Expense::TYPE_GASTO)
            ->where('incurred_on', '>=', $start)
            ->get()
            ->groupBy(fn (Expense $e) => $e->expenseCategory->name ?? 'Sin categoría')
            ->map(fn ($group) => (float) $group->sum('amount'))
            ->sortDesc();
        $expensesByCategoryMax = max(1, (float) $expensesByCategory->max());

        return view('admin.dashboard', [
            'periods' => self::PERIODS,
            'period' => $period,
            'productsSold' => $productsSold,
            'revenue' => $revenue,
            'shippedAndPaidCount' => $shippedAndPaidCount,
            'expensesTotal' => $expensesTotal,
            'topProducts' => $topProducts,
            'topProductsMax' => $topProductsMax,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'pendingOrders' => $pendingOrders,
            'pendingPayments' => $pendingPayments,
            'latestOrders' => $latestOrders,
            'expensesByCategory' => $expensesByCategory,
            'expensesByCategoryMax' => $expensesByCategoryMax,
        ]);
    }

    private function periodStart(string $period): Carbon
    {
        return match ($period) {
            'dia' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'anio' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }
}
