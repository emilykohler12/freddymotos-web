<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->withCount('orders')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.form', ['customer' => new Customer()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->validated($request));

        return redirect()->route('admin.customers.index')->with('status', 'Cliente creado.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders' => fn ($q) => $q->latest()]);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request, $customer));

        return redirect()->route('admin.customers.index')->with('status', 'Cliente actualizado.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'No se puede eliminar: el cliente tiene pedidos.');
        }

        $customer->delete();

        return back()->with('status', 'Cliente eliminado.');
    }

    private function validated(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:customers,name' . ($customer ? ",{$customer->id}" : '')],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'email' => ['nullable', 'email', 'max:160', 'unique:customers,email' . ($customer ? ",{$customer->id}" : '')],
            'dni_cuit' => ['nullable', 'string', 'max:30', 'regex:/^[0-9-]{6,20}$/'],
            'address' => ['nullable', 'string', 'max:200'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20', 'regex:/^[0-9A-Za-z-\s]{3,20}$/'],
        ], [
            'name.unique' => 'Ya existe un cliente con ese nombre.',
            'email.unique' => 'Ya existe un cliente con ese correo.',
        ]);
    }
}
