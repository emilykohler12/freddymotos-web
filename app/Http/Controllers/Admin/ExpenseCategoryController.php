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
        ]);

        ExpenseCategory::create($data);

        return back()->with('status', 'Categoría creada.');
    }

    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay registros usando esta categoría.');
        }

        $expenseCategory->delete();

        return back()->with('status', 'Categoría eliminada.');
    }
}
