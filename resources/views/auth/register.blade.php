@extends('layouts.app')

@section('title', 'Crear cuenta — ' . $settings->nombre_local)

@section('content')
    <x-site-nav show-logo />

    <main class="w-full bg-marca-gris-claro">
        <div class="mx-auto flex max-w-md flex-col px-4 py-16 sm:px-6">
            <h1 class="text-2xl font-extrabold tracking-tight text-marca-negro">Crear cuenta</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">¿Ya tenés cuenta? <a href="{{ route('login') }}" class="font-semibold text-marca-rojo hover:underline">Ingresá</a></p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg bg-marca-rojo/10 px-3 py-2 text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4 rounded-2xl bg-marca-blanco p-6 shadow-sm">
                @csrf
                <div>
                    <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nombre</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Repetir contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <button type="submit" class="w-full rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Crear cuenta
                </button>
            </form>
        </div>
    </main>

@endsection
