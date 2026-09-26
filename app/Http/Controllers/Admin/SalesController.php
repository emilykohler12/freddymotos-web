<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->string('tab', 'web')->toString();
        $search = trim((string) $request->query('search', ''));
        $date = $request->query('date', '');
        $sort = $request->string('sort', 'recent')->toString();
        $shipment = $request->string('shipment', '')->toString();
        $payment = $request->string('payment', '')->toString();

        $baseQuery = Order::with('customer')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($date !== '', fn ($q) => $q->whereDate('created_at', $date))
            ->when($shipment === 'pending', fn ($q) => $q->where('status', Order::STATUS_PENDIENTE))
            ->when($payment === 'pending', fn ($q) => $q->where('payment_status', Order::PAYMENT_STATUS_PENDIENTE));

        if ($sort === 'name_asc') {
            $baseQuery->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->orderByRaw('LOWER(customers.name) ASC')
                ->select('orders.*');
        } elseif ($sort === 'name_desc') {
            $baseQuery->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->orderByRaw('LOWER(customers.name) DESC')
                ->select('orders.*');
        } elseif ($sort === 'oldest') {
            $baseQuery->oldest('created_at');
        } else {
            $baseQuery->latest('created_at');
        }

        $web = (clone $baseQuery)->where('origin', Order::ORIGIN_WEB)->paginate(15, ['*'], 'web_page')->withQueryString();
        $whatsapp = (clone $baseQuery)->where('origin', Order::ORIGIN_WHATSAPP)->paginate(15, ['*'], 'whatsapp_page')->withQueryString();
        $local = (clone $baseQuery)->where('origin', Order::ORIGIN_LOCAL)->paginate(15, ['*'], 'local_page')->withQueryString();

        $ingresosSearch = trim((string) $request->query('ingresos_search', ''));
        $ingresosSort = $request->string('ingresos_sort', 'recent')->toString();

        $paidOrdersQuery = Order::with('customer')
            ->where('payment_status', Order::PAYMENT_STATUS_PAGADO)
            ->when($ingresosSearch !== '', function ($q) use ($ingresosSearch) {
                $q->where(function ($q) use ($ingresosSearch) {
                    $q->where('id', 'like', "%{$ingresosSearch}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$ingresosSearch}%"));
                });
            })
            ->when($date !== '', fn ($q) => $q->whereDate('created_at', $date));

        if ($ingresosSort === 'name_asc') {
            $paidOrdersQuery->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->orderByRaw('LOWER(customers.name) ASC')
                ->select('orders.*');
        } elseif ($ingresosSort === 'name_desc') {
            $paidOrdersQuery->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->orderByRaw('LOWER(customers.name) DESC')
                ->select('orders.*');
        } elseif ($ingresosSort === 'oldest') {
            $paidOrdersQuery->oldest('paid_at');
        } else {
            $paidOrdersQuery->latest('paid_at');
        }

        $paidOrders = $paidOrdersQuery->paginate(15, ['*'], 'ingresos_page')->withQueryString();

        $otherIncome = Expense::where('type', Expense::TYPE_INGRESO)
            ->when($date !== '', fn ($q) => $q->whereDate('incurred_on', $date))
            ->latest('incurred_on')
            ->paginate(15, ['*'], 'otros_ingresos_page')
            ->withQueryString();

        return view('admin.sales.index', [
            'activeTab' => $activeTab,
            'web' => $web,
            'whatsapp' => $whatsapp,
            'local' => $local,
            'paidOrders' => $paidOrders,
            'otherIncome' => $otherIncome,
            'search' => $search,
            'date' => $date,
            'sort' => $sort,
            'shipment' => $shipment,
            'payment' => $payment,
            'ingresosSearch' => $ingresosSearch,
            'ingresosSort' => $ingresosSort,
        ]);
    }
}
