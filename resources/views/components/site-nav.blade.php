{{-- Navbar para páginas interiores (catálogo, detalle, carrito, checkout). --}}
{{-- Mismo estilo que el navbar del Hero pero con fondo sólido mostaza→amarillo. --}}
{{-- Uso: <x-site-nav /> --}}

@props([
    'logo' => null,
])

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();
    $cartCount = $cartCount ?? 0;
    $logoUrl = $logo ?: $settings->logo_url;
@endphp

<header class="w-full bg-gradient-to-r from-marca-mostaza to-marca-amarillo">
    <div class="relative mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

        {{-- Logo. Sin link: no es un botón. --}}
        <div class="flex shrink-0 items-center gap-2 text-marca-blanco">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $settings->nombre_local }}" class="h-9 w-auto object-contain sm:h-10">
            @else
                <x-brand-name />
            @endif
        </div>

        {{-- Checkbox del menú mobile (peer): tiene que ser hermano previo del dropdown --}}
        <input type="checkbox" id="site-nav-toggle" class="peer hidden">

        {{-- Carrito + hamburguesa (solo mobile) --}}
        <div class="flex items-center gap-1 lg:hidden">
            <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-marca-blanco" aria-label="Carrito">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span data-cart-count @if (! $cartCount) hidden @endif
                      class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-marca-rojo px-1 text-[10px] font-bold text-marca-blanco">{{ $cartCount }}</span>
            </a>
            <label for="site-nav-toggle" class="cursor-pointer rounded-lg p-2 text-marca-blanco" aria-label="Abrir menú">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
        </div>

        {{-- Grupo derecho --}}
        <div class="absolute inset-x-0 top-full z-20 hidden flex-col gap-1 border-t border-marca-blanco/10
                    bg-marca-mostaza px-4 pb-4 pt-2 shadow-lg
                    peer-checked:flex
                    lg:static lg:flex lg:w-auto lg:flex-row lg:items-center lg:gap-8
                    lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none">
            <nav class="flex flex-col gap-1 lg:flex-row lg:items-center lg:gap-6">
                <x-nav-links />
            </nav>

            <div class="flex items-center gap-3">
                {{-- Carrito en desktop --}}
                <a href="{{ route('cart.index') }}" class="relative hidden rounded-lg p-2 text-marca-blanco transition hover:bg-marca-blanco/10 lg:inline-flex" aria-label="Carrito">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span data-cart-count @if (! $cartCount) hidden @endif
                          class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-marca-rojo px-1 text-[10px] font-bold text-marca-blanco">{{ $cartCount }}</span>
                </a>
            </div>
        </div>
    </div>
</header>
