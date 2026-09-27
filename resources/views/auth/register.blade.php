@extends('layouts.app')

@section('title', 'Crear cuenta — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full min-h-screen bg-marca-gris-claro">
        <div class="mx-auto flex max-w-md flex-col items-center px-4 py-16 text-center sm:px-6">
            <a href="{{ route('home') }}" class="mb-6 self-start rounded-lg hover:bg-marca-gris-claro/50 p-2 transition">
                <svg class="h-6 w-6 text-marca-negro" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            @if ($settings->logo_url)
                <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-6 h-16 w-auto object-contain">
            @endif

            <h1 class="text-2xl font-extrabold tracking-tight text-marca-negro">Crear cuenta</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">¿Ya tenés cuenta? <a href="{{ route('login') }}" class="font-semibold text-marca-rojo hover:underline">Ingresá</a></p>

            @if ($errors->any())
                <div class="mt-5 w-full rounded-lg bg-marca-rojo/10 px-3 py-2 text-left text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="mt-6 w-full space-y-4 rounded-2xl bg-marca-blanco p-6 text-left shadow-sm">
                @csrf
                <div>
                    <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="ejemplo@gmail.com"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="phone" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Teléfono</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                           placeholder="+54 9 3515 123456"
                           inputmode="numeric"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40"
                           onkeypress="return /[0-9+\s-()]/i.test(event.key)">
                    <p class="mt-1 text-xs text-marca-gris-oscuro">Incluye código de país</p>
                </div>
                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 pr-10 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute right-3 top-2.5 text-marca-gris-oscuro hover:text-marca-negro">
                            <svg id="password-eye" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Repetir contraseña</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 pr-10 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute right-3 top-2.5 text-marca-gris-oscuro hover:text-marca-negro">
                            <svg id="password_confirmation-eye" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <script>
                    function togglePasswordVisibility(id) {
                        const field = document.getElementById(id);
                        const eye = document.getElementById(id + '-eye');
                        if (field.type === 'password') {
                            field.type = 'text';
                            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.604-1.888A3.375 3.375 0 1023.25 12a3.375 3.375 0 00-6.364-1.875zM12 12.75v.375A2.625 2.625 0 109.375 12h.375m0 0H12m0 0v.375"></path><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"></path>';
                        } else {
                            field.type = 'password';
                            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                        }
                    }
                </script>
                <button type="submit" class="w-full rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Crear cuenta
                </button>
            </form>
        </div>
    </main>

@endsection
