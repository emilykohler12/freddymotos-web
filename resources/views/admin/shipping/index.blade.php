@extends('layouts.admin')

@section('title', 'Envíos')
@section('page-heading', 'Envíos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-marca-amarillo/15 px-4 py-3 text-sm font-medium text-marca-negro">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Empresas de envío --}}
        <div>
            <h2 class="mb-3 text-sm font-bold text-marca-negro">Empresas de envío</h2>

            <form method="POST" action="{{ route('admin.shipping.companies.store') }}" class="mb-4 space-y-3 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @csrf
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Nombre de la empresa" required class="{{ $field }}">
                    <input type="text" name="service_type" placeholder="Tipo de servicio" class="{{ $field }}">
                    <input type="text" name="contact_phone" placeholder="Teléfono de contacto" class="{{ $field }}">
                    <input type="number" step="0.01" min="0" name="price" placeholder="Precio base" required class="{{ $field }}">
                </div>
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    + Añadir empresa
                </button>
            </form>

            <div class="divide-y divide-marca-gris-claro rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @forelse ($companies as $company)
                    <details class="group px-5 py-3">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                            <span class="font-medium text-marca-negro">{{ $company->name }}</span>
                            <span class="flex items-center gap-2">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $company->active ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-gris-claro text-marca-gris-oscuro' }}">{{ $company->active ? 'Activa' : 'Inactiva' }}</span>
                                <span class="text-marca-gris-oscuro">{{ $money($company->price) }}</span>
                            </span>
                        </summary>
                        <form method="POST" action="{{ route('admin.shipping.companies.update', $company) }}" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $company->name }}" required class="{{ $field }}">
                            <input type="text" name="service_type" value="{{ $company->service_type }}" placeholder="Tipo de servicio" class="{{ $field }}">
                            <input type="text" name="contact_phone" value="{{ $company->contact_phone }}" placeholder="Teléfono" class="{{ $field }}">
                            <input type="number" step="0.01" min="0" name="price" value="{{ $company->price }}" class="{{ $field }}">
                            <label class="flex items-center gap-2 text-sm text-marca-negro sm:col-span-2">
                                <input type="checkbox" name="active" value="1" @checked($company->active) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo">
                                Activa
                            </label>
                            <div class="flex gap-2 sm:col-span-2">
                                <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Guardar</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.shipping.companies.destroy', $company) }}" onsubmit="return confirm('¿Eliminar {{ $company->name }}?');" class="mt-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg border border-marca-rojo/30 px-4 py-2 text-xs font-semibold text-marca-rojo hover:bg-marca-rojo hover:text-marca-blanco">Eliminar</button>
                        </form>
                    </details>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-marca-gris-oscuro">Todavía no hay empresas de envío.</p>
                @endforelse
            </div>
        </div>

        {{-- Zonas de envío --}}
        <div>
            <h2 class="mb-3 text-sm font-bold text-marca-negro">Zonas de envío</h2>

            <form method="POST" action="{{ route('admin.shipping.zones.store') }}" class="mb-4 space-y-3 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @csrf
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <input type="text" name="name" placeholder="Nombre de la zona" required class="{{ $field }}">
                    <input type="text" name="postal_code" placeholder="Código postal" class="{{ $field }}">
                    <input type="text" name="locations" placeholder="Provincias / localidades" class="sm:col-span-2 {{ $field }}">
                    <select name="shipping_company_id" class="{{ $field }}">
                        <option value="">Empresa de envío…</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" min="0" name="price" placeholder="Precio" required class="{{ $field }}">
                </div>
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    + Añadir zona
                </button>
            </form>

            <div class="divide-y divide-marca-gris-claro rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @forelse ($zones as $zone)
                    <details class="group px-5 py-3">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                            <span class="font-medium text-marca-negro">{{ $zone->name }}</span>
                            <span class="text-marca-gris-oscuro">{{ $money($zone->price) }}</span>
                        </summary>
                        <p class="mt-2 text-xs text-marca-gris-oscuro">{{ $zone->locations }} @if($zone->postal_code) · CP {{ $zone->postal_code }} @endif @if($zone->company) · {{ $zone->company->name }} @endif</p>
                        <form method="POST" action="{{ route('admin.shipping.zones.update', $zone) }}" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $zone->name }}" required class="{{ $field }}">
                            <input type="text" name="postal_code" value="{{ $zone->postal_code }}" class="{{ $field }}">
                            <input type="text" name="locations" value="{{ $zone->locations }}" class="sm:col-span-2 {{ $field }}">
                            <select name="shipping_company_id" class="{{ $field }}">
                                <option value="">Sin empresa</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" @selected($zone->shipping_company_id === $company->id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" min="0" name="price" value="{{ $zone->price }}" class="{{ $field }}">
                            <div class="flex gap-2 sm:col-span-2">
                                <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Guardar</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.shipping.zones.destroy', $zone) }}" onsubmit="return confirm('¿Eliminar {{ $zone->name }}?');" class="mt-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-lg border border-marca-rojo/30 px-4 py-2 text-xs font-semibold text-marca-rojo hover:bg-marca-rojo hover:text-marca-blanco">Eliminar</button>
                        </form>
                    </details>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-marca-gris-oscuro">Todavía no hay zonas de envío.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
