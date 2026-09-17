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
        // Esta pantalla es solo de Gastos. "Otros ingresos" se registra desde Movimientos.
        $frequency = $request->string('frequency')->toString();

        $items = Expense::with('expenseCategory')
            ->where('type', Expense::TYPE_GASTO)
            ->when($frequency, fn ($q) => $q->where('frequency', $frequency))
            ->orderByDesc('incurred_on')
            ->get();

        $totalThisMonth = $items
            ->filter(fn (Expense $e) => $e->incurred_on->isSameMonth(now()))
            ->sum('amount');

        return view('admin.expenses.index', [
            'items' => $items,
            'totalThisMonth' => $totalThisMonth,
            'categories' => ExpenseCategory::where('type', Expense::TYPE_GASTO)->orderBy('name')->get(),
            'frequencies' => Expense::FREQUENCIES,
            'frequency' => $frequency,
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
            'frequency' => ['nullable', 'in:' . implode(',', array_keys(Expense::FREQUENCIES))],
            'incurred_on' => ['required', 'date'],
        ]);
    }
}
