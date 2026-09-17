@extends('layouts.admin')

@section('title', $supplier->exists ? 'Editar proveedor' : 'Nuevo proveedor')
@section('page-heading', $supplier->exists ? 'Editar proveedor' : 'Nuevo proveedor')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
    @endphp

    <div>
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}"
              class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            @if ($supplier->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="name" class="{{ $lbl }}">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required class="{{ $field }}">
                </div>
                <div>
                    <label for="company" class="{{ $lbl }}">Empresa</label>
                    <input type="text" id="company" name="company" value="{{ old('company', $supplier->company) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="cuit" class="{{ $lbl }}">CUIT</label>
                    <input type="text" id="cuit" name="cuit" value="{{ old('cuit', $supplier->cuit) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="phone" class="{{ $lbl }}">Teléfono</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="email" class="{{ $lbl }}">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="address" class="{{ $lbl }}">Dirección</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $supplier->address) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="payment_terms" class="{{ $lbl }}">Condiciones de pago</label>
                    <textarea id="payment_terms" name="payment_terms" rows="2" class="{{ $field }}">{{ old('payment_terms', $supplier->payment_terms) }}</textarea>
                </div>
                <div>
                    <label for="notes" class="{{ $lbl }}">Observaciones</label>
                    <textarea id="notes" name="notes" rows="2" class="{{ $field }}">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    {{ $supplier->exists ? 'Guardar cambios' : 'Crear proveedor' }}
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
