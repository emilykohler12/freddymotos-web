{{-- Footer del sitio - fondo bordo solido, franja fina, ancho completo. --}}
{{-- Uso: <x-footer-tienda /> (nombre "footer-tienda" para no chocar con el <footer> nativo). --}}
{{-- Los datos de contacto y redes salen de SiteSetting ($settings, compartido globalmente). --}}

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();
@endphp

<footer id="contacto" class="w-full scroll-mt-4 bg-marca-bordo text-marca-blanco">
    {{-- Sin max-w ni mx-auto: ancho completo, contenido pegado al borde izquierdo (igual que el navbar) --}}
    <div class="w-full px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-8">

            {{-- Izquierda: logo (si lo subió el admin) + nombre + descripcion, centrado verticalmente --}}
            <div class="flex flex-col items-start justify-center">
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-3 h-12 w-auto shrink-0 object-contain">
                @endif
                <span class="text-xl font-extrabold tracking-tight text-marca-blanco">{{ $settings->nombre_local }}</span>
                <p class="mt-4 max-w-xs text-sm text-marca-blanco/70">
                    Repuestos y accesorios para tu moto. Atención personalizada y envíos a todo el país.
                </p>
                <div class="mt-5 flex gap-3">
                    @if ($settings->instagram_url)
                        <a href="{{ $settings->instagram_url }}" target="_blank" rel="noopener" aria-label="Instagram" class="flex h-9 w-9 items-center justify-center rounded-full bg-marca-blanco/10 text-marca-blanco transition hover:bg-marca-amarillo hover:text-marca-negro">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.2c3.2 0 3.6 0 4.9.07 1.2.06 1.8.25 2.2.42.6.2 1 .48 1.4.9.4.4.7.8.9 1.4.17.4.36 1 .42 2.2.06 1.3.07 1.7.07 4.9s0 3.6-.07 4.9c-.06 1.2-.25 1.8-.42 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.17-1 .36-2.2.42-1.3.06-1.7.07-4.9.07s-3.6 0-4.9-.07c-1.2-.06-1.8-.25-2.2-.42-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.17-.4-.36-1-.42-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.9c.06-1.2.25-1.8.42-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.17 1-.36 2.2-.42C8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.07-.9.04-1.4.2-1.7.32-.4.17-.7.37-1 .67-.3.3-.5.6-.67 1-.12.3-.28.8-.32 1.7C3.5 8.5 3.5 8.9 3.5 12s0 3.5.07 4.7c.04.9.2 1.4.32 1.7.17.4.37.7.67 1 .3.3.6.5 1 .67.3.12.8.28 1.7.32 1.2.07 1.6.07 4.7.07s3.5 0 4.7-.07c.9-.04 1.4-.2 1.7-.32.4-.17.7-.37 1-.67.3-.3.5-.6.67-1 .12-.3.28-.8.32-1.7.07-1.2.07-1.6.07-4.7s0-3.5-.07-4.7c-.04-.9-.2-1.4-.32-1.7-.17-.4-.37-.7-.67-1-.3-.3-.6-.5-1-.67-.3-.12-.8-.28-1.7-.32-1.2-.07-1.6-.07-4.7-.07zm0 3.05a4.95 4.95 0 110 9.9 4.95 4.95 0 010-9.9zm0 1.8a3.15 3.15 0 100 6.3 3.15 3.15 0 000-6.3zm5.15-3.05a1.15 1.15 0 110 2.3 1.15 1.15 0 010-2.3z"/></svg>
                        </a>
                    @endif
                    @if ($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook" class="flex h-9 w-9 items-center justify-center rounded-full bg-marca-blanco/10 text-marca-blanco transition hover:bg-marca-amarillo hover:text-marca-negro">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1V7.9c0-.9.25-1.5 1.55-1.5H17V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.4H7.5V13H10v8h3.5z"/></svg>
                        </a>
                    @endif
                    @if ($settings->whatsapp_link)
                        <a href="{{ $settings->whatsapp_link }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="flex h-9 w-9 items-center justify-center rounded-full bg-marca-blanco/10 text-marca-blanco transition hover:bg-marca-amarillo hover:text-marca-negro">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20l1.5-4A8 8 0 1112 20a8 8 0 01-4-1L4 20z"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Derecha: links rapidos + contacto, uno al lado del otro --}}
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-marca-amarillo">Links rápidos</h3>
                    <ul class="mt-4 space-y-2.5 text-sm text-marca-blanco/70">
                        <li><a href="{{ url('/') }}" class="transition hover:text-marca-amarillo">Inicio</a></li>
                        <li><a href="{{ route('products.index') }}" class="transition hover:text-marca-amarillo">Productos</a></li>
                        <li><a href="{{ route('cart.index') }}" class="transition hover:text-marca-amarillo">Mi carrito</a></li>
                        <li><a href="{{ url('/#sobre-nosotros') }}" class="transition hover:text-marca-amarillo">Sobre nosotros</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-marca-amarillo">Contacto</h3>
                    <ul class="mt-4 space-y-3 text-sm text-marca-blanco/70">
                        @if ($settings->telefono)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-blanco" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5l4-1 2 5-3 2a12 12 0 006 6l2-3 5 2-1 4a2 2 0 01-2 2A16 16 0 013 7a2 2 0 010-2z"/></svg>
                                <a href="{{ $settings->tel_link }}" class="transition hover:text-marca-amarillo">{{ $settings->telefono }}</a>
                            </li>
                        @endif
                        @if ($settings->direccion)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-blanco" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11zm0-8a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/></svg>
                                <span>{{ $settings->direccion }}</span>
                            </li>
                        @endif
                        @if ($settings->email)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-blanco" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18v12H3zM3 7l9 6 9-6"/></svg>
                                <a href="mailto:{{ $settings->email }}" class="transition hover:text-marca-amarillo">{{ $settings->email }}</a>
                            </li>
                        @endif
                        @if ($settings->horario_atencion)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-blanco" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $settings->horario_atencion }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Linea inferior --}}
    <div class="border-t border-marca-blanco/10">
        <div class="flex w-full flex-col items-center justify-between gap-2 px-4 py-3 text-xs text-marca-blanco/60 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} {{ $settings->nombre_local }}. Todos los derechos reservados.</p>
            <p>Repuestos y accesorios para motos.</p>
        </div>
    </div>
</footer>
