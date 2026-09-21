{{-- Seccion "Sobre nosotros" corta - imagen a un lado, texto al otro. Fondo bordo como acento. --}}
{{-- La historia y la fecha de creacion salen de SiteSetting (las carga el admin en Configuracion). --}}

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();
    $fotoLocal = \App\Models\BusinessPhoto::latest()->first();

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

<section id="sobre-nosotros" class="w-full scroll-mt-4 bg-marca-amarillo py-14 sm:py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:gap-16 lg:px-8">

        {{-- Imagen del local: la última foto que subió el admin en Configuración, o un ícono genérico si todavía no cargó ninguna. --}}
        <div class="relative">
            <div aria-hidden="true" class="absolute -inset-3 -z-10 rounded-3xl bg-marca-bordo/15 blur-xl"></div>
            @if ($fotoLocal)
                <img src="{{ $fotoLocal->url }}" alt="{{ $settings->nombre_local }}" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-2xl">
            @else
                <div class="flex aspect-[4/3] w-full items-center justify-center rounded-3xl bg-marca-negro/85 text-marca-blanco/40 shadow-2xl ring-1 ring-marca-negro/10">
                    <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 21V9l8-5 8 5v12M9 21v-6h6v6"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Texto --}}
        <div class="max-w-xl text-marca-negro">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">
                Sobre nosotros
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                {{ $settings->nombre_local }}
            </h2>

            @if ($settings->historia)
                <p class="mt-5 whitespace-pre-line text-base leading-relaxed text-marca-negro/80">
                    {{ $settings->historia }}
                </p>
            @endif

            {{-- Datos rapidos / trayectoria (reales: clientes y marcas siempre; años solo si se cargó la fecha de creación) --}}
            {{-- Tailwind necesita las clases completas y literales para generarlas: --}}
            {{-- grid-cols-2 grid-cols-3 --}}
            <dl class="mt-8 grid {{ count($stats) === 3 ? 'grid-cols-3' : 'grid-cols-2' }} gap-4 border-t border-marca-negro/15 pt-6">
                @foreach ($stats as $stat)
                    <div>
                        <dt class="text-2xl font-extrabold text-marca-bordo">{{ $stat['valor'] }}</dt>
                        <dd class="text-xs text-marca-negro/70">{{ $stat['label'] }}</dd>
                    </div>
                @endforeach
            </dl>

            {{-- Datos de contacto reales cargados por el admin en Configuración. --}}
            @if ($settings->horario_atencion || $settings->direccion || $settings->telefono || $settings->email)
                <ul class="mt-8 space-y-2.5 border-t border-marca-negro/15 pt-6 text-sm text-marca-negro/80">
                    @if ($settings->direccion)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11zm0-8a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
                            @if ($settings->maps_url)
                                <a href="{{ $settings->maps_url }}" target="_blank" rel="noopener" class="underline decoration-marca-negro/30 transition hover:text-marca-bordo hover:decoration-marca-bordo">{{ $settings->direccion }}</a>
                            @else
                                <span>{{ $settings->direccion }}</span>
                            @endif
                        </li>
                    @endif
                    @if ($settings->horario_atencion)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $settings->horario_atencion }}</span>
                        </li>
                    @endif
                    @if ($settings->telefono)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5l4-1 2 5-3 2a12 12 0 006 6l2-3 5 2-1 4a2 2 0 01-2 2A16 16 0 013 7a2 2 0 010-2z"/></svg>
                            <a href="{{ $settings->tel_link }}" class="transition hover:text-marca-bordo">{{ $settings->telefono }}</a>
                        </li>
                    @endif
                    @if ($settings->email)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18v12H3zM3 7l9 6 9-6"/></svg>
                            <a href="mailto:{{ $settings->email }}" class="transition hover:text-marca-bordo">{{ $settings->email }}</a>
                        </li>
                    @endif
                </ul>
            @endif

            {{-- Va a Google Maps con la dirección cargada en Configuración; si no hay dirección, no se muestra. --}}
            @if ($settings->maps_url)
                <a href="{{ $settings->maps_url }}" target="_blank" rel="noopener" class="mt-6 inline-flex items-center justify-center rounded-full bg-marca-negro px-7 py-3 text-sm font-bold text-marca-blanco transition hover:bg-marca-bordo">
                    Conocé la tienda
                </a>
            @endif
        </div>
    </div>
</section>
