<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with('user')->latest()->paginate(30);

        return view('admin.activity.index', [
            'logs' => $logs,
            'lowStock' => Product::where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->get(),
            'outOfStock' => Product::where('active', true)->where('stock', '<=', 0)->get(),
            'ingresoCategories' => ExpenseCategory::where('type', Expense::TYPE_INGRESO)->orderBy('name')->get(),
        ]);
    }
}
