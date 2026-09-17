@extends('layouts.app')

@section('title', 'Categorías — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">

            <div class="mb-10">
                <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">Catálogo</p>
                <h1 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">Todas las categorías</h1>
            </div>

            @if ($categories->isEmpty())
                <p class="rounded-2xl bg-marca-gris-claro px-6 py-16 text-center text-sm text-marca-gris-oscuro">
                    Todavía no hay categorías cargadas.
                </p>
            @else
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('products.index', ['category' => $category->name]) }}"
                           class="group flex flex-col items-center gap-3 rounded-2xl border-2 border-transparent bg-marca-gris-claro p-6 text-center transition
                                  hover:border-marca-amarillo hover:bg-marca-blanco hover:shadow-lg">
                            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-marca-blanco text-marca-negro shadow-sm transition group-hover:bg-marca-amarillo">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 11.5L12 3 3.5 11.5M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
                                </svg>
                            </span>
                            <span class="text-sm font-semibold text-marca-negro">{{ $category->name }}</span>
                            <span class="text-xs text-marca-gris-oscuro">{{ $category->products_count }} {{ Str::plural('producto', $category->products_count) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

@endsection
