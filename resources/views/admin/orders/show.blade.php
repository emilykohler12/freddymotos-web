@extends('layouts.admin')

@section('title', 'Pedido #' . $order->id)
@section('page-heading', 'Pedido #' . $order->id)

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $paymentMethodLabels = ['mercadopago' => 'Mercado Pago'] + \App\Models\Order::REAL_PAYMENT_METHODS;
    @endphp


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Productos --}}
            <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div class="border-b border-marca-gris-claro px-5 py-4">
                    <h2 class="text-sm font-bold text-marca-negro">Productos</h2>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Producto</th>
                            <th class="px-5 py-3 font-semibold">Cantidad</th>
                            <th class="px-5 py-3 font-semibold">Precio unit.</th>
                            <th class="px-5 py-3 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-marca-gris-claro">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-5 py-3 font-medium text-marca-negro">{{ $item->product_name }}</td>
                                <td class="px-5 py-3 text-marca-gris-oscuro">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-marca-gris-oscuro">{{ $money($item->unit_price) }}</td>
                                <td class="px-5 py-3 text-marca-negro">{{ $item->formatted_subtotal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-sm">
                        <tr><td colspan="3" class="px-5 py-2 text-right text-marca-gris-oscuro">Subtotal</td><td class="px-5 py-2 font-medium text-marca-negro">{{ $money($order->subtotal) }}</td></tr>
                        <tr><td colspan="3" class="px-5 py-2 text-right text-marca-gris-oscuro">Descuento</td><td class="px-5 py-2 font-medium text-marca-negro">− {{ $money($order->discount) }}</td></tr>
                        <tr><td colspan="3" class="px-5 py-2 text-right text-marca-gris-oscuro">Envío</td><td class="px-5 py-2 font-medium text-marca-negro">{{ $money($order->shipping_cost) }}</td></tr>
                        <tr><td colspan="3" class="px-5 py-3 text-right text-base font-bold text-marca-negro">Total</td><td class="px-5 py-3 text-base font-extrabold text-marca-negro">{{ $order->formatted_total }}</td></tr>
                    </tfoot>
                </table>
            </div>

            {{-- Cliente / entrega --}}
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Cliente y entrega</h2>
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-marca-gris-oscuro">Cliente</dt><dd class="font-medium text-marca-negro">{{ $order->customer->name ?? '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Teléfono</dt><dd class="font-medium text-marca-negro">{{ $order->customer->phone ?? '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Email</dt><dd class="font-medium text-marca-negro">{{ $order->customer->email ?? '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Método de entrega</dt><dd class="font-medium text-marca-negro">{{ $order->delivery_method === 'envio' ? 'Envío' : 'Retiro en el local' }}</dd></div>
                    @if ($order->shipping_address)
                        <div class="sm:col-span-2"><dt class="text-marca-gris-oscuro">Dirección de envío</dt><dd class="font-medium text-marca-negro">{{ $order->shipping_address }}</dd></div>
                    @endif
                    @if ($order->notes)
                        <div class="sm:col-span-2"><dt class="text-marca-gris-oscuro">Notas</dt><dd class="font-medium text-marca-negro">{{ $order->notes }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Estado --}}
        <div class="space-y-6">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-4 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @csrf
                @method('PUT')
                <h2 class="text-sm font-bold text-marca-negro">Estado</h2>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Método de pago</label>
                    @if ($order->payment_method === \App\Models\Order::PAYMENT_WHATSAPP)
                        <p class="mb-2 text-sm font-medium text-marca-negro">Coordina por WhatsApp — elegí cómo pagó:</p>
                        <select name="real_payment_method" class="{{ $field }}">
                            <option value="">Todavía no pagó</option>
                            @foreach (\App\Models\Order::REAL_PAYMENT_METHODS as $value => $label)
                                <option value="{{ $value }}" @selected(old('real_payment_method') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-sm font-medium text-marca-negro">{{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
                    @endif
                </div>

                <div>
                    <label for="payment_status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Estado del pago</label>
                    <select id="payment_status" name="payment_status" class="{{ $field }}">
                        @foreach ($paymentStatuses as $value => $label)
                            <option value="{{ $value }}" @selected($order->payment_status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Estado del pedido</label>
                    <select id="status" name="status" class="{{ $field }}">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Guardar estado
                </button>
            </form>

            <a href="{{ route('admin.orders.index') }}" class="block text-center text-sm font-semibold text-marca-gris-oscuro hover:text-marca-rojo">← Volver a pedidos</a>
        </div>
    </div>
@endsection
