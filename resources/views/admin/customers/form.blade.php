@extends('layouts.admin')

@section('title', $customer->exists ? 'Editar cliente' : 'Nuevo cliente')
@section('page-heading', $customer->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
    @endphp

    <div class="max-w-2xl">
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}"
              class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            @if ($customer->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="{{ $lbl }}">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required class="{{ $field }}">
                </div>
                <div>
                    <label for="phone" class="{{ $lbl }}">Teléfono</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required class="{{ $field }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="email" class="{{ $lbl }}">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="dni_cuit" class="{{ $lbl }}">DNI / CUIT</label>
                    <input type="text" id="dni_cuit" name="dni_cuit" value="{{ old('dni_cuit', $customer->dni_cuit) }}" class="{{ $field }}">
                </div>
            </div>

            <div>
                <label for="address" class="{{ $lbl }}">Dirección</label>
                <input type="text" id="address" name="address" value="{{ old('address', $customer->address) }}" class="{{ $field }}">
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label for="city" class="{{ $lbl }}">Ciudad</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="province" class="{{ $lbl }}">Provincia</label>
                    <input type="text" id="province" name="province" value="{{ old('province', $customer->province) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="postal_code" class="{{ $lbl }}">Código postal</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" class="{{ $field }}">
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    {{ $customer->exists ? 'Guardar cambios' : 'Crear cliente' }}
                </button>
                <a href="{{ route('admin.customers.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
