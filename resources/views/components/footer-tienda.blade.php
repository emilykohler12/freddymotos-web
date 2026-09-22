{{-- Footer del sitio - fondo bordo solido, franja fina, ancho completo. --}}
{{-- Uso: <x-footer-tienda /> (nombre "footer-tienda" para no chocar con el <footer> nativo). --}}
{{-- Los datos de contacto y redes salen de SiteSetting ($settings, compartido globalmente). --}}

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();
@endphp

<footer id="contacto" class="w-full scroll-mt-4 bg-marca-bordo text-marca-blanco">
    {{-- Franja de acento arriba: rompe el corte brusco con la sección anterior --}}
    <div class="h-1 w-full bg-gradient-to-r from-marca-amarillo via-marca-mostaza to-marca-amarillo"></div>

    {{-- Sin max-w ni mx-auto: ancho completo, contenido pegado al borde izquierdo (igual que el navbar) --}}
    <div class="w-full px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">

            {{-- Marca: logo (si lo subió el admin) + nombre + descripcion + redes --}}
            <div class="sm:col-span-2 lg:col-span-1">
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-3 h-12 w-auto shrink-0 object-contain">
                @else
                    <span class="text-xl font-extrabold tracking-tight text-marca-blanco">{{ $settings->nombre_local }}</span>
                @endif
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-marca-blanco/70">
                    Repuestos y accesorios para tu moto. Atención personalizada y envíos a todo el país.
                </p>
                @if ($settings->instagram_url || $settings->facebook_url)
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
                    </div>
                @endif
            </div>

            {{-- Links rapidos --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-marca-amarillo">Links rápidos</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-marca-blanco/70">
                    <li><a href="{{ url('/') }}" class="transition hover:text-marca-amarillo">Inicio</a></li>
                    <li><a href="{{ route('products.index') }}" class="transition hover:text-marca-amarillo">Productos</a></li>
                    <li><a href="{{ route('workshop.index') }}" class="transition hover:text-marca-amarillo">Taller</a></li>
                    <li><a href="{{ route('cart.index') }}" class="transition hover:text-marca-amarillo">Mi carrito</a></li>
                    <li><a href="{{ url('/#sobre-nosotros') }}" class="transition hover:text-marca-amarillo">Sobre nosotros</a></li>
                </ul>
            </div>

            {{-- Contacto --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-marca-amarillo">Contacto</h3>
                <ul class="mt-4 space-y-2.5 text-sm text-marca-blanco/70">
                    @if ($settings->direccion)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @if ($settings->maps_url)
                                <a href="{{ $settings->maps_url }}" target="_blank" rel="noopener" class="transition hover:text-marca-amarillo">{{ $settings->direccion }}</a>
                            @else
                                <span>{{ $settings->direccion }}</span>
                            @endif
                        </li>
                    @endif
                    @if ($settings->telefono)
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-marca-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="{{ $settings->tel_link }}" class="transition hover:text-marca-amarillo">{{ $settings->telefono }}</a>
                        </li>
                    @endif
                    @if ($settings->email)
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-marca-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:{{ $settings->email }}" class="transition hover:text-marca-amarillo">{{ $settings->email }}</a>
                        </li>
                    @endif
                    @if ($settings->horario_atencion)
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-marca-amarillo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $settings->horario_atencion }}</span>
                        </li>
                    @endif
                    @if (! $settings->direccion && ! $settings->telefono && ! $settings->email && ! $settings->horario_atencion)
                        <li class="text-marca-blanco/50">Próximamente</li>
                    @endif
                </ul>
            </div>

            {{-- Ayuda / WhatsApp --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-marca-amarillo">¿Necesitás ayuda?</h3>
                <p class="mt-4 text-sm leading-relaxed text-marca-blanco/70">
                    Escribinos y te ayudamos a encontrar la pieza justa para tu moto.
                </p>
                @if ($settings->whatsapp_link)
                    <a href="{{ $settings->whatsapp_link }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-full bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:-translate-y-0.5 hover:bg-marca-blanco">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5 14.4c-.3-.1-1.7-.8-1.9-.9-.3-.1-.4-.1-.6.1-.2.3-.7.9-.8 1-.2.2-.3.2-.5.1-.3-.1-1.1-.4-2.1-1.3-.8-.7-1.3-1.6-1.5-1.8-.2-.3 0-.4.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.3-.4.1-.2 0-.4 0-.5-.1-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.5h-.5c-.2 0-.5.1-.7.3-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3 4.8 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.7-.7 1.9-1.4.2-.6.2-1.2.2-1.3-.1-.1-.3-.2-.6-.3z"/><path d="M12 2a10 10 0 00-8.5 15.2L2 22l4.9-1.5A10 10 0 1012 2zm0 18a8 8 0 01-4.3-1.2l-.3-.2-3 .9.9-2.9-.2-.3A8 8 0 1112 20z"/></svg>
                        WhatsApp
                    </a>
                @endif
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
