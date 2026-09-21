<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
    public function store(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
        ]);

        $category->attributes()->create($data);

        return back()->with('status', 'Detalle añadido a ' . $category->name . '.');
    }

    public function destroy(Category $category, CategoryAttribute $attribute): RedirectResponse
    {
        abort_unless($attribute->category_id === $category->id, 404);

        $attribute->delete();

        return back()->with('status', 'Detalle eliminado.');
    }
}
