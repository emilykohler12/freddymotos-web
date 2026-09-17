@extends('layouts.app')

@section('title', $product->name . ' — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="mb-6 flex flex-wrap items-center gap-1 text-xs text-marca-gris-oscuro/70">
                <a href="{{ route('home') }}" class="transition hover:text-marca-rojo">Inicio</a>
                <span>/</span>
                <a href="{{ route('products.index') }}" class="transition hover:text-marca-rojo">Productos</a>
                <span>/</span>
                <a href="{{ route('products.index', ['category' => $product->category]) }}" class="transition hover:text-marca-rojo">{{ $product->category }}</a>
                <span>/</span>
                <span class="text-marca-negro">{{ $product->name }}</span>
            </nav>

            {{-- ============ Detalle ============ --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">

                {{-- Imagen grande --}}
                <div class="relative overflow-hidden rounded-3xl bg-marca-gris-claro">
                    <div class="aspect-square w-full">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center text-marca-gris-oscuro/25">
                                <svg viewBox="0 0 200 200" class="h-1/3 w-1/3" fill="none" stroke="currentColor" stroke-width="4">
                                    <circle cx="100" cy="100" r="55"/>
                                    <path d="M100 55v90M55 100h90" stroke-linecap="round"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex flex-col">
                    <p class="text-sm font-semibold uppercase tracking-wide text-marca-mostaza">{{ $product->brand }}</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">{{ $product->name }}</h1>

                    <p class="mt-4 text-4xl font-extrabold text-marca-negro">{{ $product->formatted_price }}</p>

                    {{-- Indicador de stock --}}
                    <div class="mt-4 flex items-center gap-2 text-sm font-semibold">
                        @if ($product->in_stock)
                            <span class="h-2.5 w-2.5 rounded-full bg-marca-amarillo"></span>
                            <span class="text-marca-gris-oscuro">Disponible · {{ $product->stock }} en stock</span>
                        @else
                            <span class="h-2.5 w-2.5 rounded-full bg-marca-rojo"></span>
                            <span class="text-marca-rojo">Sin stock por el momento</span>
                        @endif
                    </div>

                    {{-- Ficha --}}
                    <dl class="mt-6 divide-y divide-marca-gris-claro border-y border-marca-gris-claro text-sm">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-semibold text-marca-gris-oscuro">Marca</dt>
                            <dd class="text-right text-marca-negro">{{ $product->brand }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-semibold text-marca-gris-oscuro">Categoría</dt>
                            <dd class="text-right text-marca-negro">{{ $product->category }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="font-semibold text-marca-gris-oscuro">Modelo compatible</dt>
                            <dd class="text-right text-marca-negro">{{ $product->compatible_model ?? '—' }}</dd>
                        </div>
                    </dl>

                    {{-- Descripción --}}
                    @if ($product->description)
                        <div class="mt-6">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-marca-gris-oscuro">Descripción</h2>
                            <p class="mt-2 text-sm leading-relaxed text-marca-gris-oscuro">{{ $product->description }}</p>
                        </div>
                    @endif

                    {{-- Cantidad + Agregar al carrito --}}
                    <form action="{{ route('cart.add') }}" method="POST" data-cart-add class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="flex items-center rounded-full border border-marca-gris-oscuro/20" data-qty>
                            <button type="button" data-qty-minus aria-label="Restar"
                                    class="flex h-11 w-11 items-center justify-center rounded-full text-lg font-bold text-marca-negro transition hover:bg-marca-gris-claro disabled:opacity-30"
                                    @disabled(! $product->in_stock)>−</button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ max($product->stock, 1) }}"
                                   class="h-11 w-12 border-0 bg-transparent text-center text-sm font-bold text-marca-negro focus:outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none"
                                   @disabled(! $product->in_stock)>
                            <button type="button" data-qty-plus aria-label="Sumar"
                                    class="flex h-11 w-11 items-center justify-center rounded-full text-lg font-bold text-marca-negro transition hover:bg-marca-gris-claro disabled:opacity-30"
                                    @disabled(! $product->in_stock)>+</button>
                        </div>

                        <button type="submit" @disabled(! $product->in_stock)
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-full bg-marca-amarillo px-8 py-3.5 text-base font-extrabold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco disabled:cursor-not-allowed disabled:bg-marca-gris-claro disabled:text-marca-gris-oscuro/50 disabled:hover:bg-marca-gris-claro">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ $product->in_stock ? 'Agregar al carrito' : 'Sin stock' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- ============ Productos relacionados ============ --}}
            @if ($related->isNotEmpty())
                <section class="mt-16 sm:mt-24">
                    <h2 class="mb-8 text-2xl font-extrabold tracking-tight text-marca-negro sm:text-3xl">Productos relacionados</h2>
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                        @foreach ($related as $item)
                            <x-product-card :product="$item" compact />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>


    {{-- Selector de cantidad (+/−). Script mínimo, sin dependencias. --}}
    <script>
        document.querySelectorAll('[data-qty]').forEach(function (wrap) {
            var input = wrap.querySelector('input[name="quantity"]');
            var min = parseInt(input.min || '1', 10);
            var max = parseInt(input.max || '99', 10);
            wrap.querySelector('[data-qty-minus]').addEventListener('click', function () {
                input.value = Math.max(min, (parseInt(input.value, 10) || min) - 1);
            });
            wrap.querySelector('[data-qty-plus]').addEventListener('click', function () {
                input.value = Math.min(max, (parseInt(input.value, 10) || min) + 1);
            });
        });
    </script>
@endsection
