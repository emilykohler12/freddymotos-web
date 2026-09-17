@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
    @php
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $cards = [
            ['label' => 'Ventas (productos vendidos)', 'value' => $productsSoldThisMonth, 'accent' => 'bg-marca-mostaza'],
            ['label' => 'Ingresos del mes', 'value' => $money($revenueThisMonth), 'accent' => 'bg-marca-amarillo'],
            ['label' => 'Poco stock', 'value' => $lowStockCount, 'accent' => 'bg-marca-mostaza'],
            ['label' => 'Sin stock', 'value' => $outOfStockCount, 'accent' => 'bg-marca-rojo'],
            ['label' => 'Pedidos pendientes', 'value' => $pendingOrdersCount, 'accent' => 'bg-marca-mostaza'],
            ['label' => 'Pedidos enviados', 'value' => $shippedOrdersCount, 'accent' => 'bg-marca-amarillo'],
            ['label' => 'Pagos pendientes', 'value' => $pendingPaymentsCount, 'accent' => 'bg-marca-rojo'],
            ['label' => 'Gastos del mes', 'value' => $money($expensesThisMonth), 'accent' => 'bg-marca-bordo'],
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

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Últimos pedidos --}}
        <div class="rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-marca-gris-claro px-5 py-4">
                <h2 class="text-sm font-bold text-marca-negro">Últimos pedidos</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-marca-rojo hover:underline">Ver todos</a>
            </div>

            @if ($latestOrders->isEmpty())
                <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay pedidos.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Cliente</th>
                                <th class="px-5 py-3 font-semibold">Total</th>
                                <th class="px-5 py-3 font-semibold">Estado</th>
                                <th class="px-5 py-3 font-semibold">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marca-gris-claro">
                            @foreach ($latestOrders as $order)
                                <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                                    <td class="px-5 py-3 font-medium text-marca-negro">{{ $order->customer->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-marca-negro">{{ $order->formatted_total }}</td>
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

        {{-- Gráfico de gastos (últimos 6 meses) --}}
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <h2 class="text-sm font-bold text-marca-negro">Gastos · últimos 6 meses</h2>
            <div class="mt-6 flex h-40 items-end gap-3">
                @foreach ($expensesChart as $point)
                    @php $pct = max(4, round(($point['total'] / $expensesChartMax) * 100)); @endphp
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex h-32 w-full items-end">
                            <div class="w-full rounded-t-md bg-marca-bordo transition hover:bg-marca-rojo" style="height: {{ $pct }}%" title="{{ $money($point['total']) }}"></div>
                        </div>
                        <span class="text-[11px] font-semibold uppercase text-marca-gris-oscuro/60">{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.expenses.index') }}" class="mt-4 block text-center text-xs font-semibold text-marca-rojo hover:underline">Ver gastos</a>
        </div>
    </div>
@endsection
