@extends('layouts.admin')

@section('title', 'Ventas')
@section('page-heading', 'Ventas / Pedidos')

@section('content')
    @php
        $estados = [
            'pendiente' => 'bg-marca-mostaza/15 text-marca-mostaza', 'procesando' => 'bg-marca-mostaza/15 text-marca-mostaza',
            'enviado' => 'bg-marca-amarillo/20 text-marca-negro', 'entregado' => 'bg-marca-amarillo/20 text-marca-negro',
            'pagado' => 'bg-marca-amarillo/20 text-marca-negro', 'cancelado' => 'bg-marca-rojo/10 text-marca-rojo',
            'rechazado' => 'bg-marca-rojo/10 text-marca-rojo',
        ];
    @endphp

    <form method="GET" class="mb-5 flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            <option value="">Estado del pedido: todos</option>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment_status" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            <option value="">Estado del pago: todos</option>
            @foreach ($paymentStatuses as $value => $label)
                <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>


    <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                    <tr>
                        <th class="px-5 py-3 font-semibold">N°</th>
                        <th class="px-5 py-3 font-semibold">Fecha</th>
                        <th class="px-5 py-3 font-semibold">Cliente</th>
                        <th class="px-5 py-3 font-semibold">Total</th>
                        <th class="px-5 py-3 font-semibold">Pago</th>
                        <th class="px-5 py-3 font-semibold">Estado del pago</th>
                        <th class="px-5 py-3 font-semibold">Estado del pedido</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-marca-gris-claro">
                    @forelse ($orders as $order)
                        <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                            <td class="px-5 py-3 font-medium text-marca-negro">#{{ $order->id }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 text-marca-negro">{{ $order->customer->name ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold text-marca-negro">{{ $order->formatted_total }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ ucfirst($order->payment_method) }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $estados[$order->payment_status] ?? 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                    {{ $paymentStatuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $estados[$order->status] ?? 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                    {{ $statuses[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right text-xs font-semibold text-marca-rojo">Ver →</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-marca-gris-oscuro">Todavía no hay pedidos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
