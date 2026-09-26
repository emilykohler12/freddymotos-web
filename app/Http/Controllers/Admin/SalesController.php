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

        $baseQuery = Order::with('customer')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($date !== '', fn ($q) => $q->whereDate('created_at', $date));

        $web = (clone $baseQuery)->where('origin', Order::ORIGIN_WEB)->latest()->paginate(15, ['*'], 'web_page')->withQueryString();
        $whatsapp = (clone $baseQuery)->where('origin', Order::ORIGIN_WHATSAPP)->latest()->paginate(15, ['*'], 'whatsapp_page')->withQueryString();
        $local = collect();

        $paidOrders = Order::where('payment_status', Order::PAYMENT_STATUS_PAGADO)
            ->when($date !== '', fn ($q) => $q->whereDate('created_at', $date))
            ->latest('paid_at')
            ->paginate(15, ['*'], 'ingresos_page')
            ->withQueryString();

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
        ]);
    }
}
