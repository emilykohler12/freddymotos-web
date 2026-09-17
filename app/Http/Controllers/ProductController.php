<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /** Catálogo: /productos */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'brand', 'min', 'max']);

        $products = Product::query()
            ->where('active', true)
            ->filter($filters)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Product::categories(),
            'brands' => Product::brands(),
            'filters' => $filters,
        ]);
    }

    /** Detalle: /producto/{slug} */
    public function show(Product $product): View
    {
        $related = Product::query()
            ->where('category', $product->category)
            ->where('active', true)
            ->whereKeyNot($product->getKey())
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
