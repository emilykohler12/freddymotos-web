{{-- Seccion "Categorias destacadas" - Home, debajo del Hero. --}}
{{-- Grilla de 6 tarjetas. Fondo gris claro por tarjeta, hover con borde amarillo/rojo. --}}
{{-- Los iconos son SVG placeholder; se pueden cambiar por <img> con la foto real de cada rubro. --}}

<section id="categorias" class="w-full scroll-mt-4 bg-marca-blanco py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Encabezado de seccion --}}
        <div class="mb-10 flex flex-col gap-3 sm:mb-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                {{-- PLACEHOLDER: bajada corta --}}
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">
                    Encontrá lo que buscás
                </p>
                {{-- PLACEHOLDER: titulo de la seccion --}}
                <h2 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">
                    Categorías destacadas
                </h2>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-marca-negro transition hover:text-marca-rojo">
                Ver todas las categorías
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Grilla de categorias --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @php
                // PLACEHOLDER: en Laravel real esto vendria de la DB (Categoria::destacadas()).
                $categorias = [
                    ['nombre' => 'Cascos',      'icono' => 'M12 3a7 7 0 00-7 7v3H4a1 1 0 000 2h12a5 5 0 005-5 7 7 0 00-7-7h-3z'],
                    ['nombre' => 'Frenos',      'icono' => 'M12 3v3m0 12v3m9-9h-3M6 12H3m14.5-6.5l-2 2m-9 9l-2 2m13 0l-2-2m-9-9l-2-2'],
                    ['nombre' => 'Motor',       'icono' => 'M5 9h2l2-2h6l2 2h2v6h-2l-2 2H9l-2-2H5V9z'],
                    ['nombre' => 'Escapes',     'icono' => 'M3 15h10l3-3h5v4a2 2 0 01-2 2H3v-3zM7 15V9'],
                    ['nombre' => 'Neumáticos',  'icono' => 'M12 4a8 8 0 100 16 8 8 0 000-16zm0 4a4 4 0 100 8 4 4 0 000-8z'],
                    ['nombre' => 'Accesorios',  'icono' => 'M4 7h16M4 12h16M4 17h10'],
                ];
            @endphp

            @foreach ($categorias as $cat)
                <a href="{{ route('products.index', ['category' => $cat['nombre']]) }}"
                   class="group flex flex-col items-center gap-3 rounded-2xl border-2 border-transparent bg-marca-gris-claro p-5 text-center transition
                          hover:border-marca-amarillo hover:bg-marca-blanco hover:shadow-lg
                          focus:border-marca-rojo focus:outline-none">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-marca-blanco text-marca-negro shadow-sm transition group-hover:bg-marca-amarillo">
                        {{-- PLACEHOLDER icono: reemplazar por <img src="..."> del rubro --}}
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cat['icono'] }}"/>
                        </svg>
                    </span>
                    <span class="text-sm font-semibold text-marca-negro">{{ $cat['nombre'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
