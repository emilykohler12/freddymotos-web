@extends('layouts.app')

@section('title', 'Ingresar — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full min-h-screen bg-marca-gris-claro">
        <div class="mx-auto flex max-w-md flex-col items-center px-4 py-16 text-center sm:px-6">
            @if ($settings->logo_url)
                <img src="{{ $settings->logo_url }}" alt="{{ $settings->nombre_local }}" class="mb-6 h-16 w-auto object-contain">
            @endif

            <h1 class="text-2xl font-extrabold tracking-tight text-marca-negro">Ingresá a tu cuenta</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">¿No tenés cuenta? <a href="{{ route('register') }}" class="font-semibold text-marca-rojo hover:underline">Registrate</a></p>

            @if ($errors->any())
                <div class="mt-5 w-full rounded-lg bg-marca-rojo/10 px-3 py-2 text-left text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 w-full space-y-4 rounded-2xl bg-marca-blanco p-6 text-left shadow-sm">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div class="flex items-center justify-between text-sm text-marca-gris-oscuro">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-marca-gris-oscuro/30 accent-marca-rojo"> Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-marca-rojo hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="w-full rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Entrar
                </button>
            </form>
        </div>
    </main>

@endsection
