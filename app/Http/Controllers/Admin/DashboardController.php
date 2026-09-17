<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $startOfMonth = now()->startOfMonth();

        $paidOrders = fn () => Order::query()->paid();

        // Ventas: cantidad de productos vendidos (items de pedidos pagos) este mes.
        $productsSoldThisMonth = (int) OrderItem::query()
            ->whereIn('order_id', $paidOrders()->where('created_at', '>=', $startOfMonth)->pluck('id'))
            ->sum('quantity');

        $revenueThisMonth = (float) $paidOrders()->where('created_at', '>=', $startOfMonth)->sum('total');

        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        $pendingOrdersCount = Order::where('status', Order::STATUS_PENDIENTE)->count();
        $shippedOrdersCount = Order::where('status', Order::STATUS_ENVIADO)->count();
        $pendingPaymentsCount = Order::where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)->count();

        $expensesThisMonth = (float) Expense::query()
            ->where('type', Expense::TYPE_GASTO)
            ->whereYear('incurred_on', now()->year)
            ->whereMonth('incurred_on', now()->month)
            ->sum('amount');

        // Gastos de los últimos 6 meses, para el gráfico.
        $expensesChart = collect(range(5, 0))->map(function (int $i) {
            $month = now()->subMonths($i);

            return [
                'label' => ucfirst($month->translatedFormat('M')),
                'total' => (float) Expense::query()
                    ->where('type', Expense::TYPE_GASTO)
                    ->whereYear('incurred_on', $month->year)
                    ->whereMonth('incurred_on', $month->month)
                    ->sum('amount'),
            ];
        });
        $expensesChartMax = max(1, $expensesChart->max('total'));

        $latestOrders = Order::with('customer')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'productsSoldThisMonth',
            'revenueThisMonth',
            'lowStockCount',
            'outOfStockCount',
            'pendingOrdersCount',
            'shippedOrdersCount',
            'pendingPaymentsCount',
            'expensesThisMonth',
            'expensesChart',
            'expensesChartMax',
            'latestOrders',
        ));
    }
}
