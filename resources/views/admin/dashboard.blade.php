@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    @php
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $cards = [
            ['label' => 'Ventas', 'value' => $money($salesTotal), 'bg' => 'bg-marca-mostaza', 'text' => 'text-marca-blanco',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'href' => route('admin.sales.index')],
            ['label' => 'Ingresos', 'value' => $money($revenue), 'bg' => 'bg-marca-amarillo', 'text' => 'text-marca-negro',
                'icon' => 'M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z', 'href' => route('admin.sales.index', ['tab' => 'ingresos'])],
            ['label' => 'Deuda de mecánicos', 'value' => $money($mechanicsTotalDebt), 'bg' => 'bg-marca-negro', 'text' => 'text-marca-blanco',
                'icon' => 'M9 5h6a2 2 0 012 2v12l-5-3-5 3V7a2 2 0 012-2z', 'href' => route('admin.workshop.index', ['tab' => 'mecanicos'])],
            ['label' => 'Gastos', 'value' => $money($expensesTotal), 'bg' => 'bg-marca-bordo', 'text' => 'text-marca-blanco',
                'icon' => 'M3 10h18M7 15h4m-4 0v.01M3 6h18v12H3z', 'href' => route('admin.expenses.index')],
        ];
    @endphp

    {{-- Filtro de período --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($periods as $value => $label)
            <a href="{{ route('admin.dashboard', ['period' => $value]) }}"
               class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $period === $value ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Tarjetas de KPIs --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <a href="{{ $card['href'] ?? '#' }}" class="relative overflow-hidden rounded-2xl {{ $card['bg'] }} p-5 shadow-sm transition hover:shadow-lg hover:scale-105">
                <svg class="pointer-events-none absolute -bottom-3 -right-3 h-20 w-20 opacity-10 {{ $card['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                </svg>
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-marca-blanco/15 {{ $card['text'] }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                    </svg>
                </span>
                <p class="relative mt-3 text-sm {{ $card['text'] }} opacity-80">{{ $card['label'] }}</p>
                <p class="relative mt-1 text-2xl font-extrabold {{ $card['text'] }}">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Grid principal de gráficos (9 elementos) --}}
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
        {{-- 1. Ingresos vs gastos --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5 lg:col-span-2 xl:col-span-3">
            <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Ingresos vs gastos · {{ $periods[$period] }}</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div class="mt-4 h-72 w-full">
                <canvas data-chart="{{ json_encode($incomeVsExpensesChart) }}"></canvas>
            </div>
        </div>

        {{-- 2. Pagos pendientes de gastos --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.expenses.index', ['tab' => 'pendientes']) }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Gastos pendientes</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($pendingExpensePayments->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm divide-y divide-marca-gris-claro">
                    @foreach ($pendingExpensePayments->take(5) as $group)
                        @forelse ($group as $expense)
                            <li class="py-2 flex justify-between">
                                <span class="text-marca-gris-oscuro">{{ $expense->description }}</span>
                                <span class="font-semibold text-marca-negro">{{ $money($expense->amount) }}</span>
                            </li>
                        @empty
                        @endforelse
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay gastos pendientes.</p>
            @endif
        </div>

        {{-- 3. Pagos pendientes en ventas --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Pedidos sin pago</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($pendingOrderPayments->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm divide-y divide-marca-gris-claro">
                    @foreach ($pendingOrderPayments->sortBy('created_at')->take(5) as $order)
                        <li class="py-2 flex justify-between">
                            <div>
                                <span class="text-marca-negro font-medium">{{ $order->customer->name ?? 'Cliente' }}</span>
                                <p class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                            <span class="font-semibold">{{ $money($order->total) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay pagos pendientes.</p>
            @endif
        </div>

        {{-- 4. Envío pendientes --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Envíos pendientes</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($pendingOrders->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm divide-y divide-marca-gris-claro">
                    @foreach ($pendingOrders->sortBy('created_at')->take(5) as $order)
                        <li class="py-2 flex justify-between">
                            <div>
                                <span class="text-marca-negro font-medium">{{ $order->customer->name ?? 'Cliente' }}</span>
                                <p class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay envíos pendientes.</p>
            @endif
        </div>

        {{-- 5. Gastos por categorías --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.expenses.index') }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Gastos por categoría</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($hasExpensesByCategory)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($expensesByCategoryChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay gastos registrados.</p>
            @endif
        </div>

        {{-- 6. Deuda mecánicos detallado --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.workshop.index', ['tab' => 'mecanicos']) }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Deuda mecánicos</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($hasMechanicsDebt)
                <ul class="mt-4 space-y-2 text-sm divide-y divide-marca-gris-claro">
                    @foreach ($mechanicsDebt->take(5) as $mechanic)
                        <li class="py-2 flex justify-between">
                            <span class="text-marca-negro">{{ $mechanic['name'] }}</span>
                            <span class="font-semibold text-marca-rojo">{{ $money($mechanic['total']) }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">Sin deudas.</p>
            @endif
        </div>

        {{-- 7. Stock bajo --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.products.index', ['tab' => 'stock']) }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Stock bajo/sin stock</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($outOfStock->isNotEmpty() || $lowStock->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm divide-y divide-marca-gris-claro">
                    @foreach ($outOfStock->take(3) as $product)
                        <li class="py-2 flex justify-between items-center">
                            <span class="text-marca-negro">{{ $product->name }}</span>
                            <span class="rounded bg-marca-rojo/10 px-2 py-1 text-xs font-semibold text-marca-rojo">Sin stock</span>
                        </li>
                    @endforeach
                    @foreach ($lowStock->take(2) as $product)
                        <li class="py-2 flex justify-between items-center">
                            <span class="text-marca-negro">{{ $product->name }}</span>
                            <span class="text-xs font-semibold text-marca-mostaza">{{ $product->stock }} ud</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">Stock suficiente.</p>
            @endif
        </div>

        {{-- 8. Repuestos más vendidos --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.products.index', ['tab' => 'repuestos']) }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Repuestos top 5</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($hasTopProducts)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($topProductsChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">Sin ventas.</p>
            @endif
        </div>

        {{-- 9. Marcas más vendidas --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <a href="{{ route('admin.products.index', ['tab' => 'repuestos']) }}" class="flex items-center justify-between hover:text-marca-rojo transition">
                <h2 class="text-sm font-bold text-marca-negro">Marcas top 5</h2>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @if ($hasTopBrands)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($topBrandsChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">Sin ventas.</p>
            @endif
        </div>
    </div>
@endsection
