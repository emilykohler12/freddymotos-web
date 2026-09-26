<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar al panel · {{ $settings->nombre_local ?? 'Freddy Motos' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-marca-negro px-4 font-sans text-marca-negro antialiased">

    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <span class="text-2xl font-extrabold tracking-tight text-marca-blanco">
                {{ \Illuminate\Support\Str::of($settings->nombre_local ?? 'Freddy Motos')->upper() }}
            </span>
            <p class="mt-1 text-sm text-marca-blanco/50">Panel de administración</p>
        </div>

        <div class="rounded-2xl bg-marca-blanco p-7 shadow-xl">
            <h1 class="text-lg font-bold text-marca-negro">Ingresá a tu cuenta</h1>

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-marca-rojo/10 px-3 py-2 text-sm font-medium text-marca-rojo">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 pr-10 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-marca-gris-oscuro hover:text-marca-negro">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <script>
                    document.getElementById('togglePassword').addEventListener('click', function(e) {
                        e.preventDefault();
                        const passwordInput = document.getElementById('password');
                        const eyeIcon = document.getElementById('eyeIcon');

                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                        } else {
                            passwordInput.type = 'password';
                            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                        }
                    });
                </script>
                <div class="flex items-center justify-between text-sm text-marca-gris-oscuro">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-marca-gris-oscuro/30 accent-marca-rojo">
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-marca-rojo hover:underline">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Entrar
                </button>
            </form>
        </div>

        <p class="mt-5 text-center text-xs text-marca-blanco/40">
            <a href="{{ route('home') }}" class="transition hover:text-marca-blanco/70">← Volver al sitio</a>
        </p>
    </div>

</body>
</html>
