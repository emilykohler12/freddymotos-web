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
use App\Models\WorkshopInquiry;
use App\Support\Sorting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logsRead = $request->query('logs_read', '');
        $logsSort = $request->query('logs_sort', 'recent');

        $logs = ActivityLog::with('user')
            ->when($logsRead === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($logsRead === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->when($logsSort === 'recent', fn ($q) => $q->latest())
            ->when($logsSort === 'oldest', fn ($q) => $q->oldest())
            ->when($logsSort === 'text_asc', fn ($q) => $q->orderByRaw(Sorting::foldedName('description') . ' ASC'))
            ->when($logsSort === 'text_desc', fn ($q) => $q->orderByRaw(Sorting::foldedName('description') . ' DESC'))
            ->paginate(30)
            ->withQueryString();

        $openOrders = Order::with('customer')
            ->whereNotIn('status', [Order::STATUS_ENTREGADO, Order::STATUS_CANCELADO])
            ->latest()
            ->take(20)
            ->get();

        $ingresosSearch = trim((string) $request->query('ingresos_search', ''));
        $ingresosSort = $request->query('ingresos_sort', 'recent');

        $ingresoCategories = ExpenseCategory::where('type', Expense::TYPE_INGRESO)->orderByRaw(Sorting::foldedName('name'))->get();

        $otrosIngresos = Expense::with('expenseCategory')
            ->where('type', Expense::TYPE_INGRESO)
            ->when($ingresosSearch !== '', fn ($q) => $q->whereRaw(Sorting::foldedName('description') . ' LIKE ?', ['%' . Sorting::fold($ingresosSearch) . '%']))
            ->when($ingresosSort === 'recent', fn ($q) => $q->orderByDesc('incurred_on'))
            ->when($ingresosSort === 'oldest', fn ($q) => $q->orderBy('incurred_on'))
            ->when($ingresosSort === 'text_asc', fn ($q) => $q->orderByRaw(Sorting::foldedName('description') . ' ASC'))
            ->when($ingresosSort === 'text_desc', fn ($q) => $q->orderByRaw(Sorting::foldedName('description') . ' DESC'))
            ->when($ingresosSort === 'price_asc', fn ($q) => $q->orderBy('amount'))
            ->when($ingresosSort === 'price_desc', fn ($q) => $q->orderByDesc('amount'))
            ->get();

        return view('admin.activity.index', [
            'activeTab' => $request->string('tab', 'notificaciones')->toString(),
            'logs' => $logs,
            'logsRead' => $logsRead,
            'logsSort' => $logsSort,
            'whatsappOrders' => $openOrders->where('origin', Order::ORIGIN_WHATSAPP)->values(),
            'webOrders' => $openOrders->where('origin', Order::ORIGIN_WEB)->values(),
            'orderStatuses' => [
                Order::STATUS_PENDIENTE => 'Pendiente',
                Order::STATUS_ENVIADO => 'Enviado',
                Order::STATUS_ENTREGADO => 'Entregado',
                Order::STATUS_CANCELADO => 'Cancelado',
            ],
            'newInquiries' => WorkshopInquiry::where('status', WorkshopInquiry::STATUS_NUEVA)->latest()->get(),
            'inquiries' => WorkshopInquiry::latest()->paginate(15)->withQueryString(),
            'lowStock' => Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get(),
            'outOfStock' => Product::where('active', true)->where('stock', '<=', 0)->get(),
            'pendingSupplierPayments' => $this->pendingSupplierPayments(),
            'pendingRefunds' => $this->pendingRefunds(),
            'dueExpenses' => Expense::due(),
            'ingresoCategories' => $ingresoCategories,
            'otrosIngresos' => $otrosIngresos,
            'otrosIngresosByCategory' => $ingresoCategories->map(fn (ExpenseCategory $category) => [
                'category' => $category,
                'items' => $otrosIngresos->where('expense_category_id', $category->id)->values(),
            ]),
            'otrosIngresosWithoutCategory' => $otrosIngresos->whereNull('expense_category_id')->values(),
            'ingresosSearch' => $ingresosSearch,
            'ingresosSort' => $ingresosSort,
            'inventoryProducts' => Product::orderBy('name')->get(['id', 'name', 'stock']),
            'stockReasons' => StockMovement::REASONS,
            'stockMovements' => StockMovement::with(['product', 'user'])->latest()->take(20)->get(),
        ]);
    }

    public function toggleRead(ActivityLog $log): RedirectResponse
    {
        $log->update(['read_at' => $log->read_at ? null : now()]);

        return back();
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
}
