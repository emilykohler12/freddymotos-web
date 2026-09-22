<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $sort = $request->query('sort', 'name_asc');
        $search = trim((string) $request->query('search', ''));

        $categories = Category::withCount('products')
            ->with('attributes')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc')
            ->get();

        return view('admin.categories.index', compact('categories', 'sort', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'image_path' => $request->hasFile('image') ? $request->file('image')->store('categories', 'public') : null,
        ]);

        return back()->with('status', 'Categoría creada.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name,' . $category->id],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'name.unique' => 'Ya existe una categoría con ese nombre.',
        ]);

        $update = ['name' => $data['name'], 'slug' => Str::slug($data['name'])];

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $update['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($update);

        return back()->with('status', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos usando esta categoría.');
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return back()->with('status', 'Categoría eliminada.');
    }
}
