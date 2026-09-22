@extends('layouts.admin')

@section('title', 'Promociones')
@section('page-heading', 'Promociones')

@section('content')
    @php
        $field = 'rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
    @endphp

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" data-autosubmit class="flex flex-1 flex-wrap gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar promoción..." class="min-w-[180px] flex-1 {{ $field }}">
            <select name="status" class="{{ $field }}">
                <option value="" @selected($status === '')>Activas e inactivas</option>
                <option value="active" @selected($status === 'active')>Solo activas</option>
                <option value="inactive" @selected($status === 'inactive')>Solo inactivas</option>
            </select>
            <select name="type" class="{{ $field }}">
                <option value="" @selected($type === '')>Todos los descuentos</option>
                <option value="percentage" @selected($type === 'percentage')>Porcentaje (%)</option>
                <option value="fixed" @selected($type === 'fixed')>Monto fijo</option>
                <option value="nxm" @selected($type === 'nxm')>Combo (2x1, 3x2...)</option>
            </select>
            <select name="sort" class="{{ $field }}">
                <option value="recent" @selected($sort === 'recent')>Más recientes</option>
                <option value="title_asc" @selected($sort === 'title_asc')>Nombre A-Z</option>
                <option value="title_desc" @selected($sort === 'title_desc')>Nombre Z-A</option>
            </select>
        </form>
        <a href="{{ route('admin.promotions.create') }}" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            + Nueva promoción
        </a>
    </div>

    @if ($promotions->isEmpty())
        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @if ($search !== '' || $status !== '' || $type !== '')
                No hay promociones que coincidan con el filtro.
            @else
                Todavía no hay promociones. Las que actives van a aparecer en la sección "Promociones" del Home.
            @endif
        </p>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($promotions as $promotion)
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-bold text-marca-negro">{{ $promotion->title }}</p>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $promotion->active ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                            {{ $promotion->active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <p class="mt-2 inline-block rounded-full bg-marca-rojo px-3 py-1 text-sm font-extrabold text-marca-blanco">{{ $promotion->label }}</p>
                    <dl class="mt-3 space-y-1 text-sm text-marca-gris-oscuro">
                        <div>Tipo: <span class="font-medium text-marca-negro">{{ $promotion->type_label }}</span></div>
                        <div>Aplica a: <span class="font-medium text-marca-negro">{{ $promotion->scope_label }}</span></div>
                        @if ($promotion->starts_at || $promotion->ends_at)
                            <div>Vigencia:
                                <span class="font-medium text-marca-negro">
                                    {{ optional($promotion->starts_at)->format('d/m/Y') ?? 'sin inicio' }}
                                    –
                                    {{ optional($promotion->ends_at)->format('d/m/Y') ?? 'sin fin' }}
                                </span>
                            </div>
                        @endif
                    </dl>
                    <div class="mt-3 flex items-center gap-3 border-t border-marca-gris-claro pt-3">
                        <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</a>
                        <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" data-confirm="¿Eliminar {{ $promotion->title }}?" class="inline-flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
