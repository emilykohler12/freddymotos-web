{{-- Tarjeta de producto para la grilla del catálogo y para "productos relacionados". --}}
{{-- Uso: <x-product-card :product="$product" /> o <x-product-card :product="$product" compact /> --}}

@props([
    'product',
    'compact' => false,
])

<div class="group flex h-full flex-col overflow-hidden rounded-2xl bg-marca-gris-claro transition hover:shadow-xl hover:shadow-marca-negro/10">

    {{-- Imagen --}}
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/3] w-full overflow-hidden bg-marca-gris-claro">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            {{-- Placeholder mientras el admin no cargó imagen --}}
            <span class="flex h-full w-full items-center justify-center text-marca-gris-oscuro/25">
                <svg viewBox="0 0 200 200" class="h-2/5 w-2/5" fill="none" stroke="currentColor" stroke-width="4">
                    <circle cx="100" cy="100" r="55"/>
                    <path d="M100 55v90M55 100h90" stroke-linecap="round"/>
                </svg>
            </span>
        @endif

        @unless ($product->in_stock)
            <span class="absolute left-3 top-3 rounded-full bg-marca-negro px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-marca-blanco">
                Sin stock
            </span>
        @endunless
    </a>

    {{-- Datos --}}
    <div class="flex flex-1 flex-col gap-2 p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-marca-mostaza">{{ $product->brand }}</p>

        <h3 class="{{ $compact ? 'text-sm' : 'text-sm sm:text-base' }} font-semibold leading-snug text-marca-negro">
            <a href="{{ route('products.show', $product) }}" class="transition hover:text-marca-rojo">
                {{ $product->name }}
            </a>
        </h3>

        <p class="{{ $compact ? 'text-base' : 'text-lg' }} font-extrabold text-marca-negro">
            {{ $product->formatted_price }}
        </p>

        @unless ($compact)
            {{-- Agrega al carrito por fetch (ver resources/js/cart.js); si falla, envía el form normal. --}}
            <form action="{{ route('cart.add') }}" method="POST" data-cart-add class="mt-auto pt-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" @disabled(! $product->in_stock)
                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco disabled:cursor-not-allowed disabled:bg-marca-gris-claro disabled:text-marca-gris-oscuro/50 disabled:hover:bg-marca-gris-claro">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    {{ $product->in_stock ? 'Agregar al carrito' : 'Sin stock' }}
                </button>
            </form>
        @endunless
    </div>
</div>
