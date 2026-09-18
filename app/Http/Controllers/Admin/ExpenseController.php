<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $active = $items->reject(fn (Expense $e) => $e->isDeactivated());
        $deactivated = $items->filter(fn (Expense $e) => $e->isDeactivated());

        $categories = ExpenseCategory::where('type', Expense::TYPE_GASTO)->orderBy('name')->get();

        $byCategory = $categories->map(fn (ExpenseCategory $category) => [
            'category' => $category,
            'items' => $active->where('expense_category_id', $category->id)->values(),
        ]);
        $withoutCategory = $active->whereNull('expense_category_id')->values();

        $totalThisMonth = $items
            ->filter(fn (Expense $e) => $e->incurred_on->isSameMonth(now()))
            ->sum('amount');

        return view('admin.expenses.index', [
            'byCategory' => $byCategory,
            'withoutCategory' => $withoutCategory,
            'deactivated' => $deactivated,
            'totalThisMonth' => $totalThisMonth,
            'categories' => $categories,
            'frequencies' => Expense::FREQUENCIES,
            'frequency' => $frequency,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Expense::create($data);

        return $this->redirectFor($data['type'])->with('status', ucfirst($data['type']) . ' registrado.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update($this->validated($request));

        return $this->redirectFor($expense->type)->with('status', 'Registro actualizado.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $type = $expense->type;
        $expense->delete();

        return $this->redirectFor($type)->with('status', 'Registro eliminado.');
    }

    /** Pagos únicos/anuales quedan marcados como pagados y se archivan en "Desactivados". */
    public function togglePaid(Expense $expense): RedirectResponse
    {
        $expense->update(['paid' => ! $expense->paid]);

        return $this->redirectFor($expense->type)->with('status', $expense->paid ? 'Gasto marcado como pagado.' : 'Gasto reactivado.');
    }

    private function redirectFor(string $type): RedirectResponse
    {
        return $type === Expense::TYPE_INGRESO
            ? redirect()->route('admin.activity.index', ['tab' => 'ingresos'])
            : redirect()->route('admin.expenses.index');
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
