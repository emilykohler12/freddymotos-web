<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPurchase;
use App\Support\Sorting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'name_asc');

        $suppliers = Supplier::withCount('products')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(fn ($q) => $q->whereRaw(Sorting::foldedName('name') . ' LIKE ?', ['%' . Sorting::fold($search) . '%'])
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderByRaw(Sorting::foldedName('name') . ($sort === 'name_desc' ? ' DESC' : ' ASC'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers', 'search', 'sort'));
    }

    public function create(): View
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return redirect()->route('admin.suppliers.index')->with('status', 'Proveedor creado.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchases' => fn ($q) => $q->latest('purchased_at'), 'products']);

        return view('admin.suppliers.show', [
            'supplier' => $supplier,
            'allProducts' => Product::orderBy('name')->get(),
            'purchaseStatuses' => SupplierPurchase::STATUSES,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request, $supplier));

        return redirect()->route('admin.suppliers.index')->with('status', 'Proveedor actualizado.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos con este proveedor.');
        }

        $supplier->delete();

        return back()->with('status', 'Proveedor eliminado.');
    }

    /**
     * Registrar una compra: elige un repuesto del catálogo y una cantidad, calcula el
     * monto según el costo cargado, y suma el stock recibido (salvo que se cancele).
     */
    public function storePurchase(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:' . implode(',', array_keys(SupplierPurchase::STATUSES))],
            'purchased_at' => ['required', 'date'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $amount = (float) $product->cost_price * $data['quantity'];

        DB::transaction(function () use ($supplier, $data, $product, $amount) {
            $supplier->purchases()->create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'description' => "{$product->name} x{$data['quantity']}",
                'amount' => $amount,
                'status' => $data['status'],
                'purchased_at' => $data['purchased_at'],
            ]);

            if ($data['status'] !== SupplierPurchase::STATUS_CANCELADO) {
                $product->increment('stock', $data['quantity']);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth('web')->id(),
                    'reason' => StockMovement::REASON_COMPRA,
                    'quantity_change' => $data['quantity'],
                    'note' => "Compra a proveedor: {$supplier->name}",
                ]);
            }
        });

        return back()->with('status', 'Compra registrada.');
    }

    private function validated(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:suppliers,name' . ($supplier ? ",{$supplier->id}" : '')],
            'company' => ['nullable', 'string', 'max:150'],
            'cuit' => ['nullable', 'string', 'max:30', 'regex:/^[0-9-]{6,20}$/'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'email' => ['nullable', 'email', 'max:160', 'unique:suppliers,email' . ($supplier ? ",{$supplier->id}" : '')],
            'address' => ['nullable', 'string', 'max:200'],
            'payment_terms' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.unique' => 'Ya existe un proveedor con ese nombre.',
            'email.unique' => 'Ya existe un proveedor con ese correo.',
        ]);
    }
}
