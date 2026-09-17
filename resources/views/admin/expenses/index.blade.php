@extends('layouts.admin')

@section('title', 'Gastos')
@section('page-heading', 'Gastos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-marca-amarillo/15 px-4 py-3 text-sm font-medium text-marca-negro">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">{{ session('error') }}</div>
    @endif

    {{-- Tabs Gastos / Otros ingresos --}}
    <div class="mb-6 flex gap-2">
        <a href="{{ route('admin.expenses.index', ['type' => 'gasto']) }}"
           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $type === 'gasto' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            Gastos
        </a>
        <a href="{{ route('admin.expenses.index', ['type' => 'ingreso']) }}"
           class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $type === 'ingreso' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            Otros ingresos
        </a>
        <span class="ml-auto self-center text-sm text-marca-gris-oscuro">
            Total del mes: <strong class="text-marca-negro">{{ $money($totalThisMonth) }}</strong>
        </span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Listado en tarjetas (como el mockup) --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @forelse ($items as $item)
                    <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <p class="font-bold text-marca-negro">{{ $item->description }}</p>
                        <dl class="mt-2 space-y-1 text-sm text-marca-gris-oscuro">
                            <div>Categoría: <span class="font-medium text-marca-negro">{{ $item->expenseCategory->name ?? $item->category ?? '—' }}</span></div>
                            <div>Monto: <span class="font-medium text-marca-negro">{{ $money($item->amount) }}</span></div>
                            <div>Frecuencia: <span class="font-medium text-marca-negro">{{ $frequencies[$item->frequency] ?? '—' }}</span></div>
                            <div>Fecha: <span class="font-medium text-marca-negro">{{ $item->incurred_on->format('d/m/Y') }}</span></div>
                        </dl>
                        <div class="mt-3 flex gap-3 border-t border-marca-gris-claro pt-3">
                            <details>
                                <summary class="cursor-pointer text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</summary>
                                <form method="POST" action="{{ route('admin.expenses.update', $item) }}" class="mt-3 space-y-2">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <input type="text" name="description" value="{{ $item->description }}" required class="{{ $field }}">
                                    <input type="number" step="0.01" min="0" name="amount" value="{{ $item->amount }}" required class="{{ $field }}">
                                    <select name="expense_category_id" class="{{ $field }}">
                                        <option value="">Sin categoría</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected($item->expense_category_id === $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="frequency" class="{{ $field }}">
                                        <option value="">Sin frecuencia</option>
                                        @foreach ($frequencies as $value => $label)
                                            <option value="{{ $value }}" @selected($item->frequency === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="incurred_on" value="{{ $item->incurred_on->toDateString() }}" required class="{{ $field }}">
                                    <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Guardar</button>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('admin.expenses.destroy', $item) }}" onsubmit="return confirm('¿Eliminar este registro?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="col-span-2 rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        Todavía no hay {{ $type === 'gasto' ? 'gastos' : 'ingresos' }} registrados.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            {{-- Nuevo registro --}}
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Nuevo {{ $type === 'gasto' ? 'gasto' : 'ingreso' }}</h2>
                <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
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
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Categorías de {{ $type === 'gasto' ? 'gastos' : 'ingresos' }}</h2>
                <ul class="mb-3 divide-y divide-marca-gris-claro text-sm">
                    @forelse ($categories as $category)
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
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="text" name="name" placeholder="Nueva categoría" required class="{{ $field }}">
                    <button type="submit" class="shrink-0 rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Crear</button>
                </form>
            </div>
        </div>
    </div>
@endsection
