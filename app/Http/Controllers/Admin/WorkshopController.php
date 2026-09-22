<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mechanic;
use App\Models\Product;
use App\Models\WorkshopCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(Request $request): View
    {
        $categorySearch = trim((string) $request->query('category_search', ''));
        $mechanicSearch = trim((string) $request->query('mechanic_search', ''));

        // Listas completas (sin filtrar), para los selects de los formularios.
        $allCategories = WorkshopCategory::orderBy('name')->get();
        $allMechanics = Mechanic::orderBy('name')->get();

        return view('admin.workshop.index', [
            'activeTab' => $request->string('tab', 'categorias')->toString(),
            'categories' => $categorySearch !== ''
                ? $allCategories->filter(fn (WorkshopCategory $c) => str_contains(strtolower($c->name), strtolower($categorySearch)))->values()
                : $allCategories,
            'mechanics' => Mechanic::with(['jobs' => fn ($q) => $q->with('product')->latest()])
                ->when($mechanicSearch !== '', fn ($q) => $q->where('name', 'like', "%{$mechanicSearch}%"))
                ->orderBy('name')
                ->get(),
            'allMechanics' => $allMechanics,
            'products' => Product::orderBy('name')->get(['id', 'name', 'price']),
            'categorySearch' => $categorySearch,
            'mechanicSearch' => $mechanicSearch,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        WorkshopCategory::create($data);

        return redirect()->route('admin.workshop.index', ['tab' => 'categorias'])->with('status', 'Categoría creada.');
    }

    public function destroyCategory(WorkshopCategory $workshopCategory): RedirectResponse
    {
        $workshopCategory->delete();

        return redirect()->route('admin.workshop.index', ['tab' => 'categorias'])->with('status', 'Categoría eliminada.');
    }
}
