<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        ActivityLog::log('categoria', "Categoría creada: {$category->name}", $category);

        return back()->with('status', 'Categoría creada.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $category->update(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);

        ActivityLog::log('categoria', "Categoría editada: {$category->name}", $category);

        return back()->with('status', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos usando esta categoría.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::log('categoria', "Categoría eliminada: {$name}");

        return back()->with('status', 'Categoría eliminada.');
    }
}
