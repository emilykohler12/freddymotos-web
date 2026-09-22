@extends('layouts.admin')

@section('title', 'Configuración')
@section('page-heading', 'Configuración del negocio')

@section('content')
    <p class="mb-6 text-sm text-marca-gris-oscuro">
        Estos datos se muestran en el Home y en el pie de página del sitio público.
    </p>

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">
            {{ $errors->first() }}
        </div>
    @endif

    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
    @endphp

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tabs: radios, labels y paneles como hermanos directos (así el CSS peer-checked --}}
        {{-- funciona tanto para resaltar el tab activo como para mostrar/ocultar el panel). --}}
        {{-- El hidden "tab" recuerda cuál estaba abierta para que, al guardar, la redirección vuelva a la misma. --}}
        <input type="hidden" name="tab" id="active-tab-field" value="{{ request('tab', 'general') }}">
        <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="settings-tab" id="tab-general" class="peer/general hidden" @checked(request('tab', 'general') === 'general') onchange="document.getElementById('active-tab-field').value='general'; window.__syncSettingsTab && window.__syncSettingsTab('general')">
        <label for="tab-general" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/general:bg-marca-negro peer-checked/general:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            General
        </label>

        <input type="radio" name="settings-tab" id="tab-negocio" class="peer/negocio hidden" @checked(request('tab') === 'negocio') onchange="document.getElementById('active-tab-field').value='negocio'; window.__syncSettingsTab && window.__syncSettingsTab('negocio')">
        <label for="tab-negocio" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/negocio:bg-marca-negro peer-checked/negocio:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M4 21V9l8-5 8 5v12M9 21v-6h6v6"/></svg>
            Sobre el negocio
        </label>

        <input type="radio" name="settings-tab" id="tab-pagos" class="peer/pagos hidden" @checked(request('tab') === 'pagos') onchange="document.getElementById('active-tab-field').value='pagos'; window.__syncSettingsTab && window.__syncSettingsTab('pagos')">
        <label for="tab-pagos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pagos:bg-marca-negro peer-checked/pagos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18v10H3V7zm0 4h18M7 15h4"/></svg>
            Pagos
        </label>

        {{-- ===== General ===== --}}
        <div class="hidden w-full space-y-6 pt-4 peer-checked/general:block">
            <div class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div>
                    <label for="nombre_local" class="{{ $lbl }}">Nombre del local</label>
                    <input type="text" id="nombre_local" name="nombre_local" value="{{ old('nombre_local', $settings->nombre_local) }}" class="{{ $field }}">
                </div>

                <div>
                    <label for="logo" class="{{ $lbl }}">Logo <span class="normal-case text-marca-gris-oscuro/50">(se muestra en el login, el registro y la página de inicio)</span></label>
                    @if ($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-2 h-14 w-auto rounded-lg bg-marca-gris-claro object-contain p-1">
                        <label class="mb-2 flex items-center gap-2 text-sm text-marca-gris-oscuro">
                            <input type="checkbox" name="remove_logo" value="1" class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-rojo focus:ring-marca-rojo">
                            Eliminar el logo actual
                        </label>
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*" class="{{ $field }}">
                </div>

                <div>
                    <label for="hero_photo" class="{{ $lbl }}">Foto principal <span class="normal-case text-marca-gris-oscuro/50">(se muestra bien arriba en la página de inicio)</span></label>
                    @if ($settings->hero_photo_url)
                        <img src="{{ $settings->hero_photo_url }}" alt="Foto principal" class="mb-2 h-24 w-auto rounded-lg bg-marca-gris-claro object-cover p-1">
                        <label class="mb-2 flex items-center gap-2 text-sm text-marca-gris-oscuro">
                            <input type="checkbox" name="remove_hero_photo" value="1" class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-rojo focus:ring-marca-rojo">
                            Eliminar la foto actual
                        </label>
                    @endif
                    <input type="file" id="hero_photo" name="hero_photo" accept="image/*" class="{{ $field }}">
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="telefono" class="{{ $lbl }}">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $settings->telefono) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="whatsapp" class="{{ $lbl }}">WhatsApp <span class="normal-case text-marca-gris-oscuro/50">(solo números, con código de país)</span></label>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $settings->whatsapp) }}" placeholder="5493510000000" class="{{ $field }}">
                    </div>
                </div>

                <div>
                    <label for="email" class="{{ $lbl }}">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $settings->email) }}" class="{{ $field }}">
                </div>

                <div>
                    <label for="direccion" class="{{ $lbl }}">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $settings->direccion) }}" class="{{ $field }}">
                </div>

                <div>
                    <label for="horario_atencion" class="{{ $lbl }}">Horario de atención</label>
                    <input type="text" id="horario_atencion" name="horario_atencion" value="{{ old('horario_atencion', $settings->horario_atencion) }}" placeholder="Lunes a viernes de 9 a 18" class="{{ $field }}">
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="instagram_url" class="{{ $lbl }}">Instagram (URL)</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}" placeholder="https://instagram.com/..." class="{{ $field }}">
                    </div>
                    <div>
                        <label for="facebook_url" class="{{ $lbl }}">Facebook (URL)</label>
                        <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}" placeholder="https://facebook.com/..." class="{{ $field }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Sobre el negocio ===== --}}
        <div class="hidden w-full space-y-6 pt-4 peer-checked/negocio:block">
            <div class="space-y-4 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div>
                    <label for="fecha_creacion" class="{{ $lbl }}">Fecha de creación del negocio</label>
                    <input type="date" id="fecha_creacion" name="fecha_creacion" value="{{ old('fecha_creacion', optional($settings->fecha_creacion)->toDateString()) }}" class="max-w-xs {{ $field }}">
                </div>
                <div>
                    <label for="historia" class="{{ $lbl }}">Historia <span class="normal-case text-marca-gris-oscuro/50">(se muestra en "Sobre nosotros")</span></label>
                    <textarea id="historia" name="historia" rows="4" maxlength="2000" class="{{ $field }}">{{ old('historia', $settings->historia) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===== Pagos ===== --}}
        <div class="hidden w-full space-y-6 pt-4 peer-checked/pagos:block">
            <div class="rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Moneda e impuestos</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="moneda" class="{{ $lbl }}">Moneda</label>
                        <input type="text" id="moneda" name="moneda" value="{{ old('moneda', $settings->moneda) }}" placeholder="ARS" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="tax_rate" class="{{ $lbl }}">Impuestos <span class="normal-case text-marca-gris-oscuro/50">(% ej. IVA)</span></label>
                        <input type="number" step="0.01" min="0" max="100" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate) }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Métodos de pago</h2>
                <div class="flex flex-wrap gap-4">
                    @php $selectedMethods = old('payment_methods', $settings->payment_methods ?? []); @endphp
                    @foreach (['efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'tarjeta_debito' => 'Tarjeta débito', 'tarjeta_credito' => 'Tarjeta crédito'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                            <input type="checkbox" name="payment_methods[]" value="{{ $value }}" @checked(in_array($value, $selectedMethods)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3 border-t border-marca-gris-claro pt-5">
            <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                Guardar cambios
            </button>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
        </div>
    </form>

    {{-- Fotos del negocio: la más reciente se muestra en "Sobre nosotros" del Home. --}}
    {{-- Solo en la pestaña "Sobre el negocio". Este bloque tiene su propio <form> (sube un --}}
    {{-- archivo con otra acción) y no puede ir anidado dentro del <form> principal, así que
         el peer-checked de Tailwind no le llega (necesita ser hermano directo del radio). Por
         eso el show/hide de acá es a mano por JS, sincronizado con los mismos radios de arriba. --}}
    <div id="fotos-negocio-section" class="{{ request('tab') === 'negocio' ? '' : 'hidden' }} w-full">
        <div class="mt-6 space-y-4 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-marca-negro">Fotos del negocio</h2>
                <p class="mt-1 text-xs text-marca-gris-oscuro">La foto más reciente que subas es la que se muestra en "Sobre nosotros" del Home.</p>
            </div>

            @if ($photos->isNotEmpty())
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($photos as $photo)
                        <div class="group relative overflow-hidden rounded-xl ring-1 ring-marca-gris-oscuro/10">
                            <img src="{{ $photo->url }}" alt="Foto del negocio" class="aspect-square w-full object-cover">
                            @if ($loop->first)
                                <span class="absolute left-1.5 top-1.5 rounded-full bg-marca-amarillo px-2 py-0.5 text-[10px] font-bold text-marca-negro">En Sobre nosotros</span>
                            @endif
                            <form method="POST" action="{{ route('admin.settings.photos.destroy', $photo) }}" data-confirm="¿Eliminar esta foto?" class="absolute inset-x-0 bottom-0 bg-marca-negro/70 opacity-0 transition group-hover:opacity-100">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full py-1.5 text-xs font-semibold text-marca-blanco">Eliminar</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.photos.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                @csrf
                <input type="file" name="photo" accept="image/*" required class="{{ $field }}">
                <button type="submit" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Subir foto
                </button>
            </form>
        </div>
    </div>

    <script>
        window.__syncSettingsTab = function (tab) {
            var section = document.getElementById('fotos-negocio-section');
            if (!section) return;
            section.classList.toggle('hidden', tab !== 'negocio');
        };
    </script>
@endsection
