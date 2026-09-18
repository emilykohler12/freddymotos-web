@extends('layouts.admin')

@section('title', 'Movimientos')
@section('page-heading', 'Movimientos y notificaciones')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
    @endphp


    {{-- Tabs: radios, labels y paneles como hermanos directos (así el CSS peer-checked --}}
    {{-- funciona tanto para resaltar el tab activo como para mostrar/ocultar el panel). --}}
    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="activity-tab" id="tab-notificaciones" class="peer/notificaciones hidden" @checked($activeTab === 'notificaciones')>
        <label for="tab-notificaciones" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/notificaciones:bg-marca-negro peer-checked/notificaciones:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notificaciones
        </label>

        <input type="radio" name="activity-tab" id="tab-inventario" class="peer/inventario hidden" @checked($activeTab === 'inventario')>
        <label for="tab-inventario" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/inventario:bg-marca-negro peer-checked/inventario:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Inventario
        </label>

        <input type="radio" name="activity-tab" id="tab-ingresos" class="peer/ingresos hidden" @checked($activeTab === 'ingresos')>
        <label for="tab-ingresos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/ingresos:bg-marca-negro peer-checked/ingresos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Otros ingresos
        </label>

    {{-- Notificaciones: stock + feed de movimientos --}}
    <div class="hidden w-full pt-4 peer-checked/notificaciones:block">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                @if ($whatsappOrders->isNotEmpty() || $webOrders->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        {{-- Pedidos por WhatsApp: coordinados a mano, necesitan seguimiento --}}
                        <div class="rounded-2xl bg-marca-bordo/5 p-4 ring-1 ring-marca-bordo/20">
                            <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-marca-bordo">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 20l1.5-4A8 8 0 1112 20a8 8 0 01-4-1L4 20z"/></svg>
                                Pedidos por WhatsApp
                            </h3>
                            @if ($whatsappOrders->isEmpty())
                                <p class="text-sm text-marca-gris-oscuro">Nada pendiente por acá.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($whatsappOrders as $order)
                                        <div class="rounded-xl bg-marca-blanco p-3 text-sm shadow-sm">
                                            <div class="flex items-center justify-between gap-2">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-marca-negro hover:text-marca-bordo">{{ $order->customer->name ?? '—' }}</a>
                                                <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mt-0.5 text-xs text-marca-gris-oscuro">{{ $order->formatted_total }} · {{ $orderStatuses[$order->status] ?? $order->status }}</p>
                                            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-2 flex gap-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                                                <select name="status" class="flex-1 rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-xs">
                                                    @foreach ($orderStatuses as $value => $label)
                                                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="shrink-0 rounded-lg bg-marca-bordo px-3 py-1.5 text-xs font-semibold text-marca-blanco hover:bg-marca-negro">Actualizar</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Compras web: ya pagadas online, solo hace falta despachar --}}
                        <div class="rounded-2xl bg-marca-amarillo/10 p-4 ring-1 ring-marca-amarillo/30">
                            <h3 class="mb-3 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-marca-negro">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Compras web
                            </h3>
                            @if ($webOrders->isEmpty())
                                <p class="text-sm text-marca-gris-oscuro">Nada pendiente por acá.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($webOrders as $order)
                                        <div class="rounded-xl bg-marca-blanco p-3 text-sm shadow-sm">
                                            <div class="flex items-center justify-between gap-2">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-marca-negro hover:text-marca-rojo">{{ $order->customer->name ?? '—' }}</a>
                                                <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mt-0.5 text-xs text-marca-gris-oscuro">{{ $order->formatted_total }} · {{ $orderStatuses[$order->status] ?? $order->status }}</p>
                                            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-2 flex gap-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                                                <select name="status" class="flex-1 rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-xs">
                                                    @foreach ($orderStatuses as $value => $label)
                                                        <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="shrink-0 rounded-lg bg-marca-amarillo px-3 py-1.5 text-xs font-semibold text-marca-negro hover:bg-marca-rojo hover:text-marca-blanco">Actualizar</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($outOfStock->isNotEmpty() || $lowStock->isNotEmpty() || $pendingSupplierPayments->isNotEmpty() || $pendingRefunds->isNotEmpty() || $dueExpenses->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($outOfStock as $product)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-rojo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span class="text-marca-negro"><strong>{{ $product->name }}</strong> está sin stock.</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-rojo hover:underline">Reponer</a>
                            </div>
                        @endforeach
                        @foreach ($lowStock as $product)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-mostaza/15 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-mostaza" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <span class="text-marca-negro"><strong>{{ $product->name }}</strong> tiene poco stock ({{ $product->stock }}).</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-negro hover:underline">Reponer</a>
                            </div>
                        @endforeach
                        @foreach ($pendingSupplierPayments as $purchase)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-bordo/10 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-bordo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-marca-negro">Falta pagar <strong>$ {{ number_format($purchase->balance, 0, ',', '.') }}</strong> a <strong>{{ $purchase->supplier->name ?? '—' }}</strong> ({{ $purchase->description }}).</span>
                                <a href="{{ route('admin.suppliers.show', $purchase->supplier) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-bordo hover:underline">Ver proveedor</a>
                            </div>
                        @endforeach
                        @foreach ($pendingRefunds as $order)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-rojo" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M3 10l4-4m-4 4 4 4m14 3H10m11 0-4 4m4-4-4-4"/></svg>
                                <span class="text-marca-negro">Falta reembolsar <strong>{{ $order->formatted_total }}</strong> a <strong>{{ $order->customer->name ?? '—' }}</strong> (pedido cancelado y ya cobrado).</span>
                                <a href="{{ route('admin.orders.show', $order) }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-rojo hover:underline">Ver pedido</a>
                            </div>
                        @endforeach
                        @foreach ($dueExpenses as $expense)
                            <div class="flex items-center gap-3 rounded-xl bg-marca-mostaza/15 px-4 py-3 text-sm">
                                <svg class="h-5 w-5 shrink-0 text-marca-mostaza" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-marca-negro">Toca pagar <strong>{{ $expense->description }}</strong> ({{ \App\Models\Expense::FREQUENCIES[$expense->frequency] ?? $expense->frequency }}) — último pago {{ $expense->incurred_on->format('d/m/Y') }}.</span>
                                <a href="{{ route('admin.expenses.index') }}" class="ml-auto shrink-0 text-xs font-semibold text-marca-negro hover:underline">Ver gastos</a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    @if ($logs->isEmpty())
                        <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay compras registradas.</p>
                    @else
                        <ul class="divide-y divide-marca-gris-claro">
                            @foreach ($logs as $log)
                                <li class="flex items-start gap-4 px-5 py-4">
                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-marca-gris-claro text-xs font-bold uppercase text-marca-gris-oscuro">
                                        {{ Str::substr($log->type, 0, 1) }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-marca-negro">{{ $log->description }}</p>
                                        <p class="mt-0.5 text-xs text-marca-gris-oscuro">
                                            {{ $log->user->name ?? 'Sistema' }} · {{ $log->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>{{ $logs->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Inventario: sumar o restar stock de un producto --}}
    <div class="hidden w-full pt-4 peer-checked/inventario:block">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Ajustar stock</h2>
                <form method="POST" action="{{ route('admin.stock-movements.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="stock-product" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Producto</label>
                        <select id="stock-product" name="product_id" required class="{{ $field }}">
                            <option value="">Elegir…</option>
                            @foreach ($inventoryProducts as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} (stock: {{ $product->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Motivo</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($stockReasons as $value => $label)
                                @if ($value !== \App\Models\StockMovement::REASON_AJUSTE)
                                    <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                                        <input type="radio" name="reason" value="{{ $value }}" @checked($loop->first) required class="h-4 w-4 border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                                        {{ $label }}
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-marca-gris-oscuro">Compra suma stock. Venta en el local lo resta (para ventas que no pasaron por la web).</p>
                    </div>

                    <div>
                        <label for="stock-quantity" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Cantidad</label>
                        <input type="number" id="stock-quantity" name="quantity" min="1" required class="{{ $field }}">
                    </div>

                    <div>
                        <label for="stock-note" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nota (opcional)</label>
                        <input type="text" id="stock-note" name="note" maxlength="255" class="{{ $field }}">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                        Guardar movimiento
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <h2 class="mb-3 text-sm font-bold text-marca-negro">Últimos movimientos</h2>
                @if ($stockMovements->isEmpty())
                    <p class="text-sm text-marca-gris-oscuro">Todavía no hay movimientos de stock.</p>
                @else
                    <ul class="divide-y divide-marca-gris-claro text-sm">
                        @foreach ($stockMovements as $movement)
                            <li class="flex items-center justify-between gap-3 py-2.5">
                                <div>
                                    <p class="font-medium text-marca-negro">{{ $movement->product->name ?? 'Producto eliminado' }}</p>
                                    <p class="text-xs text-marca-gris-oscuro">
                                        {{ $movement->reason_label }} · {{ $movement->user->name ?? 'Sistema' }} · {{ $movement->created_at->diffForHumans() }}
                                        @if ($movement->note) · {{ $movement->note }} @endif
                                    </p>
                                </div>
                                <span class="shrink-0 font-bold {{ $movement->quantity_change >= 0 ? 'text-marca-amarillo' : 'text-marca-rojo' }}">
                                    {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ $movement->quantity_change }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Otros ingresos: registrar + listado + categorías --}}
    <div class="hidden w-full pt-4 peer-checked/ingresos:block">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <h2 class="text-sm font-bold text-marca-negro">Registros de otros ingresos</h2>
                @if ($otrosIngresos->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        Todavía no hay otros ingresos registrados.
                    </p>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($otrosIngresos as $item)
                            @include('admin.expenses._card', ['item' => $item, 'categories' => $ingresoCategories])
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Registrar otro ingreso</h2>
                    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="type" value="ingreso">
                        <input type="text" name="description" placeholder="Descripción" required class="{{ $field }}">
                        <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                        <select name="expense_category_id" class="{{ $field }}">
                            <option value="">Sin categoría</option>
                            @foreach ($ingresoCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="incurred_on" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Registrar ingreso
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Categorías de otros ingresos</h2>
                    <ul class="mb-3 divide-y divide-marca-gris-claro text-sm">
                        @forelse ($ingresoCategories as $category)
                            <li class="flex items-center justify-between py-2">
                                <span class="text-marca-negro">{{ $category->name }}</span>
                                <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?');" class="inline-flex">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                </form>
                            </li>
                        @empty
                            <li class="py-2 text-marca-gris-oscuro">Sin categorías todavía.</li>
                        @endforelse
                    </ul>
                    <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="ingreso">
                        <input type="text" name="name" placeholder="Nueva categoría" required class="{{ $field }}">
                        <button type="submit" class="shrink-0 rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
