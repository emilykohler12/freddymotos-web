<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('type')->toString() ?: Expense::TYPE_GASTO;

        $items = Expense::with('expenseCategory')
            ->where('type', $type)
            ->orderByDesc('incurred_on')
            ->get();

        $totalThisMonth = $items
            ->filter(fn (Expense $e) => $e->incurred_on->isSameMonth(now()))
            ->sum('amount');

        return view('admin.expenses.index', [
            'items' => $items,
            'type' => $type,
            'totalThisMonth' => $totalThisMonth,
            'categories' => ExpenseCategory::where('type', $type)->orderBy('name')->get(),
            'frequencies' => Expense::FREQUENCIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $expense = Expense::create($data);

        ActivityLog::log($data['type'], ucfirst($data['type']) . " registrado: {$expense->description}", $expense);

        return back()->with('status', ucfirst($data['type']) . ' registrado.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update($this->validated($request));

        ActivityLog::log($expense->type, ucfirst($expense->type) . " editado: {$expense->description}", $expense);

        return back()->with('status', 'Registro actualizado.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        ActivityLog::log($expense->type, ucfirst($expense->type) . " eliminado: {$expense->description}");

        return back()->with('status', 'Registro eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:gasto,ingreso'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['nullable', 'in:unica,mensual,anual'],
            'incurred_on' => ['required', 'date'],
        ]);
    }
}
