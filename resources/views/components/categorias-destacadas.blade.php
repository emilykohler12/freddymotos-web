{{-- Seccion "Categorias destacadas" - Home, debajo del Hero. --}}
{{-- Grilla de tarjetas con las categorías reales que carga el admin (/admin/categorias), hasta 7. --}}
{{-- Si todavía no cargó ninguna, la sección no se muestra (nada de datos de ejemplo). --}}

@php
    $categorias = \App\Models\Category::orderBy('name')->take(7)->get();
    $iconoCategoria = 'M20.5 11.5L12 3 3.5 11.5M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9';
@endphp

@if ($categorias->isNotEmpty())
<section id="categorias" class="w-full scroll-mt-4 bg-marca-blanco py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Encabezado de seccion --}}
        <div class="mb-10 flex flex-col gap-3 sm:mb-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">
                    Encontrá lo que buscás
                </p>
                <h2 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">
                    Categorías destacadas
                </h2>
            </div>
            <a href="{{ route('categories.index') }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-marca-negro transition hover:text-marca-rojo">
                Ver todas las categorías
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Grilla de categorias --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 sm:gap-5 lg:grid-cols-7">
            @foreach ($categorias as $cat)
                <a href="{{ route('products.index', ['category' => $cat->name]) }}"
                   class="group relative flex flex-col overflow-hidden rounded-2xl bg-marca-gris-claro shadow-sm ring-1 ring-marca-negro/5 transition
                          hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-marca-rojo">
                    <div class="relative flex h-28 w-full items-center justify-center overflow-hidden bg-marca-gris-claro sm:h-32">
                        @if ($cat->image_url)
                            <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-marca-blanco text-marca-negro shadow-sm transition group-hover:bg-marca-amarillo">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconoCategoria }}"/>
                                </svg>
                            </span>
                        @endif
                        <span class="absolute inset-0 bg-gradient-to-t from-marca-negro/60 via-transparent to-transparent opacity-0 transition group-hover:opacity-100"></span>
                    </div>
                    <span class="bg-marca-blanco px-3 py-2.5 text-center text-sm font-semibold text-marca-negro transition group-hover:text-marca-rojo">
                        {{ $cat->name }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
