@extends('layouts.admin')

@section('title', 'Envíos')
@section('page-heading', 'Envíos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp


    {{-- Tabs: radios, labels y paneles como hermanos directos (así el CSS peer-checked --}}
    {{-- funciona tanto para resaltar el tab activo como para mostrar/ocultar el panel). --}}
    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="shipping-tab" id="tab-empresas" class="peer/empresas hidden" checked>
        <label for="tab-empresas" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/empresas:bg-marca-negro peer-checked/empresas:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v8H3V7zm11 3h4l3 3v2h-7m-7 2a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z"/></svg>
            Empresas
        </label>

        <input type="radio" name="shipping-tab" id="tab-zonas" class="peer/zonas hidden">
        <label for="tab-zonas" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/zonas:bg-marca-negro peer-checked/zonas:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11zm0-8a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
            Zonas
        </label>

    {{-- Empresas de envío --}}
    <div class="hidden w-full pt-4 peer-checked/empresas:block">
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

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($companies as $company)
                <details class="group rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
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
                        <button type="submit" class="w-full rounded-lg border border-marca-rojo/30 px-4 py-2 text-xs font-semibold text-marca-rojo hover:bg-marca-rojo hover:text-marca-blanco">Eliminar</button>
                    </form>
                </details>
            @empty
                <p class="col-span-full rounded-2xl bg-marca-blanco px-5 py-8 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">Todavía no hay empresas de envío.</p>
            @endforelse
        </div>
    </div>

    {{-- Zonas de envío --}}
    <div class="hidden w-full pt-4 peer-checked/zonas:block">
        <form method="POST" action="{{ route('admin.shipping.zones.store') }}" class="mb-4 space-y-3 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <input type="text" name="name" placeholder="Nombre de la zona" required class="{{ $field }}">
                <input type="text" name="postal_code" placeholder="Código postal" class="{{ $field }}">
                <input type="text" name="provincia" placeholder="Provincia" class="{{ $field }}">
                <input type="text" name="localidad" placeholder="Localidad" class="{{ $field }}">
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

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($zones as $zone)
                <details class="group rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <span class="font-medium text-marca-negro">{{ $zone->name }}</span>
                        <span class="text-marca-gris-oscuro">{{ $money($zone->price) }}</span>
                    </summary>
                    <p class="mt-2 text-xs text-marca-gris-oscuro">
                        @if ($zone->localidad || $zone->provincia)
                            {{ collect([$zone->localidad, $zone->provincia])->filter()->implode(', ') }}
                        @endif
                        @if($zone->postal_code) · CP {{ $zone->postal_code }} @endif @if($zone->company) · {{ $zone->company->name }} @endif
                    </p>
                    <form method="POST" action="{{ route('admin.shipping.zones.update', $zone) }}" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $zone->name }}" required class="{{ $field }}">
                        <input type="text" name="postal_code" value="{{ $zone->postal_code }}" class="{{ $field }}">
                        <input type="text" name="provincia" value="{{ $zone->provincia }}" placeholder="Provincia" class="{{ $field }}">
                        <input type="text" name="localidad" value="{{ $zone->localidad }}" placeholder="Localidad" class="{{ $field }}">
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
                        <button type="submit" class="w-full rounded-lg border border-marca-rojo/30 px-4 py-2 text-xs font-semibold text-marca-rojo hover:bg-marca-rojo hover:text-marca-blanco">Eliminar</button>
                    </form>
                </details>
            @empty
                <p class="col-span-full rounded-2xl bg-marca-blanco px-5 py-8 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">Todavía no hay zonas de envío.</p>
            @endforelse
        </div>
    </div>
    </div>
@endsection
