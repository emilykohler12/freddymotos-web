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
            <h1 class="text-lg font-bold text-marca-negro">Ingresá el código</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">Revisá tu correo y cargá el código de 6 dígitos junto con tu nueva contraseña.</p>

            @if (session('status'))
                <div class="mt-4 rounded-lg bg-marca-amarillo/20 px-3 py-2 text-sm font-medium text-marca-negro">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-marca-rojo/10 px-3 py-2 text-sm font-medium text-marca-rojo">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="code" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Código de 6 dígitos</label>
                    <input type="text" id="code" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" required
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-center text-lg font-bold tracking-[0.4em] focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nueva contraseña</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Repetir contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Cambiar contraseña
                </button>
            </form>

            <p class="mt-4 text-center text-xs">
                <a href="{{ route('password.request') }}" class="font-semibold text-marca-gris-oscuro hover:text-marca-rojo">Pedir un código nuevo</a>
            </p>
        </div>

        <p class="mt-5 text-center text-xs text-marca-blanco/40">
            <a href="{{ route('home') }}" class="transition hover:text-marca-blanco/70">← Volver al sitio</a>
        </p>
    </div>

</body>
</html>
