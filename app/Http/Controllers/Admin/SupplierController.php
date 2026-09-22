<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort', 'name_asc');

        $suppliers = Supplier::withCount('products')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy('name', $sort === 'name_desc' ? 'desc' : 'asc')
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

        return view('admin.suppliers.show', compact('supplier'));
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

    /** Registrar una compra (para el historial y el cálculo de deuda). */
    public function storePurchase(Request $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'purchased_at' => ['required', 'date'],
        ]);

        $supplier->purchases()->create($data + ['paid_amount' => $data['paid_amount'] ?? 0]);

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
