<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Mechanic;
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

    private const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

    private const DIAS = ['lun', 'mar', 'mié', 'jue', 'vie', 'sáb', 'dom'];

    private const PALETTE = ['#F5C518', '#D22F27', '#6E1423', '#BC7C1A', '#141414', '#2A2A2A'];

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

        // ---- Productos más vendidos ----
        $topProducts = OrderItem::query()
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty')
            ->whereIn('order_id', $paidOrdersInPeriod()->pluck('id'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        $lowStock = Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get();
        $outOfStock = Product::where('active', true)->where('stock', '<=', 0)->get();

        $pendingOrders = Order::with('customer')
            ->where('status', Order::STATUS_PENDIENTE)
            ->where('created_at', '>=', $start)
            ->latest()
            ->get();

        $pendingOrderPayments = Order::with(['customer', 'items'])
            ->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE)
            ->where('created_at', '>=', $start)
            ->latest()
            ->get();

        // Todos los gastos sin marcar como pagados (no solo los vencidos), agrupados por frecuencia.
        $pendingExpensePayments = Expense::where('type', Expense::TYPE_GASTO)
            ->where('paid', false)
            ->orderByDesc('incurred_on')
            ->get()
            ->groupBy(fn (Expense $e) => $e->frequency ?? 'unica');

        // ---- Gastos por categoría ----
        $expensesByCategory = Expense::with('expenseCategory')
            ->where('type', Expense::TYPE_GASTO)
            ->where('incurred_on', '>=', $start)
            ->get()
            ->groupBy(fn (Expense $e) => $e->expenseCategory->name ?? 'Sin categoría')
            ->map(fn ($group) => (float) $group->sum('amount'))
            ->sortDesc();

        // ---- Cuánto deben los mecánicos por productos que compraron y no pagaron ----
        $mechanicsDebt = Mechanic::with(['jobs' => fn ($q) => $q->where('pagado', false)])
            ->get()
            ->map(fn (Mechanic $m) => ['name' => $m->name, 'total' => (float) $m->jobs->sum('monto_a_pagar')])
            ->filter(fn ($m) => $m['total'] > 0)
            ->sortByDesc('total')
            ->values();

        return view('admin.dashboard', [
            'periods' => self::PERIODS,
            'period' => $period,
            'productsSold' => $productsSold,
            'revenue' => $revenue,
            'shippedAndPaidCount' => $shippedAndPaidCount,
            'expensesTotal' => $expensesTotal,
            'lowStock' => $lowStock,
            'outOfStock' => $outOfStock,
            'pendingOrders' => $pendingOrders,
            'pendingOrderPayments' => $pendingOrderPayments,
            'pendingExpensePayments' => $pendingExpensePayments,
            'topProductsChart' => $this->topProductsChart($topProducts),
            'expensesByCategoryChart' => $this->expensesByCategoryChart($expensesByCategory),
            'incomeVsExpensesChart' => $this->incomeVsExpensesChart($period, $start),
            'mechanicsDebtChart' => $this->mechanicsDebtChart($mechanicsDebt),
            'hasTopProducts' => $topProducts->isNotEmpty(),
            'hasExpensesByCategory' => $expensesByCategory->isNotEmpty(),
            'hasMechanicsDebt' => $mechanicsDebt->isNotEmpty(),
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

    private function topProductsChart($topProducts): array
    {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $topProducts->pluck('product_name')->all(),
                'datasets' => [[
                    'label' => 'Unidades vendidas',
                    'data' => $topProducts->pluck('total_qty')->all(),
                    'backgroundColor' => '#F5C518',
                    'borderRadius' => 6,
                    'maxBarThickness' => 28,
                ]],
            ],
            'options' => [
                'indexAxis' => 'y',
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
            ],
        ];
    }

    private function expensesByCategoryChart($expensesByCategory): array
    {
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $expensesByCategory->keys()->all(),
                'datasets' => [[
                    'data' => $expensesByCategory->values()->all(),
                    'backgroundColor' => $this->colors($expensesByCategory->count()),
                    'borderWidth' => 2,
                    'borderColor' => '#FFFFFF',
                ]],
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 12, 'padding' => 12]]],
            ],
        ];
    }

    /** Cuánto debe cada mecánico por productos que compró y todavía no pagó. */
    private function mechanicsDebtChart($mechanicsDebt): array
    {
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $mechanicsDebt->pluck('name')->all(),
                'datasets' => [[
                    'data' => $mechanicsDebt->pluck('total')->all(),
                    'backgroundColor' => $this->colors($mechanicsDebt->count()),
                    'borderWidth' => 2,
                    'borderColor' => '#FFFFFF',
                ]],
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 12, 'padding' => 12]]],
            ],
        ];
    }

    private function colors(int $count): array
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $colors[] = self::PALETTE[$i % count(self::PALETTE)];
        }

        return $colors;
    }

    /** Ingresos (pedidos pagos + otros ingresos) vs gastos, agrupados según el filtro de período activo. */
    private function incomeVsExpensesChart(string $period, Carbon $start): array
    {
        $buckets = $this->buckets($period, $start);

        $labels = [];
        $ingresos = [];
        $gastos = [];

        foreach ($buckets as $bucket) {
            $labels[] = $bucket['label'];

            $ingresos[] = round(
                (float) Order::query()->paid()->whereBetween('created_at', [$bucket['start'], $bucket['end']])->sum('total')
                + (float) Expense::where('type', Expense::TYPE_INGRESO)->whereBetween('incurred_on', [$bucket['start'], $bucket['end']])->sum('amount'),
                2
            );

            $gastos[] = round(
                (float) Expense::where('type', Expense::TYPE_GASTO)->whereBetween('incurred_on', [$bucket['start'], $bucket['end']])->sum('amount'),
                2
            );
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ingresos',
                        'data' => $ingresos,
                        'backgroundColor' => '#F5C518',
                        'borderRadius' => 6,
                        'maxBarThickness' => 48,
                    ],
                    [
                        'label' => 'Gastos',
                        'data' => $gastos,
                        'backgroundColor' => '#6E1423',
                        'borderRadius' => 6,
                        'maxBarThickness' => 48,
                    ],
                ],
            ],
            'options' => [
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'bottom']],
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];
    }

    /**
     * Tramos de tiempo para el gráfico de ingresos vs gastos, según la granularidad del período.
     *
     * @return array<int, array{label:string, start:Carbon, end:Carbon}>
     */
    private function buckets(string $period, Carbon $start): array
    {
        $buckets = [];

        switch ($period) {
            case 'dia':
                for ($h = 0; $h < 24; $h++) {
                    $hourStart = $start->copy()->addHours($h);
                    $buckets[] = ['label' => $hourStart->format('H:00'), 'start' => $hourStart, 'end' => $hourStart->copy()->addHour()->subSecond()];
                }
                break;

            case 'semana':
                for ($d = 0; $d < 7; $d++) {
                    $day = $start->copy()->addDays($d);
                    $buckets[] = ['label' => self::DIAS[$day->dayOfWeekIso - 1], 'start' => $day->copy()->startOfDay(), 'end' => $day->copy()->endOfDay()];
                }
                break;

            case 'anio':
                for ($m = 0; $m < 12; $m++) {
                    $month = $start->copy()->addMonths($m);
                    $buckets[] = ['label' => self::MESES[$month->month - 1], 'start' => $month->copy()->startOfMonth(), 'end' => $month->copy()->endOfMonth()];
                }
                break;

            default: // mes
                $daysInMonth = $start->daysInMonth;
                for ($d = 0; $d < $daysInMonth; $d++) {
                    $day = $start->copy()->addDays($d);
                    $buckets[] = ['label' => (string) $day->day, 'start' => $day->copy()->startOfDay(), 'end' => $day->copy()->endOfDay()];
                }
                break;
        }

        return $buckets;
    }
}
