@extends('layouts.admin')

@section('title', 'Gastos')
@section('page-heading', 'Gastos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex items-center gap-2">
            <label for="frequency" class="text-sm text-marca-gris-oscuro">Filtrar por frecuencia:</label>
            <select id="frequency" name="frequency" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                <option value="">Todas</option>
                @foreach ($frequencies as $value => $label)
                    <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <p class="text-sm text-marca-gris-oscuro">
            Total del mes: <strong class="text-marca-negro">{{ $money($totalThisMonth) }}</strong>
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            {{-- Un bloque por categoría, con un "+" para cargar un gasto directo en esa categoría --}}
            @foreach ($byCategory as $group)
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-marca-negro">{{ $group['category']->name }}</h2>
                        <label for="add-cat-{{ $group['category']->id }}" class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-full bg-marca-amarillo text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco" title="Añadir gasto en {{ $group['category']->name }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </label>
                    </div>

                    <input type="checkbox" id="add-cat-{{ $group['category']->id }}" class="peer/cat{{ $group['category']->id }} hidden">
                    <form method="POST" action="{{ route('admin.expenses.store') }}" class="mb-4 hidden gap-3 rounded-2xl bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5 peer-checked/cat{{ $group['category']->id }}:grid sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="type" value="gasto">
                        <input type="hidden" name="expense_category_id" value="{{ $group['category']->id }}">
                        <input type="text" name="description" placeholder="Descripción" required class="{{ $field }} sm:col-span-2">
                        <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                        <select name="frequency" class="{{ $field }}">
                            <option value="">Sin frecuencia</option>
                            @foreach ($frequencies as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="incurred_on" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                        <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Guardar</button>
                    </form>

                    @if ($group['items']->isEmpty())
                        <p class="rounded-2xl bg-marca-blanco px-5 py-6 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            Sin gastos en esta categoría.
                        </p>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($group['items'] as $item)
                                @include('admin.expenses._card', ['item' => $item])
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if ($withoutCategory->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Sin categoría</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($withoutCategory as $item)
                            @include('admin.expenses._card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($byCategory->isEmpty() && $withoutCategory->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Todavía no hay gastos registrados.
                </p>
            @endif

            {{-- Desactivados: pagos únicos o anuales ya pagados --}}
            @if ($deactivated->isNotEmpty())
                <div>
                    <input type="checkbox" id="show-deactivated" class="peer hidden">
                    <label for="show-deactivated" class="flex cursor-pointer items-center gap-2 text-sm font-bold uppercase tracking-wide text-marca-gris-oscuro hover:text-marca-negro">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        Desactivados ({{ $deactivated->count() }})
                    </label>
                    <div class="mt-3 hidden grid-cols-1 gap-4 opacity-60 peer-checked:grid sm:grid-cols-2">
                        @foreach ($deactivated as $item)
                            @include('admin.expenses._card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Nuevo gasto (sin categoría específica) --}}
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Nuevo gasto</h2>
                <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="type" value="gasto">
                    <input type="text" name="description" placeholder="Descripción" required class="{{ $field }}">
                    <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                    <select name="expense_category_id" class="{{ $field }}">
                        <option value="">Sin categoría</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="frequency" class="{{ $field }}">
                        <option value="">Frecuencia…</option>
                        @foreach ($frequencies as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="incurred_on" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                    <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                        Registrar
                    </button>
                </form>
            </div>

            {{-- Categorías --}}
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Categorías de gastos</h2>
                <ul class="mb-3 divide-y divide-marca-gris-claro text-sm">
                    @forelse ($categories as $category)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-marca-negro">{{ $category->name }}</span>
                            <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?');" class="inline-flex">
                                @csrf @method('DELETE')
                                <input type="hidden" name="type" value="gasto">
                                <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-marca-gris-oscuro">Sin categorías todavía.</li>
                    @endforelse
                </ul>
                <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="type" value="gasto">
                    <input type="text" name="name" placeholder="Nueva categoría" required class="{{ $field }}">
                    <button type="submit" class="shrink-0 rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Crear</button>
                </form>
            </div>
        </div>
    </div>
@endsection
