{{-- Seccion "Repuestos mas vendidos" - Home, debajo de Categorias. --}}
{{-- Ranking 100% real por cantidad vendida en pedidos pagos (sin relleno de --}}
{{-- destacados/ultimos): si todavia no hay ventas, la seccion no se muestra. --}}
{{-- Colores invertidos respecto a "Categorías destacadas" (fondo negro, tarjetas blancas). --}}
{{-- Con más de 5 productos se muestra como carrusel con flechas. --}}

@php
    try {
        $topIds = \App\Models\OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereNotNull('product_id')
            ->whereHas('order', fn ($q) => $q->paid())
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(12)
            ->pluck('product_id');

        $productos = \App\Models\Product::query()->whereIn('id', $topIds)->where('active', true)->where('stock', '>', 0)->get()
            ->sortBy(fn ($p) => array_search($p->id, $topIds->all()))
            ->values();
    } catch (\Throwable $e) {
        // La tabla todavía no existe (falta correr: php artisan migrate).
        $productos = collect();
    }

    $esCarrusel = $productos->count() > 5;
@endphp

@if ($productos->isNotEmpty())
<section class="w-full bg-marca-negro py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Encabezado centrado --}}
        <div class="mx-auto mb-10 max-w-2xl text-center sm:mb-14">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-amarillo">
                Lo más vendido
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight text-marca-blanco sm:text-4xl">
                Repuestos más vendidos
            </h2>
            <p class="mt-3 text-base text-marca-blanco/70">
                Los repuestos y accesorios que más eligen nuestros clientes.
            </p>
        </div>

        {{-- Grilla o carrusel según la cantidad --}}
        <div class="relative">
            @if ($esCarrusel)
                <button type="button" aria-label="Anteriores"
                        onclick="document.getElementById('bestsellers-track').scrollBy({left: -288, behavior: 'smooth'})"
                        class="absolute -left-3 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-marca-blanco text-marca-negro shadow-lg transition hover:bg-marca-amarillo sm:flex">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" aria-label="Siguientes"
                        onclick="document.getElementById('bestsellers-track').scrollBy({left: 288, behavior: 'smooth'})"
                        class="absolute -right-3 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-marca-blanco text-marca-negro shadow-lg transition hover:bg-marca-amarillo sm:flex">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif

            <div id="bestsellers-track"
                 class="{{ $esCarrusel
                    ? 'flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory px-1 pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden'
                    : 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5' }}">
                @foreach ($productos as $producto)
                    <div class="group flex flex-col overflow-hidden rounded-2xl bg-marca-blanco shadow-lg transition hover:-translate-y-1 hover:shadow-xl {{ $esCarrusel ? 'w-64 shrink-0 snap-start' : '' }}">

                        {{-- Imagen --}}
                        <a href="{{ route('products.show', $producto) }}" class="relative block aspect-square w-full overflow-hidden bg-marca-gris-claro">
                            @if ($producto->image_url)
                                <img src="{{ $producto->image_url }}" alt="{{ $producto->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full w-full items-center justify-center text-marca-gris-oscuro/25">
                                    <svg viewBox="0 0 200 200" class="h-2/5 w-2/5" fill="none" stroke="currentColor" stroke-width="4">
                                        <circle cx="100" cy="100" r="55"/><path d="M100 55v90M55 100h90" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            @endif
                        </a>

                        {{-- Datos --}}
                        <div class="flex flex-1 flex-col gap-3 p-4">
                            <h3 class="text-sm font-semibold text-marca-negro">
                                <a href="{{ route('products.show', $producto) }}" class="transition hover:text-marca-rojo">{{ $producto->name }}</a>
                            </h3>
                            <p class="text-lg font-extrabold text-marca-negro">
                                {{ $producto->formatted_price }}
                            </p>

                            <form action="{{ route('cart.add') }}" method="POST" data-cart-add class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $producto->id }}">
                                <button type="submit" @disabled(! $producto->in_stock)
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco disabled:cursor-not-allowed disabled:bg-marca-gris-claro disabled:text-marca-gris-oscuro/50 disabled:hover:bg-marca-gris-claro">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $producto->in_stock ? 'Agregar al carrito' : 'Sin stock' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-full border-2 border-marca-blanco px-7 py-3 text-sm font-bold text-marca-blanco transition hover:bg-marca-blanco hover:text-marca-negro">
                Ver todo el catálogo
            </a>
        </div>
    </div>
</section>
@endif
