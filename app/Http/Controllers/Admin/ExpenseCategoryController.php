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

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:80'],
            'parent_id' => ['sometimes', 'nullable', 'exists:expense_categories,id'],
        ]);

        if (($data['parent_id'] ?? null) == $expenseCategory->id) {
            $message = 'Una categoría no puede ser su propia subcategoría.';

            return $request->wantsJson()
                ? response()->json(['error' => $message], 422)
                : back()->with('error', $message);
        }

        $expenseCategory->update($data);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'Categoría actualizada.']);
        }

        return $this->redirectFor($expenseCategory->type)->with('status', 'Categoría actualizada.');
    }

    public function destroy(Request $request, ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->exists()) {
            $message = 'No se puede eliminar: hay registros usando esta categoría.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $message], 400);
            }
            return $this->redirectFor($expenseCategory->type)->with('error', $message);
        }

        $type = $expenseCategory->type;
        $expenseCategory->delete();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'Categoría desactivada.']);
        }

        return $this->redirectFor($type)->with('status', 'Categoría eliminada.');
    }

    private function redirectFor(string $type): RedirectResponse
    {
        return $type === ExpenseCategory::TYPE_INGRESO
            ? redirect()->route('admin.activity.index', ['tab' => 'ingresos'])
            : redirect()->route('admin.expenses.index');
    }
}
