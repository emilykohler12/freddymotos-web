@extends('layouts.admin')

@section('title', 'Movimientos')
@section('page-heading', 'Movimientos y notificaciones')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
    @endphp


    {{-- Tabs: radios, labels y paneles como hermanos directos (así el CSS peer-checked --}}
    {{-- funciona tanto para resaltar el tab activo como para mostrar/ocultar el panel). --}}
    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="activity-tab" id="tab-notificaciones" class="peer/notificaciones hidden" checked>
        <label for="tab-notificaciones" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/notificaciones:bg-marca-negro peer-checked/notificaciones:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notificaciones
        </label>

        <input type="radio" name="activity-tab" id="tab-ingresos" class="peer/ingresos hidden">
        <label for="tab-ingresos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/ingresos:bg-marca-negro peer-checked/ingresos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Otros ingresos
        </label>

    {{-- Notificaciones: stock + feed de movimientos --}}
    <div class="hidden w-full pt-4 peer-checked/notificaciones:block">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                @if ($outOfStock->isNotEmpty() || $lowStock->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($outOfStock as $product)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-rojo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span class="text-marca-negro"><strong>{{ $product->name }}</strong> está sin stock.</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-rojo hover:underline">Reponer</a>
                            </div>
                        @endforeach
                        @foreach ($lowStock as $product)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-mostaza/15 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-mostaza" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span class="text-marca-negro"><strong>{{ $product->name }}</strong> tiene poco stock ({{ $product->stock }}).</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-negro hover:underline">Reponer</a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    @if ($logs->isEmpty())
                        <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay movimientos registrados.</p>
                    @else
                        <ul class="divide-y divide-marca-gris-claro">
                            @foreach ($logs as $log)
                                <li class="flex items-start gap-4 px-5 py-4">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-marca-gris-claro text-xs font-bold uppercase text-marca-gris-oscuro">
                                        {{ Str::substr($log->type, 0, 1) }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-marca-negro">{{ $log->description }}</p>
                                        <p class="mt-0.5 text-xs text-marca-gris-oscuro">
                                            {{ ucfirst($log->type) }} · {{ $log->user->name ?? 'Sistema' }} · {{ $log->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>{{ $logs->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Otros ingresos: registrar + categorías --}}
    <div class="hidden w-full pt-4 peer-checked/ingresos:block">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Registrar otro ingreso</h2>
                <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="type" value="ingreso">
                    <input type="text" name="description" placeholder="Descripción" required class="{{ $field }}">
                    <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                    <select name="expense_category_id" class="{{ $field }}">
                        <option value="">Sin categoría</option>
                        @foreach ($ingresoCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="incurred_on" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                    <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                        Registrar ingreso
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Categorías de otros ingresos</h2>
                <ul class="mb-3 divide-y divide-marca-gris-claro text-sm">
                    @forelse ($ingresoCategories as $category)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-marca-negro">{{ $category->name }}</span>
                            <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-marca-gris-oscuro">Sin categorías todavía.</li>
                    @endforelse
                </ul>
                <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="type" value="ingreso">
                    <input type="text" name="name" placeholder="Nueva categoría" required class="{{ $field }}">
                    <button type="submit" class="shrink-0 rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Crear</button>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
