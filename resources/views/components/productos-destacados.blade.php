{{-- Seccion "Productos mas vendidos" - Home, debajo de Categorias. --}}
{{-- Ranking real por cantidad vendida en pedidos pagos; si todavia no hay ventas, --}}
{{-- completa con destacados/ultimos cargados para no dejar la seccion vacia. --}}

@php
    try {
        $topIds = \App\Models\OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereNotNull('product_id')
            ->whereHas('order', fn ($q) => $q->paid())
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(4)
            ->pluck('product_id');

        $productos = \App\Models\Product::query()->whereIn('id', $topIds)->where('active', true)->get()
            ->sortBy(fn ($p) => array_search($p->id, $topIds->all()))
            ->values();
        $hayVentas = $productos->isNotEmpty();

        if ($productos->count() < 4) {
            $faltan = 4 - $productos->count();
            $relleno = \App\Models\Product::query()
                ->where('active', true)
                ->whereNotIn('id', $productos->pluck('id'))
                ->orderByDesc('is_featured')
                ->latest()
                ->take($faltan)
                ->get();
            $productos = $productos->concat($relleno);
        }
    } catch (\Throwable $e) {
        // La tabla todavía no existe (falta correr: php artisan migrate).
        $productos = collect();
        $hayVentas = false;
    }
@endphp

@if ($productos->isNotEmpty())
<section class="w-full bg-marca-blanco py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Encabezado centrado --}}
        <div class="mx-auto mb-10 max-w-2xl text-center sm:mb-14">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">
                Lo más vendido
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">
                Productos más vendidos
            </h2>
            <p class="mt-3 text-base text-marca-gris-oscuro">
                @if ($hayVentas)
                    Los repuestos y accesorios que más eligen nuestros clientes.
                @else
                    Una selección de repuestos y accesorios de la tienda.
                @endif
            </p>
        </div>

        {{-- Grilla de 4 productos --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($productos as $producto)
                <div class="group flex flex-col overflow-hidden rounded-2xl border border-marca-gris-claro bg-marca-blanco transition hover:-translate-y-1 hover:border-marca-amarillo hover:shadow-xl">

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
                        @if ($producto->is_featured)
                            <span class="absolute left-3 top-3 rounded-full bg-marca-rojo px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-marca-blanco">
                                Destacado
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

        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-full border-2 border-marca-negro px-7 py-3 text-sm font-bold text-marca-negro transition hover:bg-marca-negro hover:text-marca-blanco">
                Ver todo el catálogo
            </a>
        </div>
    </div>
</section>
@endif
