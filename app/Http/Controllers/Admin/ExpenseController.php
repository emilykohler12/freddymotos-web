<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Support\Sorting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        // Esta pantalla es solo de Gastos. "Otros ingresos" se registra desde Movimientos.
        $frequency = $request->string('frequency')->toString();
        $search = trim((string) $request->query('search', ''));
        $categorySearch = trim((string) $request->query('category_search', ''));
        $categorySort = $request->query('category_sort', 'name_asc');

        $items = Expense::with('expenseCategory')
            ->where('type', Expense::TYPE_GASTO)
            ->when($frequency, fn ($q) => $q->where('frequency', $frequency))
            ->when($search !== '', fn ($q) => $q->whereRaw(Sorting::foldedName('description') . ' LIKE ?', ['%' . Sorting::fold($search) . '%']))
            ->orderByDesc('incurred_on')
            ->get();

        $categories = ExpenseCategory::where('type', Expense::TYPE_GASTO)->with('children')->orderByRaw(Sorting::foldedName('name'))->get();

        $categoryList = $categories
            ->when($categorySearch !== '', fn ($c) => $c->filter(fn (ExpenseCategory $cat) => str_contains(Sorting::fold($cat->name), Sorting::fold($categorySearch))))
            ->sortBy(fn (ExpenseCategory $cat) => Sorting::fold($cat->name), SORT_STRING, $categorySort === 'name_desc')
            ->values();

        $groupByCategory = function (Collection $collection) use ($categories) {
            return $categories->map(fn (ExpenseCategory $category) => [
                'category' => $category,
                'items' => $collection->where('expense_category_id', $category->id)->values(),
            ]);
        };

        $pending = $items->where('paid', false)->values();
        $paid = $items->where('paid', true)->values();

        $totalThisMonth = $items
            ->filter(fn (Expense $e) => $e->incurred_on->isSameMonth(now()))
            ->sum('amount');

        return view('admin.expenses.index', [
            'activeTab' => $request->string('tab', 'categoria')->toString(),
            'allExpenses' => $items,
            'pendingByCategory' => $groupByCategory($pending),
            'pendingWithoutCategory' => $pending->whereNull('expense_category_id')->values(),
            'paidByCategory' => $groupByCategory($paid),
            'paidWithoutCategory' => $paid->whereNull('expense_category_id')->values(),
            'totalThisMonth' => $totalThisMonth,
            'categories' => $categories,
            'categoryList' => $categoryList,
            'categorySearch' => $categorySearch,
            'categorySort' => $categorySort,
            'frequencies' => Expense::FREQUENCIES,
            'frequency' => $frequency,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $tab] = $this->validated($request);

        Expense::create($data);

        return $this->redirectFor($tab)->with('status', ucfirst($data['type']) . ' registrado.');
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        [$data, $tab] = $this->validated($request);

        $expense->update($data);

        return $this->redirectFor($tab)->with('status', 'Registro actualizado.');
    }

    public function destroy(Request $request, Expense $expense): RedirectResponse
    {
        $type = $expense->type;
        $expense->delete();

        return $this->redirectFor($request->string('tab', $type === Expense::TYPE_INGRESO ? 'ingresos' : 'categoria')->toString())
            ->with('status', 'Registro eliminado.');
    }

    /** Pagos únicos/anuales quedan marcados como pagados y se archivan en "Pagadas". */
    public function togglePaid(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update(['paid' => ! $expense->paid]);

        return $this->redirectFor($request->string('tab', 'pendientes')->toString())
            ->with('status', $expense->paid ? 'Gasto marcado como pagado.' : 'Gasto reactivado.');
    }

    private function redirectFor(string $tab): RedirectResponse
    {
        return in_array($tab, ['ingresos'], true)
            ? redirect()->route('admin.activity.index', ['tab' => 'ingresos'])
            : redirect()->route('admin.expenses.index', ['tab' => in_array($tab, ['categoria', 'nuevo-gasto', 'pendientes', 'pagadas'], true) ? $tab : 'categoria']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:gasto,ingreso'],
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'frequency' => ['nullable', 'in:' . implode(',', array_keys(Expense::FREQUENCIES))],
            'incurred_on' => ['required', 'date'],
            'tab' => ['nullable', 'string'],
        ]);

        $tab = $data['tab'] ?? ($data['type'] === Expense::TYPE_INGRESO ? 'ingresos' : 'categoria');
        unset($data['tab']);

        return [$data, $tab];
    }
}
