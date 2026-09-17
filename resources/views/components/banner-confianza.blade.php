{{-- Seccion "Banner de confianza" - franja oscura con 4 items en linea. Texto en blanco. --}}

<section class="w-full bg-marca-negro py-10 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
            @php
                $items = [
                    [
                        'titulo' => 'Envíos a todo el país',
                        'texto'  => 'Despachamos por correo y transporte',
                        'icono'  => 'M3 7h11v8H3V7zm11 3h4l3 3v2h-7m-7 2a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z',
                    ],
                    [
                        'titulo' => 'Garantía',
                        'texto'  => 'Repuestos con garantía real',
                        'icono'  => 'M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3zm-1.5 9l-2-2m0 0l4-4',
                    ],
                    [
                        'titulo' => 'Pago seguro',
                        'texto'  => 'Tarjeta, transferencia y efectivo',
                        'icono'  => 'M3 7h18v10H3V7zm0 4h18M7 15h4',
                    ],
                    [
                        'titulo' => 'Atención por WhatsApp',
                        'texto'  => 'Te asesoramos antes de comprar',
                        'icono'  => 'M4 20l1.5-4A8 8 0 1112 20a8 8 0 01-4-1L4 20z',
                    ],
                ];
            @endphp

            @foreach ($items as $item)
                <div class="flex flex-col items-center gap-3 text-center sm:flex-row sm:items-center sm:gap-4 sm:text-left">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-marca-blanco/10 text-marca-amarillo">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icono'] }}"/>
                        </svg>
                    </span>
                    <div>
                        {{-- PLACEHOLDER titulo --}}
                        <p class="text-sm font-bold text-marca-blanco">{{ $item['titulo'] }}</p>
                        {{-- PLACEHOLDER texto --}}
                        <p class="text-xs text-marca-blanco/60">{{ $item['texto'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
