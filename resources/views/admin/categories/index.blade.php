@extends('layouts.admin')

@section('title', 'Categorías')
@section('page-heading', 'Categorías')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
    @endphp

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">{{ session('error') }}</div>
    @endif

    {{-- Crear --}}
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mb-6 flex max-w-xl gap-3 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
        @csrf
        <input type="text" name="name" placeholder="Nombre de la nueva categoría" required class="{{ $field }}">
        <button type="submit" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            Crear
        </button>
    </form>

    {{-- Listado, orden A-Z --}}
    @if ($categories->isEmpty())
        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
            Todavía no hay categorías.
        </p>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($categories as $category)
                <details class="group rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <span class="font-medium text-marca-negro">{{ $category->name }}</span>
                        <span class="flex items-center gap-2">
                            <span class="rounded-full bg-marca-gris-claro px-2.5 py-1 text-xs font-semibold text-marca-gris-oscuro">
                                {{ $category->products_count }} {{ Str::plural('producto', $category->products_count) }}
                            </span>
                            <svg class="h-4 w-4 shrink-0 text-marca-gris-oscuro transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </summary>

                    <div class="mt-4 space-y-2">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $category->name }}" required class="{{ $field }}">
                            <button type="submit" class="shrink-0 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-xs font-semibold text-marca-negro transition hover:border-marca-amarillo">
                                Guardar
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg border border-marca-rojo/30 px-3 py-2 text-xs font-semibold text-marca-rojo transition hover:bg-marca-rojo hover:text-marca-blanco">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </details>
            @endforeach
        </div>
    @endif
@endsection
