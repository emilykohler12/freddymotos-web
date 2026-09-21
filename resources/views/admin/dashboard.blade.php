@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    @php
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $cards = [
            ['label' => 'Ventas (productos vendidos)', 'value' => $productsSold, 'bg' => 'bg-marca-mostaza', 'text' => 'text-marca-blanco',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label' => 'Ingresos', 'value' => $money($revenue), 'bg' => 'bg-marca-amarillo', 'text' => 'text-marca-negro',
                'icon' => 'M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Pedidos enviados y pagados', 'value' => $shippedAndPaidCount, 'bg' => 'bg-marca-negro', 'text' => 'text-marca-blanco',
                'icon' => 'M9 5h6a2 2 0 012 2v12l-5-3-5 3V7a2 2 0 012-2z'],
            ['label' => 'Gastos', 'value' => $money($expensesTotal), 'bg' => 'bg-marca-bordo', 'text' => 'text-marca-blanco',
                'icon' => 'M3 10h18M7 15h4m-4 0v.01M3 6h18v12H3z'],
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
            <div class="relative overflow-hidden rounded-2xl {{ $card['bg'] }} p-5 shadow-sm">
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
            </div>
        @endforeach
    </div>

    {{-- Ingresos vs gastos: todo el ancho, siguiendo el mismo filtro de período de arriba --}}
    <div class="mt-8 w-full rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
        <h2 class="text-sm font-bold text-marca-negro">Ingresos vs gastos · {{ $periods[$period] }}</h2>
        <div class="mt-4 h-72 w-full sm:h-80">
            <canvas data-chart="{{ json_encode($incomeVsExpensesChart) }}"></canvas>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Productos más vendidos</h2>
            @if ($hasTopProducts)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($topProductsChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">Todavía no hay ventas en este período.</p>
            @endif
        </div>

        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-marca-negro">Gastos por categoría</h2>
                <a href="{{ route('admin.expenses.index') }}" class="text-xs font-semibold text-marca-rojo hover:underline">Ver gastos</a>
            </div>
            @if ($hasExpensesByCategory)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($expensesByCategoryChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay gastos registrados en este período.</p>
            @endif
        </div>

        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-marca-negro">Cuánto se le debe a cada mecánico</h2>
                <a href="{{ route('admin.workshop.index', ['tab' => 'mecanicos']) }}" class="text-xs font-semibold text-marca-rojo hover:underline">Ver taller</a>
            </div>
            @if ($hasMechanicsDebt)
                <div class="mt-4 h-72">
                    <canvas data-chart="{{ json_encode($mechanicsDebtChart) }}"></canvas>
                </div>
            @else
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay trabajos de mecánicos pendientes de pago.</p>
            @endif
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Stock bajo / sin stock --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Sin stock o poco stock</h2>
            @if ($outOfStock->isEmpty() && $lowStock->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">Todos los productos tienen stock suficiente.</p>
            @else
                <ul class="mt-4 divide-y divide-marca-gris-claro text-sm">
                    @foreach ($outOfStock as $product)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-marca-negro">{{ $product->name }}</span>
                            <span class="rounded-full bg-marca-rojo/10 px-2.5 py-0.5 text-xs font-semibold text-marca-rojo">Sin stock</span>
                        </li>
                    @endforeach
                    @foreach ($lowStock as $product)
                        <li class="flex items-center justify-between py-2">
                            <span class="text-marca-negro">{{ $product->name }}</span>
                            <span class="rounded-full bg-marca-mostaza/15 px-2.5 py-0.5 text-xs font-semibold text-marca-mostaza">{{ $product->stock }} unidades</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Pedidos pendientes --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Pedidos pendientes</h2>
            @if ($pendingOrders->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay pedidos pendientes en este período.</p>
            @else
                <ul class="mt-4 divide-y divide-marca-gris-claro text-sm">
                    @foreach ($pendingOrders as $order)
                        <li class="flex items-center justify-between gap-3 py-2">
                            <div>
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-marca-negro hover:text-marca-rojo">{{ $order->customer->name ?? '—' }}</a>
                                <p class="text-xs text-marca-gris-oscuro">
                                    {{ $order->delivery_method === \App\Models\Order::DELIVERY_ENVIO ? 'Falta enviar' : 'Falta retirar en el local' }}
                                    @if ($order->payment_status === \App\Models\Order::PAYMENT_STATUS_PENDIENTE) · Pago pendiente @endif
                                </p>
                            </div>
                            <span class="shrink-0 text-marca-gris-oscuro">{{ $order->formatted_total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Pagos pendientes: pedidos del ecommerce --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Pagos pendientes · Pedidos</h2>
            @if ($pendingOrderPayments->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay pagos de pedidos pendientes en este período.</p>
            @else
                <ul class="mt-4 divide-y divide-marca-gris-claro text-sm">
                    @foreach ($pendingOrderPayments as $order)
                        <li class="py-2">
                            <div class="flex items-center justify-between gap-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-marca-negro hover:text-marca-rojo">{{ $order->customer->name ?? '—' }}</a>
                                <span class="shrink-0 text-marca-gris-oscuro">{{ $order->formatted_total }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-marca-gris-oscuro">{{ $order->items->pluck('product_name')->implode(', ') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Pagos pendientes: gastos vencidos --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Pagos pendientes · Gastos vencidos</h2>
            @if ($pendingExpensePayments->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay gastos recurrentes vencidos.</p>
            @else
                <ul class="mt-4 divide-y divide-marca-gris-claro text-sm">
                    @foreach ($pendingExpensePayments as $expense)
                        <li class="py-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-medium text-marca-negro">{{ $expense->description }}</span>
                                <span class="shrink-0 text-marca-gris-oscuro">{{ $expense->formatted_amount }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-marca-gris-oscuro">
                                {{ \App\Models\Expense::FREQUENCIES[$expense->frequency] ?? $expense->frequency }} · venció el {{ $expense->next_due_on->format('d/m/Y') }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
            <a href="{{ route('admin.expenses.index', ['tab' => 'pendientes']) }}" class="mt-4 block text-center text-xs font-semibold text-marca-rojo hover:underline">Ver gastos pendientes</a>
        </div>
    </div>
@endsection
