@extends('layouts.admin')

@section('title', $customer->name)
@section('page-heading', $customer->name)

@section('content')
    @php
        $estados = [
            'pendiente' => 'bg-marca-mostaza/15 text-marca-mostaza', 'procesando' => 'bg-marca-mostaza/15 text-marca-mostaza',
            'enviado' => 'bg-marca-amarillo/20 text-marca-negro', 'entregado' => 'bg-marca-amarillo/20 text-marca-negro',
            'pagado' => 'bg-marca-amarillo/20 text-marca-negro', 'cancelado' => 'bg-marca-rojo/10 text-marca-rojo',
        ];
    @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div class="border-b border-marca-gris-claro px-5 py-4">
                    <h2 class="text-sm font-bold text-marca-negro">Historial de compras</h2>
                </div>
                @if ($customer->orders->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hizo pedidos.</p>
                @else
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                            <tr>
                                <th class="px-5 py-3 font-semibold">N°</th>
                                <th class="px-5 py-3 font-semibold">Fecha</th>
                                <th class="px-5 py-3 font-semibold">Total</th>
                                <th class="px-5 py-3 font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marca-gris-claro">
                            @foreach ($customer->orders as $order)
                                <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                                    <td class="px-5 py-3 font-medium text-marca-negro">#{{ $order->id }}</td>
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3 text-marca-negro">{{ $order->formatted_total }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $estados[$order->status] ?? 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Datos del cliente</h2>
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-marca-gris-oscuro">Teléfono</dt><dd class="font-medium text-marca-negro">{{ $customer->phone }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Email</dt><dd class="font-medium text-marca-negro">{{ $customer->email ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">DNI/CUIT</dt><dd class="font-medium text-marca-negro">{{ $customer->dni_cuit ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Dirección</dt><dd class="font-medium text-marca-negro">{{ $customer->address ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Ciudad / Provincia</dt><dd class="font-medium text-marca-negro">{{ trim(($customer->city ?? '') . ' ' . ($customer->province ? '/ ' . $customer->province : '')) ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Código postal</dt><dd class="font-medium text-marca-negro">{{ $customer->postal_code ?: '—' }}</dd></div>
                </dl>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="mt-4 block text-center text-xs font-semibold text-marca-rojo hover:underline">Editar datos</a>
            </div>

            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Resumen</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-marca-gris-oscuro">Pedidos</dt><dd class="font-semibold text-marca-negro">{{ $customer->orders->count() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-marca-gris-oscuro">Total gastado</dt><dd class="font-semibold text-marca-negro">{{ $customer->formatted_total_spent }}</dd></div>
                    <div class="flex justify-between"><dt class="text-marca-gris-oscuro">Última compra</dt><dd class="font-semibold text-marca-negro">{{ optional($customer->last_order_at)->format('d/m/Y') ?? '—' }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
@endsection
