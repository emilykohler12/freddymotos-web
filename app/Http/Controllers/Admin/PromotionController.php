<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'recent');
        $status = $request->query('status', '');
        $type = $request->query('type', '');

        $promotions = Promotion::with(['category', 'products'])
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status === 'active', fn ($q) => $q->where('active', true))
            ->when($status === 'inactive', fn ($q) => $q->where('active', false))
            ->when($type !== '', fn ($q) => $q->where('type', $type))
            ->when($sort === 'title_asc', fn ($q) => $q->orderBy('title'))
            ->when($sort === 'title_desc', fn ($q) => $q->orderByDesc('title'))
            ->when($sort === 'recent', fn ($q) => $q->latest())
            ->get();

        return view('admin.promotions.index', compact('promotions', 'search', 'sort', 'status', 'type'));
    }

    public function create(): View
    {
        return $this->formView(new Promotion());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $promotion = Promotion::create($data);
        $promotion->products()->sync($data['scope'] === Promotion::SCOPE_PRODUCTS ? ($request->input('product_ids') ?? []) : []);

        return redirect()->route('admin.promotions.index')->with('status', 'Promoción creada.');
    }

    public function edit(Promotion $promotion): View
    {
        return $this->formView($promotion);
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $this->validated($request);

        $promotion->update($data);
        $promotion->products()->sync($data['scope'] === Promotion::SCOPE_PRODUCTS ? ($request->input('product_ids') ?? []) : []);

        return redirect()->route('admin.promotions.index')->with('status', 'Promoción actualizada.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return back()->with('status', 'Promoción eliminada.');
    }

    private function formView(Promotion $promotion): View
    {
        return view('admin.promotions.form', [
            'promotion' => $promotion,
            'categories' => Category::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        $promotion = $request->route('promotion');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120', 'unique:promotions,title' . ($promotion ? ",{$promotion->id}" : '')],
            'type' => ['required', 'in:percentage,fixed,nxm'],
            'value' => ['nullable', 'numeric', 'min:0', 'required_if:type,percentage,fixed'],
            'buy_quantity' => ['nullable', 'integer', 'min:2', 'required_if:type,nxm'],
            'pay_quantity' => ['nullable', 'integer', 'min:1', 'required_if:type,nxm'],
            'scope' => ['required', 'in:all,category,products'],
            'category_id' => ['nullable', 'exists:categories,id', 'required_if:scope,category'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['exists:products,id'],
            'active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ], [
            'title.unique' => 'Ya existe una promoción con ese nombre.',
        ]);

        $data['active'] = $request->boolean('active');

        if ($data['scope'] !== Promotion::SCOPE_CATEGORY) {
            $data['category_id'] = null;
        }

        unset($data['product_ids']);

        return $data;
    }
}
