<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const SORTS = [
        'name_asc' => ['name', 'asc'],
        'name_desc' => ['name', 'desc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'stock' => ['stock', 'asc'],
        'category' => ['category', 'asc'],
        'brand' => ['brand', 'asc'],
    ];

    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        [$column, $direction] = self::SORTS[$sort] ?? self::SORTS['name_asc'];

        $products = Product::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->orderBy($column, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'sort' => $sort ?: 'name_asc',
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::with('attributes')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'attributeValues' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        // El stock inicial se carga aparte desde Movimientos → Inventario.
        $data['stock'] = 0;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        $this->syncAttributes($product, $data['category_id'], $request->input('attributes', []));

        return redirect()->route('admin.products.index')->with('status', 'Producto creado.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::with('attributes')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'attributeValues' => $product->attributeValues->pluck('value', 'category_attribute_id'),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        $this->syncAttributes($product, $data['category_id'], $request->input('attributes', []));

        return redirect()->route('admin.products.index')->with('status', 'Producto actualizado.');
    }

    /** Guarda solo los detalles (clasificadores) que el admin completó para la categoría elegida. */
    private function syncAttributes(Product $product, int $categoryId, array $values): void
    {
        $attributeIds = CategoryAttribute::where('category_id', $categoryId)->pluck('id');

        foreach ($attributeIds as $attributeId) {
            $value = trim((string) ($values[$attributeId] ?? ''));

            if ($value === '') {
                ProductAttributeValue::where('product_id', $product->id)
                    ->where('category_attribute_id', $attributeId)
                    ->delete();

                continue;
            }

            ProductAttributeValue::updateOrCreate(
                ['product_id' => $product->id, 'category_attribute_id' => $attributeId],
                ['value' => $value],
            );
        }

        // Detalles que no pertenecen a la categoría actual del producto no se guardan.
        ProductAttributeValue::where('product_id', $product->id)
            ->whereNotIn('category_attribute_id', $attributeIds)
            ->delete();
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return back()->with('status', 'Producto eliminado.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:60', 'unique:products,sku' . ($product ? ",{$product->id}" : '')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand' => ['required', 'string', 'max:80'],
            'compatible_model' => ['nullable', 'string', 'max:2000'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'active' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['nullable', 'string', 'max:255'],
        ]);

        unset($data['attributes']);

        $data['active'] = $request->boolean('active');

        unset($data['image']);

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-" . ++$i;
        }

        return $slug;
    }
}
