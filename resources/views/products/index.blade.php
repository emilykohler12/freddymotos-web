@extends('layouts.app')

@section('title', 'Catálogo de productos — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">

            {{-- Encabezado --}}
            <div class="mb-8">
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">Catálogo</p>
                <h1 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">Todos los productos</h1>
                <p class="mt-2 text-sm text-marca-gris-oscuro">{{ $products->total() }} productos encontrados</p>
            </div>

            {{-- ============ Barra de filtros ============ --}}
            <form method="GET" action="{{ route('products.index') }}"
                  class="mb-10 grid grid-cols-1 gap-4 rounded-2xl bg-marca-gris-claro p-4 sm:grid-cols-2 sm:p-6 lg:grid-cols-12">

                {{-- Buscador --}}
                <div class="lg:col-span-4">
                    <label for="f-search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Buscar</label>
                    <input type="search" id="f-search" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Nombre, marca, modelo…"
                           class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm text-marca-negro placeholder:text-marca-gris-oscuro/40 focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>

                {{-- Categoría --}}
                <div class="lg:col-span-3">
                    <label for="f-category" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Categoría</label>
                    <select id="f-category" name="category"
                            class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm text-marca-negro focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <option value="">Todas</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Marca --}}
                <div class="lg:col-span-3">
                    <label for="f-brand" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Marca</label>
                    <select id="f-brand" name="brand"
                            class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm text-marca-negro focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <option value="">Todas</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand }}" @selected(($filters['brand'] ?? '') === $brand)>{{ $brand }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Rango de precio --}}
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Precio</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min" min="0" step="1000" value="{{ $filters['min'] ?? '' }}" placeholder="Mín"
                               class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-2 py-2 text-sm text-marca-negro placeholder:text-marca-gris-oscuro/40 focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <span class="text-marca-gris-oscuro/50">–</span>
                        <input type="number" name="max" min="0" step="1000" value="{{ $filters['max'] ?? '' }}" placeholder="Máx"
                               class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-2 py-2 text-sm text-marca-negro placeholder:text-marca-gris-oscuro/40 focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-end gap-3 sm:col-span-2 lg:col-span-12">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-full bg-marca-negro px-6 py-2.5 text-sm font-bold text-marca-blanco transition hover:bg-marca-rojo">
                        Aplicar filtros
                    </button>
                    @if (array_filter($filters))
                        <a href="{{ route('products.index') }}"
                           class="inline-flex items-center justify-center rounded-full border border-marca-gris-oscuro/20 px-5 py-2.5 text-sm font-semibold text-marca-gris-oscuro transition hover:border-marca-rojo hover:text-marca-rojo">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            {{-- ============ Grilla ============ --}}
            @if ($products->isEmpty())
                <div class="rounded-2xl bg-marca-gris-claro px-6 py-16 text-center">
                    <p class="text-lg font-semibold text-marca-negro">No encontramos productos con esos filtros.</p>
                    <p class="mt-1 text-sm text-marca-gris-oscuro">Probá cambiar la búsqueda o quitar algún filtro.</p>
                    <a href="{{ route('products.index') }}" class="mt-5 inline-flex rounded-full bg-marca-amarillo px-6 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                        Ver todo el catálogo
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                {{-- ============ Paginación ============ --}}
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </main>

@endsection
