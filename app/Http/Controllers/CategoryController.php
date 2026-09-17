<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /** Página pública con todas las categorías: /categorias */
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount(['products' => fn ($q) => $q->where('active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
