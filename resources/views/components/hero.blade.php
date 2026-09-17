{{-- Seccion HERO - Home (frontend). Tienda de repuestos de motos. 100% Tailwind, mobile-first. --}}
{{-- Uso en una vista: <x-hero :logo="$logoUrl" nombre="Freddy Motos" /> --}}
{{-- Los textos visibles estan marcados mas abajo como placeholders: reemplazalos por el contenido real de la tienda. --}}
{{-- La imagen destacada es un SVG de placeholder. Para usar una foto real, mira la nota junto al bloque svg. --}}

@props([
    // URL de la imagen del logo que sube el admin desde el panel.
    // Ej. en el controller: $logoUrl = \Storage::url(setting('logo_path'));
    // Si viene null se muestra el texto de la marca como fallback.
    'logo' => null,
    // Nombre del local (fallback textual del logo + alt de la imagen).
    'nombre' => 'Freddy Motos',
])

@php $cartCount = $cartCount ?? 0; @endphp

<section class="relative flex w-full flex-col overflow-hidden bg-gradient-to-br from-marca-mostaza via-marca-amarillo to-marca-amarillo lg:min-h-[92vh]">

    {{-- ============ 1. NAVBAR (ancho completo) ============ --}}
    <header class="relative z-20 w-full">
        <div class="flex w-full items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            {{-- Logo pegado al borde izquierdo. Si el admin subio una imagen se usa esa; si no, el nombre real del negocio. Sin link: no es un botón. --}}
            <div class="flex shrink-0 items-center gap-2 text-marca-blanco">
                @if ($logo)
                    <img src="{{ $logo }}" alt="{{ $nombre }}" class="h-9 w-auto object-contain sm:h-10">
                @else
                    <x-brand-name :nombre="$nombre" />
                @endif
            </div>

            {{-- Checkbox del menú mobile (peer): hermano previo del dropdown --}}
            <input type="checkbox" id="hero-nav-toggle" class="peer hidden">

            {{-- Carrito + hamburguesa (solo mobile) --}}
            <div class="flex items-center gap-1 lg:hidden">
                <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-marca-blanco" aria-label="Carrito">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span data-cart-count @if (! $cartCount) hidden @endif
                          class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-marca-rojo px-1 text-[10px] font-bold text-marca-blanco">{{ $cartCount }}</span>
                </a>
                <label for="hero-nav-toggle" class="cursor-pointer rounded-lg p-2 text-marca-blanco" aria-label="Abrir menú">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </label>
            </div>

            {{-- Grupo derecho: links + boton "Ver catalogo" + carrito. Dropdown en mobile, fila a la derecha en desktop. --}}
            <div class="absolute inset-x-0 top-full z-20 hidden flex-col gap-1 border-t border-marca-blanco/10
                        bg-marca-mostaza/95 px-4 pb-4 pt-2 shadow-lg backdrop-blur
                        peer-checked:flex
                        lg:static lg:flex lg:w-auto lg:flex-row lg:items-center lg:gap-8
                        lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none lg:backdrop-blur-0">
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

    {{-- ============ 2 + 3. CONTENIDO (texto izq / imagen der) ============ --}}
    <div class="relative z-10 mx-auto grid w-full max-w-7xl flex-1 grid-cols-1 items-center gap-10 px-4 pb-20 pt-6 sm:px-6 lg:grid-cols-2 lg:gap-10 lg:px-8 lg:pb-28 lg:pt-12">

        {{-- Bloque de texto --}}
        <div class="max-w-xl">
            {{-- PLACEHOLDER: nombre del local o frase corta --}}
            <p class="mb-4 inline-block rounded-full bg-marca-negro/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-marca-blanco">
                Repuestos originales para tu moto
            </p>

            <h1 class="text-[2.75rem] font-extrabold leading-[1.02] tracking-tight text-marca-blanco sm:text-6xl lg:text-7xl">
                <span class="block">Encontrá el repuesto</span>
                <span class="block">que tu moto necesita</span>
            </h1>

            <p class="mt-5 max-w-md text-base leading-relaxed text-marca-blanco/80 sm:text-lg">
                Cascos, frenos, filtros y mucho más, con envíos a todo el país.
                Te ayudamos a elegir la pieza justa para tu modelo.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-4">
                {{-- Botón principal (pill, fondo blanco, texto oscuro) --}}
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-full bg-marca-blanco px-7 py-3 text-sm font-bold text-marca-negro shadow-lg shadow-marca-negro/10 transition hover:-translate-y-0.5 hover:bg-marca-gris-claro">
                    Ver productos
                </a>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-marca-blanco/90 transition hover:text-marca-blanco">
                    Ver categorías
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Imagen destacada + salpicaduras decorativas --}}
        <div class="relative lg:-mr-4">
            {{-- Salpicaduras / textura en tono mostaza-amarillo detras de la imagen --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute left-1/2 top-1/2 h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-marca-amarillo/70 blur-3xl sm:h-96 sm:w-96"></div>
                <div class="absolute -left-10 top-4 h-40 w-40 rounded-full bg-marca-mostaza/60 blur-2xl"></div>
                <div class="absolute -bottom-6 right-2 h-44 w-72 rounded-[45%] bg-marca-blanco/25 blur-2xl"></div>
                <svg viewBox="0 0 400 400" class="absolute inset-0 h-full w-full text-marca-mostaza/40" fill="currentColor">
                    <path d="M210 40c34-14 58 18 92 12s52 34 46 68 26 58 8 88-8 62-42 70-46 40-82 34-58-26-92-30-64 4-82-26-2-60-6-94 4-64 26-84 44-8 76-20 38-22 32-8z"/>
                </svg>
            </div>

            {{-- PLACEHOLDER imagen (ver nota abajo para usar <img>) --}}
            <svg viewBox="0 0 640 380" class="relative mx-auto w-full max-w-xl drop-shadow-2xl" role="img" aria-label="Moto destacada (placeholder)">
                <ellipse cx="320" cy="352" rx="250" ry="18" fill="#141414" opacity="0.15"/>
                <circle cx="150" cy="270" r="78" fill="#141414"/>
                <circle cx="150" cy="270" r="40" fill="none" stroke="#F5C518" stroke-width="12"/>
                <circle cx="150" cy="270" r="10" fill="#F5C518"/>
                <circle cx="500" cy="270" r="78" fill="#141414"/>
                <circle cx="500" cy="270" r="40" fill="none" stroke="#F5C518" stroke-width="12"/>
                <circle cx="500" cy="270" r="10" fill="#F5C518"/>
                <path d="M120 262 L250 190 L370 182 L440 140 L482 152 L512 268 Z" fill="#2A2A2A"/>
                <path d="M245 196 Q345 122 470 156 L472 182 Q372 168 300 214 Z" fill="#F5C518"/>
                <path d="M120 258 Q170 214 260 206 L262 232 Q180 244 148 266 Z" fill="#141414"/>
                <rect x="476" y="150" width="16" height="118" rx="8" fill="#2A2A2A" transform="rotate(13 484 209)"/>
                <path d="M448 140 l46 -10" stroke="#141414" stroke-width="12" stroke-linecap="round"/>
                <path d="M300 214 L360 262 L470 262 L470 240 L360 240 Z" fill="#6E1423"/>
            </svg>

            {{-- Para foto real, reemplaza el <svg> de arriba por:
                 <img src="/images/hero-moto.png" alt="Moto destacada"
                      class="relative mx-auto w-full max-w-xl drop-shadow-2xl">
                 (o usa la ruta con el helper asset() de Laravel) --}}
        </div>
    </div>

    {{-- ============ 5. FRANJA INFERIOR DE TRANSICIÓN ============ --}}
    <div class="relative z-10 bg-marca-blanco">
        <div class="mx-auto flex max-w-7xl items-center justify-center px-4 py-6 text-center sm:px-6 lg:px-8">
            <p class="text-sm font-medium text-marca-gris-oscuro">
                Garantía en todos los repuestos · Envíos a todo el país · Trabajamos las mejores marcas
            </p>
        </div>
    </div>
</section>
