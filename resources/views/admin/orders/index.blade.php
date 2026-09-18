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
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $paymentMethodLabels = ['mercadopago' => 'Mercado Pago', 'whatsapp' => 'A coordinar (WhatsApp)'] + \App\Models\Order::REAL_PAYMENT_METHODS;
    @endphp

    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Cargar pedido por WhatsApp
        </a>
    </div>

    {{-- Tabs: radios, labels y paneles como hermanos directos (así el CSS peer-checked --}}
    {{-- funciona tanto para resaltar el tab activo como para mostrar/ocultar el panel). --}}
    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="orders-tab" id="tab-pedidos" class="peer/pedidos hidden" checked>
        <label for="tab-pedidos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pedidos:bg-marca-negro peer-checked/pedidos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6a2 2 0 012 2v12l-5-3-5 3V7a2 2 0 012-2z"/></svg>
            Pedidos
        </label>

        <input type="radio" name="orders-tab" id="tab-ingresos" class="peer/ingresos hidden">
        <label for="tab-ingresos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/ingresos:bg-marca-negro peer-checked/ingresos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ingresos
        </label>

    {{-- ===== Pedidos (vista operativa: envío, estado) ===== --}}
    <div class="hidden w-full space-y-5 pt-4 peer-checked/pedidos:block">
        <form method="GET" class="flex flex-wrap gap-2">
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
                                <td class="px-5 py-3 text-marca-gris-oscuro">{{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
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

        <div>{{ $orders->links() }}</div>
    </div>

    {{-- ===== Ingresos (solo lo que entró: pedidos pagos) ===== --}}
    <div class="hidden w-full space-y-5 pt-4 peer-checked/ingresos:block">
        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <span class="inline-block h-1.5 w-8 rounded-full bg-marca-amarillo"></span>
            <p class="mt-3 text-sm text-marca-gris-oscuro">Total cobrado</p>
            <p class="mt-1 text-2xl font-extrabold text-marca-negro">{{ $money($income['total']) }}</p>
        </div>

        <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                        <tr>
                            <th class="px-5 py-3 font-semibold">N°</th>
                            <th class="px-5 py-3 font-semibold">Fecha de pago</th>
                            <th class="px-5 py-3 font-semibold">Cliente</th>
                            <th class="px-5 py-3 font-semibold">Monto</th>
                            <th class="px-5 py-3 font-semibold">Método de pago</th>
                            <th class="px-5 py-3 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-marca-gris-claro">
                        @forelse ($income['orders'] as $order)
                            <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                                <td class="px-5 py-3 font-medium text-marca-negro">#{{ $order->id }}</td>
                                <td class="px-5 py-3 text-marca-gris-oscuro">{{ optional($order->paid_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-marca-negro">{{ $order->customer->name ?? '—' }}</td>
                                <td class="px-5 py-3 font-semibold text-marca-negro">{{ $order->formatted_total }}</td>
                                <td class="px-5 py-3 text-marca-gris-oscuro">{{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                <td class="px-5 py-3 text-right text-xs font-semibold text-marca-rojo">Ver →</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-marca-gris-oscuro">Todavía no hay pedidos pagos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $income['orders']->links() }}</div>
    </div>
    </div>
@endsection
