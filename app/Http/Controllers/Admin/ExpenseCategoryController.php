<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'type' => ['required', 'in:gasto,ingreso'],
            'parent_id' => ['nullable', 'exists:expense_categories,id'],
        ]);

        ExpenseCategory::create($data);

        return $this->redirectFor($data['type'])->with('status', 'Categoría creada.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'parent_id' => ['nullable', 'exists:expense_categories,id'],
        ]);

        $expenseCategory->update($data);

        return $this->redirectFor($expenseCategory->type)->with('status', 'Categoría actualizada.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->expenses()->exists()) {
            return $this->redirectFor($expenseCategory->type)->with('error', 'No se puede eliminar: hay registros usando esta categoría.');
        }

        $type = $expenseCategory->type;
        $expenseCategory->delete();

        return $this->redirectFor($type)->with('status', 'Categoría eliminada.');
    }

    private function redirectFor(string $type): RedirectResponse
    {
        return $type === ExpenseCategory::TYPE_INGRESO
            ? redirect()->route('admin.activity.index', ['tab' => 'ingresos'])
            : redirect()->route('admin.expenses.index');
    }
}
