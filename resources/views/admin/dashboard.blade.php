@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    @php
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $cards = [
            ['label' => 'Ventas (productos vendidos)', 'value' => $productsSold, 'accent' => 'bg-marca-mostaza'],
            ['label' => 'Ingresos', 'value' => $money($revenue), 'accent' => 'bg-marca-amarillo'],
            ['label' => 'Pedidos enviados y pagados', 'value' => $shippedAndPaidCount, 'accent' => 'bg-marca-negro'],
            ['label' => 'Gastos', 'value' => $money($expensesTotal), 'accent' => 'bg-marca-bordo'],
        ];
        $estados = [
            'pendiente'  => 'bg-marca-mostaza/15 text-marca-mostaza',
            'procesando' => 'bg-marca-mostaza/15 text-marca-mostaza',
            'enviado'    => 'bg-marca-amarillo/20 text-marca-negro',
            'entregado'  => 'bg-marca-amarillo/20 text-marca-negro',
            'pagado'     => 'bg-marca-amarillo/20 text-marca-negro',
            'cancelado'  => 'bg-marca-rojo/10 text-marca-rojo',
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
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <span class="inline-block h-1.5 w-8 rounded-full {{ $card['accent'] }}"></span>
                <p class="mt-3 text-sm text-marca-gris-oscuro">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-extrabold text-marca-negro">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Productos más vendidos --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Productos más vendidos</h2>
            @if ($topProducts->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">Todavía no hay ventas en este período.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($topProducts as $item)
                        @php $pct = max(4, round(($item->total_qty / $topProductsMax) * 100)); @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-marca-negro">{{ $item->product_name }}</span>
                                <span class="text-marca-gris-oscuro">{{ $item->total_qty }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-marca-gris-claro">
                                <div class="h-2 rounded-full bg-marca-amarillo" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

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

        {{-- Pagos pendientes --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Pagos pendientes</h2>
            @if ($pendingPayments->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay pagos pendientes en este período.</p>
            @else
                <ul class="mt-4 divide-y divide-marca-gris-claro text-sm">
                    @foreach ($pendingPayments as $order)
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

        {{-- Últimos pedidos --}}
        <div class="rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <div class="flex items-center justify-between border-b border-marca-gris-claro px-5 py-4">
                <h2 class="text-sm font-bold text-marca-negro">Últimos pedidos</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-marca-rojo hover:underline">Ver todos</a>
            </div>

            @if ($latestOrders->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay pedidos en este período.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Cliente</th>
                                <th class="px-5 py-3 font-semibold">Descripción</th>
                                <th class="px-5 py-3 font-semibold">Estado</th>
                                <th class="px-5 py-3 font-semibold">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marca-gris-claro">
                            @foreach ($latestOrders as $order)
                                <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                                    <td class="px-5 py-3 font-medium text-marca-negro">{{ $order->customer->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $order->items->pluck('product_name')->implode(', ') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $estados[$order->status] ?? 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Gastos por categoría --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Gastos por categoría</h2>
            @if ($expensesByCategory->isEmpty())
                <p class="mt-4 text-sm text-marca-gris-oscuro">No hay gastos registrados en este período.</p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($expensesByCategory as $categoryName => $total)
                        @php $pct = max(4, round(($total / $expensesByCategoryMax) * 100)); @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-marca-negro">{{ $categoryName }}</span>
                                <span class="text-marca-gris-oscuro">{{ $money($total) }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-marca-gris-claro">
                                <div class="h-2 rounded-full bg-marca-bordo" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('admin.expenses.index') }}" class="mt-4 block text-center text-xs font-semibold text-marca-rojo hover:underline">Ver gastos</a>
        </div>
    </div>
@endsection
