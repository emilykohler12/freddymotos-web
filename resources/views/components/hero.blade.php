{{--
  Sección HERO — Home (frontend)
  Tienda de repuestos de motos. Estilo 100% Tailwind, mobile-first.

  Uso:   <x-hero />

  TEXTOS: todo el contenido visible está marcado con {{-- PLACEHOLDER --}}.
  Reemplazá por el texto real de la tienda.

  IMAGEN: hay un SVG de placeholder. Para usar una foto real, reemplazá el
  bloque <svg>...</svg> por:
      <img src="{{ asset('images/hero-moto.png') }}"
           alt="Moto / repuesto destacado"
           class="relative mx-auto w-full max-w-lg drop-shadow-2xl">
--}}

<section class="relative w-full overflow-hidden bg-gradient-to-br from-marca-mostaza to-marca-amarillo">

    {{-- ============ 1. NAVBAR ============ --}}
    <header class="relative z-20">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-2 text-marca-blanco">
                {{-- PLACEHOLDER: logo / nombre del local --}}
                <span class="text-xl font-extrabold tracking-tight sm:text-2xl">
                    FREDDY<span class="text-marca-negro">MOTOS</span>
                </span>
            </a>

            {{-- Toggle menú mobile (solo Tailwind, sin JS) --}}
            <input type="checkbox" id="hero-nav-toggle" class="peer hidden">
            <label for="hero-nav-toggle"
                   class="cursor-pointer rounded-lg p-2 text-marca-blanco lg:hidden"
                   aria-label="Abrir menú">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>

            {{-- Links de navegación (centrados en desktop, dropdown en mobile) --}}
            <nav class="absolute inset-x-0 top-full z-20 hidden flex-col gap-1 border-t border-marca-blanco/10
                        bg-marca-mostaza/95 px-4 pb-4 pt-2 shadow-lg backdrop-blur
                        peer-checked:flex
                        lg:static lg:flex lg:flex-1 lg:flex-row lg:items-center lg:justify-center lg:gap-8
                        lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none lg:backdrop-blur-0">
                <a href="#" class="rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:hover:bg-transparent">Inicio</a>
                <a href="#" class="rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:hover:bg-transparent">Productos</a>
                <a href="#" class="rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:hover:bg-transparent">Categorías</a>
                <a href="#" class="rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:hover:bg-transparent">Nosotros</a>
                <a href="#" class="rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:hover:bg-transparent">Contacto</a>

                {{-- CTA dentro del menú en mobile --}}
                <a href="#" class="mt-2 inline-flex items-center justify-center rounded-full border border-marca-blanco/70 px-5 py-2 text-sm font-semibold text-marca-blanco transition hover:bg-marca-blanco hover:text-marca-negro lg:hidden">
                    Ver catálogo
                </a>
            </nav>

            {{-- Botón de acción a la derecha (desktop) --}}
            <div class="hidden shrink-0 lg:block">
                <a href="#" class="inline-flex items-center rounded-full border border-marca-blanco/70 px-5 py-2 text-sm font-semibold text-marca-blanco transition hover:bg-marca-blanco hover:text-marca-negro">
                    Ver catálogo
                </a>
            </div>
        </div>
    </header>

    {{-- ============ 2 + 3. CONTENIDO (texto izq / imagen der) ============ --}}
    <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 pb-16 pt-8 sm:px-6 lg:grid-cols-2 lg:gap-8 lg:px-8 lg:pb-28 lg:pt-16">

        {{-- Bloque de texto --}}
        <div class="max-w-xl">
            {{-- PLACEHOLDER: nombre del local o frase corta --}}
            <p class="mb-4 inline-block rounded-full bg-marca-negro/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-marca-blanco">
                Repuestos originales para tu moto
            </p>

            {{-- PLACEHOLDER: título grande en dos líneas --}}
            <h1 class="text-4xl font-extrabold leading-[1.05] tracking-tight text-marca-blanco sm:text-5xl lg:text-6xl">
                <span class="block">Título grande</span>
                <span class="block">en dos líneas</span>
            </h1>

            {{-- PLACEHOLDER: párrafo descriptivo corto --}}
            <p class="mt-5 max-w-md text-base leading-relaxed text-marca-blanco/80 sm:text-lg">
                Párrafo descriptivo corto que acompaña al título. Contá en una o dos
                frases qué ofrece la tienda y por qué elegirla.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-4">
                {{-- Botón principal (pill, fondo blanco, texto oscuro) --}}
                <a href="#" class="inline-flex items-center justify-center rounded-full bg-marca-blanco px-7 py-3 text-sm font-bold text-marca-negro shadow-lg shadow-marca-negro/10 transition hover:-translate-y-0.5 hover:bg-marca-gris-claro">
                    Ver productos
                </a>
                <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-marca-blanco/90 transition hover:text-marca-blanco">
                    Ver categorías
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Imagen destacada + salpicaduras decorativas --}}
        <div class="relative">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -left-8 top-6 h-40 w-40 rounded-full bg-marca-mostaza/70 blur-2xl"></div>
                <div class="absolute right-0 top-0 h-56 w-56 rounded-full bg-marca-amarillo/80 blur-3xl"></div>
                <div class="absolute -bottom-4 left-1/4 h-44 w-72 rounded-[45%] bg-marca-blanco/20 blur-2xl"></div>
                <div class="absolute bottom-8 right-8 h-28 w-28 rotate-12 rounded-3xl bg-marca-mostaza/50 blur-xl"></div>
            </div>

            {{-- PLACEHOLDER imagen (ver nota arriba para usar <img>) --}}
            <svg viewBox="0 0 640 380" class="relative mx-auto w-full max-w-lg drop-shadow-2xl" role="img" aria-label="Moto destacada (placeholder)">
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
        </div>
    </div>

    {{-- ============ 5. FRANJA INFERIOR DE TRANSICIÓN ============ --}}
    <div class="relative z-10 bg-marca-blanco">
        <div class="mx-auto flex max-w-7xl flex-col items-center gap-4 px-4 py-6 text-center sm:flex-row sm:justify-center sm:gap-8 sm:px-6 lg:px-8">
            {{-- PLACEHOLDER: garantía · envíos · marcas que se trabajan --}}
            <p class="text-sm font-medium text-marca-gris-oscuro">
                Garantía en todos los repuestos · Envíos a todo el país · Trabajamos las mejores marcas
            </p>
            {{-- Botón secundario --}}
            <a href="#" class="inline-flex shrink-0 items-center rounded-full border border-marca-gris-oscuro/20 px-5 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:border-marca-rojo hover:text-marca-rojo">
                Conocé más
            </a>
        </div>
    </div>
</section>
