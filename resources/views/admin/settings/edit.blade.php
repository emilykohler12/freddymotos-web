@extends('layouts.admin')

@section('title', 'Configuración')
@section('page-heading', 'Configuración del negocio')

@section('content')
    <div class="max-w-3xl">
        <p class="mb-6 text-sm text-marca-gris-oscuro">
            Estos datos se muestran en el Home y en el pie de página del sitio público.
        </p>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            @method('PUT')

            @php
                $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
                $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
            @endphp

            <div>
                <label for="nombre_local" class="{{ $lbl }}">Nombre del local</label>
                <input type="text" id="nombre_local" name="nombre_local" value="{{ old('nombre_local', $settings->nombre_local) }}" class="{{ $field }}">
            </div>

            <div>
                <label for="logo" class="{{ $lbl }}">Logo</label>
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-2 h-14 w-auto rounded-lg bg-marca-gris-claro object-contain p-1">
                @endif
                <input type="file" id="logo" name="logo" accept="image/*" class="{{ $field }}">
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

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Sobre el negocio</h2>
                <div class="space-y-4">
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

            <div class="border-t border-marca-gris-claro pt-5">
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

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Métodos de pago</h2>
                <div class="flex flex-wrap gap-4">
                    @php $selectedMethods = old('payment_methods', $settings->payment_methods ?? []); @endphp
                    @foreach (['mercadopago' => 'Mercado Pago', 'efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'whatsapp' => 'Coordinar por WhatsApp'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                            <input type="checkbox" name="payment_methods[]" value="{{ $value }}" @checked(in_array($value, $selectedMethods)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Datos bancarios</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="banco" class="{{ $lbl }}">Banco</label>
                        <input type="text" id="banco" name="banco" value="{{ old('banco', $settings->banco) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="cbu_alias" class="{{ $lbl }}">CBU / Alias</label>
                        <input type="text" id="cbu_alias" name="cbu_alias" value="{{ old('cbu_alias', $settings->cbu_alias) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="titular_cuenta" class="{{ $lbl }}">Titular de la cuenta</label>
                        <input type="text" id="titular_cuenta" name="titular_cuenta" value="{{ old('titular_cuenta', $settings->titular_cuenta) }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Mercado Pago</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="mp_public_key" class="{{ $lbl }}">Public key</label>
                        <input type="text" id="mp_public_key" name="mp_public_key" value="{{ old('mp_public_key', $settings->mp_public_key) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="mp_access_token" class="{{ $lbl }}">Access token <span class="normal-case text-marca-gris-oscuro/50">{{ $settings->mp_access_token ? '(cargado — dejar vacío para no cambiarlo)' : '' }}</span></label>
                        <input type="password" id="mp_access_token" name="mp_access_token" autocomplete="new-password" placeholder="{{ $settings->mp_access_token ? '••••••••••••' : '' }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
