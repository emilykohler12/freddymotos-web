@extends('layouts.app')

@section('title', 'Taller — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="w-full px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">Taller</h1>
                <p class="mt-2 max-w-xl text-sm text-marca-gris-oscuro">
                    Además de repuestos, ofrecemos estos servicios de taller para tu moto.
                </p>
            </div>

            @if ($categories->isEmpty())
                <p class="rounded-2xl bg-marca-gris-claro px-6 py-16 text-center text-sm text-marca-gris-oscuro">
                    Todavía no hay servicios cargados. Dejanos tu consulta igual, te contactamos.
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($categories as $categoria)
                        <div class="rounded-2xl bg-marca-gris-claro p-5">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-marca-amarillo/20 text-marca-negro">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3a1 1 0 011.4 0l1 1a1 1 0 001 .3l1.4-.2a1 1 0 011 .6l.6 1.3a1 1 0 00.7.6l1.4.4a1 1 0 01.7 1.2l-.3 1.4a1 1 0 00.2 1l1 1a1 1 0 010 1.4l-1 1a1 1 0 00-.3 1l.2 1.4a1 1 0 01-.6 1l-1.3.6a1 1 0 00-.6.7l-.4 1.4a1 1 0 01-1.2.7l-1.4-.3a1 1 0 00-1 .2l-1 1a1 1 0 01-1.4 0l-1-1a1 1 0 00-1-.3l-1.4.2a1 1 0 01-1-.6l-.6-1.3a1 1 0 00-.7-.6l-1.4-.4a1 1 0 01-.7-1.2l.3-1.4a1 1 0 00-.2-1l-1-1a1 1 0 010-1.4l1-1a1 1 0 00.3-1L4.3 8a1 1 0 01.6-1l1.3-.6a1 1 0 00.6-.7l.4-1.4zM12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
                            </span>
                            <h3 class="mt-3 text-base font-bold text-marca-negro">{{ $categoria->name }}</h3>
                            @if ($categoria->description)
                                <p class="mt-1 text-sm text-marca-gris-oscuro">{{ $categoria->description }}</p>
                            @endif
                            @if ($categoria->formatted_price)
                                <p class="mt-2 text-sm font-bold text-marca-rojo">Desde {{ $categoria->formatted_price }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
@endsection
