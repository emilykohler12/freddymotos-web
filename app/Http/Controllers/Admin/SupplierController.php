<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::withCount('products')->orderBy('name')->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $supplier = Supplier::create($this->validated($request));

        ActivityLog::log('proveedor', "Proveedor creado: {$supplier->name}", $supplier);

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
        $supplier->update($this->validated($request));

        ActivityLog::log('proveedor', "Proveedor editado: {$supplier->name}", $supplier);

        return redirect()->route('admin.suppliers.index')->with('status', 'Proveedor actualizado.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->products()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay productos con este proveedor.');
        }

        $name = $supplier->name;
        $supplier->delete();

        ActivityLog::log('proveedor', "Proveedor eliminado: {$name}");

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

        ActivityLog::log('proveedor', "Compra registrada a {$supplier->name} por \$ " . number_format($data['amount'], 0, ',', '.'), $supplier);

        return back()->with('status', 'Compra registrada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:150'],
            'cuit' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'address' => ['nullable', 'string', 'max:200'],
            'payment_terms' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
