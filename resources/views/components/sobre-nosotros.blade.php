{{-- Seccion "Sobre nosotros" corta - imagen a un lado, texto al otro. Fondo bordo como acento. --}}
{{-- La historia y la fecha de creacion salen de SiteSetting (las carga el admin en Configuracion). --}}

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();

    try {
        // "Atendido" = tiene al menos un pedido pago. Sube cada vez que un
        // cliente nuevo completa una compra (no cuenta carritos abandonados).
        $clientesAtendidos = \App\Models\Customer::query()->conCompra()->count();
        $marcasDisponibles = \App\Models\Product::query()->where('active', true)->distinct('brand')->count('brand');
    } catch (\Throwable $e) {
        $clientesAtendidos = 0;
        $marcasDisponibles = 0;
    }

    $stats = array_filter([
        $settings->anos_trayectoria !== null
            ? ['valor' => '+' . $settings->anos_trayectoria, 'label' => Str::plural('año', $settings->anos_trayectoria) . ' de trayectoria']
            : null,
        ['valor' => (string) $clientesAtendidos, 'label' => 'clientes atendidos'],
        ['valor' => (string) $marcasDisponibles, 'label' => 'marcas disponibles'],
    ]);
@endphp

<section id="sobre-nosotros" class="w-full scroll-mt-4 bg-marca-bordo py-14 sm:py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8">

        {{-- Imagen del local --}}
        <div class="relative">
            <div aria-hidden="true" class="absolute -inset-3 -z-10 rounded-3xl bg-marca-amarillo/20 blur-xl"></div>
            {{-- PLACEHOLDER imagen: reemplazar por <img src="/images/local.jpg" alt="Nuestro local" class="w-full rounded-3xl object-cover shadow-2xl"> --}}
            <div class="flex aspect-[4/3] w-full items-center justify-center rounded-3xl bg-marca-negro/30 text-marca-blanco/40 shadow-2xl ring-1 ring-marca-blanco/10">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 21V9l8-5 8 5v12M9 21v-6h6v6"/>
                </svg>
            </div>
        </div>

        {{-- Texto --}}
        <div class="max-w-xl text-marca-blanco">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-amarillo">
                Sobre nosotros
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                {{ $settings->nombre_local }}
            </h2>

            @if ($settings->historia)
                <p class="mt-5 whitespace-pre-line text-base leading-relaxed text-marca-blanco/80">
                    {{ $settings->historia }}
                </p>
            @endif

            {{-- Datos rapidos / trayectoria (reales: clientes y marcas siempre; años solo si se cargó la fecha de creación) --}}
            {{-- Tailwind necesita las clases completas y literales para generarlas: --}}
            {{-- grid-cols-2 grid-cols-3 --}}
            <dl class="mt-8 grid {{ count($stats) === 3 ? 'grid-cols-3' : 'grid-cols-2' }} gap-4 border-t border-marca-blanco/15 pt-6">
                @foreach ($stats as $stat)
                    <div>
                        <dt class="text-2xl font-extrabold text-marca-amarillo">{{ $stat['valor'] }}</dt>
                        <dd class="text-xs text-marca-blanco/70">{{ $stat['label'] }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- Va a Google Maps con la dirección cargada en Configuración; si no hay dirección, no se muestra. --}}
            @if ($settings->maps_url)
                <a href="{{ $settings->maps_url }}" target="_blank" rel="noopener" class="mt-8 inline-flex items-center justify-center rounded-full bg-marca-amarillo px-7 py-3 text-sm font-bold text-marca-negro transition hover:bg-marca-blanco">
                    Conocé la tienda
                </a>
            @endif
        </div>
    </div>
</section>
