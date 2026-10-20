@extends('layouts.admin')

@section('title', 'Seguridad')
@section('page-heading', 'Seguridad de la cuenta')

@section('content')
    <div class="max-w-xl space-y-6">
        @if ($errors->any())
            <div class="rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
        @endif

        @if ($recoveryCodes)
            <div class="rounded-2xl bg-marca-blanco p-6 shadow-sm">
                <h2 class="text-base font-bold">Guardá tus códigos de recuperación</h2>
                <p class="mt-1 text-sm text-marca-gris-oscuro">Cada uno sirve una sola vez si perdés el celular. Se muestran solo ahora: copialos a un lugar seguro.</p>
                <ul class="mt-4 grid grid-cols-2 gap-2 font-mono text-sm">
                    @foreach ($recoveryCodes as $code)
                        <li class="rounded bg-marca-gris-claro px-3 py-2">{{ $code }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($enabled)
            <div class="rounded-2xl bg-marca-blanco p-6 shadow-sm">
                <h2 class="text-base font-bold">Doble factor activo</h2>
                <p class="mt-1 text-sm text-marca-gris-oscuro">Al ingresar al panel se te pide el código de tu app autenticadora.</p>
            </div>
        @else
            <div class="rounded-2xl bg-marca-blanco p-6 shadow-sm">
                <h2 class="text-base font-bold">Activar doble factor (obligatorio para el panel)</h2>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-marca-gris-oscuro">
                    <li>Instalá Google Authenticator, Microsoft Authenticator o Authy en tu celular.</li>
                    <li>Agregá una cuenta nueva con “ingresar clave de configuración” y escribí esta clave (tipo: basada en tiempo):</li>
                </ol>
                <p class="mt-3 select-all break-all rounded bg-marca-gris-claro px-3 py-2 font-mono text-sm font-bold tracking-wider">{{ $secret }}</p>
                <p class="mt-2 text-xs text-marca-gris-oscuro">Desde el celular también podés tocar <a class="font-semibold text-marca-rojo underline" href="{{ $otpUri }}">este enlace</a> para agregarla directo.</p>

                <form method="POST" action="{{ route('admin.two-factor.confirm') }}" class="mt-5 space-y-3">
                    @csrf
                    <label for="code" class="block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Código de 6 dígitos que muestra la app</label>
                    <input type="text" id="code" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                    <button type="submit" class="rounded-full bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Activar</button>
                </form>
            </div>
        @endif
    </div>
@endsection
