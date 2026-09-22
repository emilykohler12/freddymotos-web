<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Support\Sorting;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /** Página pública con todas las categorías: /categorias */
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount(['products' => fn ($q) => $q->where('active', true)])
                ->whereHas('products', fn ($q) => $q->where('active', true))
                ->orderByRaw(Sorting::foldedName('name'))
                ->get(),
        ]);
    }
}
