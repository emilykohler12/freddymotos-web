@extends('layouts.admin')

@section('title', $supplier->name)
@section('page-heading', $supplier->name)

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp


    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Registrar compra: elegir un repuesto del catálogo y una cantidad --}}
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-4 text-sm font-bold text-marca-negro">Registrar compra</h2>
                <form method="POST" action="{{ route('admin.suppliers.purchases.store', $supplier) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Repuesto</label>
                        <select name="product_id" required class="{{ $field }}">
                            <option value="">Elegir repuesto…</option>
                            @foreach ($allProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $money($product->cost_price) }} costo)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Cantidad</label>
                        <input type="number" min="1" value="1" name="quantity" required class="{{ $field }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Fecha</label>
                        <input type="date" name="purchased_at" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Estado del pago</label>
                        <select name="status" required class="{{ $field }}">
                            @foreach ($purchaseStatuses as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'pendiente')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Registrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Historial de compras --}}
            <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div class="border-b border-marca-gris-claro px-5 py-4">
                    <h2 class="text-sm font-bold text-marca-negro">Historial de compras</h2>
                </div>
                @if ($supplier->purchases->isEmpty())
                    <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay compras registradas.</p>
                @else
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Fecha</th>
                                <th class="px-5 py-3 font-semibold">Detalle</th>
                                <th class="px-5 py-3 font-semibold">Monto</th>
                                <th class="px-5 py-3 font-semibold">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marca-gris-claro">
                            @foreach ($supplier->purchases as $purchase)
                                <tr>
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $purchase->purchased_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3 text-marca-negro">{{ $purchase->description }}</td>
                                    <td class="px-5 py-3 text-marca-negro">{{ $money($purchase->amount) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $purchase->status === 'pendiente' ? 'bg-marca-rojo/10 text-marca-rojo' : ($purchase->status === 'pagado' ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-gris-claro text-marca-gris-oscuro') }}">
                                            {{ $purchase->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Repuestos comprados (catálogo asociado) --}}
            @if ($supplier->products->isNotEmpty())
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Repuestos de este proveedor</h2>
                    <ul class="divide-y divide-marca-gris-claro text-sm">
                        @foreach ($supplier->products as $product)
                            <li class="flex items-center justify-between py-2">
                                <span class="text-marca-negro">{{ $product->name }}</span>
                                <span class="text-marca-gris-oscuro">Stock: {{ $product->stock }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Datos de contacto</h2>
                <dl class="space-y-2 text-sm">
                    <div><dt class="text-marca-gris-oscuro">Empresa</dt><dd class="font-medium text-marca-negro">{{ $supplier->company ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">CUIT</dt><dd class="font-medium text-marca-negro">{{ $supplier->cuit ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Teléfono</dt><dd class="font-medium text-marca-negro">{{ $supplier->phone ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Email</dt><dd class="font-medium text-marca-negro">{{ $supplier->email ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Dirección</dt><dd class="font-medium text-marca-negro">{{ $supplier->address ?: '—' }}</dd></div>
                    <div><dt class="text-marca-gris-oscuro">Condiciones de pago</dt><dd class="font-medium text-marca-negro">{{ $supplier->payment_terms ?: '—' }}</dd></div>
                </dl>
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="mt-4 block text-center text-xs font-semibold text-marca-rojo hover:underline">Editar datos</a>
            </div>

            <div class="rounded-2xl bg-marca-bordo p-5 text-marca-blanco shadow-sm">
                <p class="text-xs uppercase tracking-wide text-marca-blanco/70">Deuda actual</p>
                <p class="mt-1 text-2xl font-extrabold">{{ $supplier->formatted_debt }}</p>
            </div>
        </div>
    </div>
@endsection
