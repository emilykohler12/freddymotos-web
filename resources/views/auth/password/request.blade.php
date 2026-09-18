<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar contraseña · {{ $settings->nombre_local ?? 'Freddy Motos' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-marca-negro px-4 font-sans text-marca-negro antialiased">

    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <span class="text-2xl font-extrabold tracking-tight text-marca-blanco">
                {{ \Illuminate\Support\Str::of($settings->nombre_local ?? 'Freddy Motos')->upper() }}
            </span>
            <p class="mt-1 text-sm text-marca-blanco/50">Recuperar contraseña</p>
        </div>

        <div class="rounded-2xl bg-marca-blanco p-7 shadow-xl">
            <h1 class="text-lg font-bold text-marca-negro">¿Olvidaste tu contraseña?</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">Ingresá tu email y te mandamos un código de 6 dígitos para recuperarla.</p>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-marca-rojo/10 px-3 py-2 text-sm font-medium text-marca-rojo">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.send-code') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Enviar código
                </button>
            </form>

            <p class="mt-4 text-center text-xs">
                <a href="{{ route('password.code-form') }}" class="font-semibold text-marca-gris-oscuro hover:text-marca-rojo">Ya tengo un código</a>
            </p>
        </div>

        <p class="mt-5 text-center text-xs text-marca-blanco/40">
            <a href="{{ route('home') }}" class="transition hover:text-marca-blanco/70">← Volver al sitio</a>
        </p>
    </div>

</body>
</html>
