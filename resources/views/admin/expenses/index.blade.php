@extends('layouts.admin')

@section('title', 'Gastos')
@section('page-heading', 'Gastos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <p class="mb-6 text-sm text-marca-gris-oscuro">
        Total del mes: <strong class="text-marca-negro">{{ $money($totalThisMonth) }}</strong>
    </p>

    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="expenses-tab" id="tab-categoria" class="peer/categoria hidden" @checked($activeTab === 'categoria')>
        <label for="tab-categoria" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/categoria:bg-marca-negro peer-checked/categoria:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
            Categoría
        </label>

        <input type="radio" name="expenses-tab" id="tab-nuevo-gasto" class="peer/nuevogasto hidden" @checked($activeTab === 'nuevo-gasto')>
        <label for="tab-nuevo-gasto" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/nuevogasto:bg-marca-negro peer-checked/nuevogasto:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo gasto
        </label>

        <input type="radio" name="expenses-tab" id="tab-pendientes" class="peer/pendientes hidden" @checked($activeTab === 'pendientes')>
        <label for="tab-pendientes" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pendientes:bg-marca-negro peer-checked/pendientes:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pendientes a pagar
        </label>

        <input type="radio" name="expenses-tab" id="tab-pagadas" class="peer/pagadas hidden" @checked($activeTab === 'pagadas')>
        <label for="tab-pagadas" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pagadas:bg-marca-negro peer-checked/pagadas:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Pagadas
        </label>

        {{-- ===== Categoría ===== --}}
        <div class="hidden w-full pt-4 peer-checked/categoria:block">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Categorías existentes</h2>
                    <ul class="divide-y divide-marca-gris-claro rounded-2xl bg-marca-blanco px-5 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        @forelse ($categories as $category)
                            <li class="flex items-center justify-between py-3">
                                <span class="text-marca-negro">{{ $category->name }}</span>
                                <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?');" class="inline-flex">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="type" value="gasto">
                                    <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                </form>
                            </li>
                        @empty
                            <li class="py-6 text-center text-marca-gris-oscuro">Sin categorías todavía.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Nueva categoría</h2>
                    <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="type" value="gasto">
                        <input type="text" name="name" placeholder="Nombre de la categoría" required class="{{ $field }}">
                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Crear</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== Nuevo gasto ===== --}}
        <div class="hidden w-full pt-4 peer-checked/nuevogasto:block">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Registrar gasto</h2>
                    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="type" value="gasto">
                        <input type="hidden" name="tab" value="nuevo-gasto">
                        <input type="text" name="description" placeholder="Descripción" required class="{{ $field }}">
                        <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                        <select name="expense_category_id" class="{{ $field }}">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select name="frequency" required class="{{ $field }}">
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

                <div class="space-y-4 lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-sm font-bold text-marca-negro">Registro de gastos</h2>
                        <form method="GET" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="nuevo-gasto">
                            <label for="frequency" class="text-xs text-marca-gris-oscuro">Frecuencia:</label>
                            <select id="frequency" name="frequency" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs">
                                <option value="">Todas</option>
                                @foreach ($frequencies as $value => $label)
                                    <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    @if ($allExpenses->isEmpty())
                        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            Todavía no hay gastos registrados.
                        </p>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($allExpenses as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'nuevo-gasto'])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Pendientes a pagar (separadas por categoría) ===== --}}
        <div class="hidden w-full space-y-8 pt-4 peer-checked/pendientes:block">
            @foreach ($pendingByCategory as $group)
                @if ($group['items']->isNotEmpty())
                    <div>
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">{{ $group['category']->name }}</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($group['items'] as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pendientes'])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($pendingWithoutCategory->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Sin categoría</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($pendingWithoutCategory as $item)
                            @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pendientes'])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($pendingByCategory->every(fn ($g) => $g['items']->isEmpty()) && $pendingWithoutCategory->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay gastos pendientes de pago.
                </p>
            @endif
        </div>

        {{-- ===== Pagadas (separadas por categoría) ===== --}}
        <div class="hidden w-full space-y-8 pt-4 peer-checked/pagadas:block">
            @foreach ($paidByCategory as $group)
                @if ($group['items']->isNotEmpty())
                    <div>
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">{{ $group['category']->name }}</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($group['items'] as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pagadas'])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($paidWithoutCategory->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Sin categoría</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($paidWithoutCategory as $item)
                            @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pagadas'])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($paidByCategory->every(fn ($g) => $g['items']->isEmpty()) && $paidWithoutCategory->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Todavía no hay gastos pagados.
                </p>
            @endif
        </div>
    </div>
@endsection
