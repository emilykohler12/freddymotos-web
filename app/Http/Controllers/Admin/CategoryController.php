<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Sorting;
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
            ->when($search !== '', fn ($q) => $q->whereRaw(Sorting::foldedName('name') . ' LIKE ?', ['%' . Sorting::fold($search) . '%']))
            ->orderByRaw(Sorting::foldedName('name') . ($sort === 'name_desc' ? ' DESC' : ' ASC'))
            ->get();

        return view('admin.categories.index', compact('categories', 'sort', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->duplicateName($data['name'])) {
            return back()->with('error', 'Ya creaste esa categoría.')->withInput();
        }

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
            'name' => ['required', 'string', 'max:80'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($this->duplicateName($data['name'], $category->id)) {
            return back()->with('error', 'Ya creaste esa categoría.')->withInput();
        }

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

    /**
     * Compara ignorando mayúsculas/tildes (ej. "Discos de embrague" y "Discos
     * de Embrague" son la misma categoría) y también por slug: dos nombres
     * distintos pueden generar el mismo slug, y ese campo es único en la base,
     * así que sin este chequeo el guardado explota con un 500 en vez de avisar.
     */
    private function duplicateName(string $name, ?int $exceptId = null): bool
    {
        return Category::where(function ($q) use ($name) {
                $q->whereRaw(Sorting::foldedName('name') . ' = ?', [Sorting::fold($name)])
                    ->orWhere('slug', Str::slug($name));
            })
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }
}
