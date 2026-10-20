@extends('layouts.app')

@section('title', 'Verificación — ' . $settings->nombre_local)
@section('noindex', '1')

@section('content')
    <x-site-nav />

    <main class="w-full min-h-screen bg-marca-gris-claro">
        <div class="mx-auto flex max-w-md flex-col items-center px-4 py-16 text-center sm:px-6">
            <h1 class="text-2xl font-extrabold tracking-tight text-marca-negro">Verificación en dos pasos</h1>
            <p class="mt-1 text-sm text-marca-gris-oscuro">Ingresá el código de 6 dígitos de tu app autenticadora, o un código de recuperación.</p>

            @if ($errors->any())
                <div class="mt-5 w-full rounded-lg bg-marca-rojo/10 px-3 py-2 text-left text-sm font-medium text-marca-rojo">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 w-full space-y-4 rounded-2xl bg-marca-blanco p-6 text-left shadow-sm">
                @csrf
                <div>
                    <label for="code" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Código</label>
                    <input type="text" id="code" name="code" required autofocus autocomplete="one-time-code" inputmode="text"
                           class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                </div>
                <button type="submit" class="w-full rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Verificar
                </button>
            </form>
        </div>
    </main>
@endsection
