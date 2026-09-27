@extends('layouts.admin')

@section('title', 'Movimientos')
@section('page-heading', 'Movimientos y notificaciones')

@section('content')
    @php
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $tabs = [
            'web' => 'Ventas Web',
            'whatsapp' => 'Ventas WhatsApp',
            'consultas' => 'Consultas',
            'por-enviar' => 'Pedidos por enviar',
            'por-cobrar' => 'Pedidos por cobrar',
            'pagos-pendientes' => 'Pagos pendientes',
            'stock' => 'Stock',
            'gastos' => 'Vencimiento de gastos',
            'promociones' => 'Vencimiento de promociones',
        ];
    @endphp

    {{-- Tabs --}}
    <div class="flex flex-wrap items-start gap-2">
        @foreach ($tabs as $value => $label)
            <a href="{{ route('admin.activity.index', ['tab' => $value]) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === $value ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ===== Ventas Web ===== --}}
    @if ($activeTab === 'web')
        <div class="w-full space-y-3 pt-4">
            @if ($webOrders->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ventas web abiertas.
                </p>
            @else
                @foreach ($webOrders as $order)
                    <div class="rounded-2xl bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('admin.orders.show', [$order, 'from' => 'web']) }}" class="font-semibold text-marca-negro hover:text-marca-rojo">#{{ $order->id }} · {{ $order->customer->name ?? '—' }}</a>
                            <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-marca-gris-oscuro">{{ $order->formatted_total }}</p>
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
            @endif
        </div>
    @endif

    {{-- ===== Ventas WhatsApp ===== --}}
    @if ($activeTab === 'whatsapp')
        <div class="w-full space-y-3 pt-4">
            @if ($whatsappOrders->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ventas por WhatsApp abiertas.
                </p>
            @else
                @foreach ($whatsappOrders as $order)
                    <div class="rounded-2xl bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('admin.orders.show', [$order, 'from' => 'whatsapp']) }}" class="font-semibold text-marca-negro hover:text-marca-bordo">#{{ $order->id }} · {{ $order->customer->name ?? '—' }}</a>
                            <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-marca-gris-oscuro">{{ $order->formatted_total }}</p>
                        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="mt-2 grid grid-cols-2 gap-2">
                            @csrf @method('PUT')
                            <select name="payment_status" class="rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-xs">
                                @foreach ($paymentStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected($order->payment_status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-xs">
                                @foreach ($orderStatuses as $value => $label)
                                    <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="real_payment_method" class="col-span-2 rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-xs">
                                <option value="">Cómo pagó (si marcás "Pagado")</option>
                                @foreach ($realPaymentMethods as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="col-span-2 rounded-lg bg-marca-bordo px-3 py-1.5 text-xs font-semibold text-marca-blanco hover:bg-marca-negro">Actualizar</button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    {{-- ===== Consultas ===== --}}
    @if ($activeTab === 'consultas')
        <div class="w-full space-y-4 pt-4">
            @if ($inquiries->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Todavía no hay consultas.
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($inquiries as $inquiry)
                        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-marca-negro">{{ $inquiry->name }}</p>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $inquiry->status === 'atendida' ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-rojo/10 text-marca-rojo' }}">
                                    {{ $inquiry->status === 'atendida' ? 'Respondida' : 'Pendiente' }}
                                </span>
                            </div>
                            <dl class="mt-2 space-y-1 text-sm text-marca-gris-oscuro">
                                <div>Tel: <a href="tel:{{ $inquiry->phone }}" class="font-medium text-marca-negro hover:text-marca-rojo">{{ $inquiry->phone }}</a></div>
                                @if ($inquiry->email)
                                    <div>Email: <span class="font-medium text-marca-negro">{{ $inquiry->email }}</span></div>
                                @endif
                                <div class="pt-1 text-marca-negro">{{ $inquiry->message }}</div>
                                <div class="text-xs">{{ $inquiry->created_at->diffForHumans() }}</div>
                            </dl>
                            <div class="mt-3 flex items-center gap-3 border-t border-marca-gris-claro pt-3">
                                <form method="POST" action="{{ route('admin.inquiries.toggle', $inquiry) }}" class="inline-flex">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">
                                        {{ $inquiry->status === 'atendida' ? 'Marcar como pendiente' : 'Marcar como respondida' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" data-confirm="¿Eliminar esta consulta?" class="ml-auto inline-flex">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div>{{ $inquiries->links() }}</div>
            @endif
        </div>
    @endif

    {{-- ===== Pedidos por enviar ===== --}}
    @if ($activeTab === 'por-enviar')
        <div class="w-full space-y-3 pt-4">
            @if ($toShip->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay pedidos pendientes de envío.
                </p>
            @else
                @foreach ($toShip as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center gap-3 rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $order->status === 'enviado' ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-mostaza/15 text-marca-mostaza' }}">
                            {{ $orderStatuses[$order->status] ?? $order->status }}
                        </span>
                        <span class="font-medium text-marca-negro">#{{ $order->id }} · {{ $order->customer->name ?? '—' }}</span>
                        <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y') }}</span>
                        <span class="ml-auto font-semibold text-marca-negro">{{ $order->formatted_total }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    @endif

    {{-- ===== Pedidos por cobrar ===== --}}
    @if ($activeTab === 'por-cobrar')
        <div class="w-full space-y-3 pt-4">
            @if ($toCollect->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay pedidos pendientes de pago.
                </p>
            @else
                @foreach ($toCollect as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center gap-3 rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                        <span class="font-medium text-marca-negro">#{{ $order->id }} · {{ $order->customer->name ?? '—' }}</span>
                        <span class="text-xs text-marca-gris-oscuro">{{ $order->created_at->format('d/m/Y') }}</span>
                        <span class="ml-auto font-semibold text-marca-rojo">{{ $order->formatted_total }}</span>
                    </a>
                @endforeach
            @endif
        </div>
    @endif

    {{-- ===== Pagos pendientes (mecánicos + WhatsApp sin pagar) ===== --}}
    @if ($activeTab === 'pagos-pendientes')
        <div class="w-full space-y-6 pt-4">
            <div>
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Deuda de mecánicos</h2>
                @if ($mechanicsDebt->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        Sin deudas de mecánicos.
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach ($mechanicsDebt as $row)
                            <a href="{{ route('admin.workshop.index', ['tab' => 'mecanicos']) }}" class="flex items-center justify-between rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                                <span class="font-medium text-marca-negro">{{ $row['mechanic']->name }}</span>
                                <span class="font-semibold text-marca-rojo">{{ $money($row['total']) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Pedidos de WhatsApp sin pagar</h2>
                @if ($whatsappUnpaid->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        No hay pedidos de WhatsApp sin pagar.
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach ($whatsappUnpaid as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                                <span class="font-medium text-marca-negro">#{{ $order->id }} · {{ $order->customer->name ?? '—' }}</span>
                                <span class="font-semibold text-marca-rojo">{{ $order->formatted_total }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Deuda a proveedores</h2>
                @if ($pendingSupplierPayments->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        No hay pagos pendientes a proveedores.
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach ($pendingSupplierPayments as $purchase)
                            <a href="{{ route('admin.suppliers.show', $purchase->supplier) }}" class="flex items-center justify-between rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                                <span class="font-medium text-marca-negro">{{ $purchase->supplier->name ?? '—' }} · {{ $purchase->description }}</span>
                                <span class="font-semibold text-marca-rojo">{{ $money($purchase->amount - $purchase->paid_amount) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Reembolsos pendientes</h2>
                @if ($pendingRefunds->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        No hay reembolsos pendientes.
                    </p>
                @else
                    <div class="space-y-2">
                        @foreach ($pendingRefunds as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between rounded-xl bg-marca-blanco p-4 text-sm shadow-sm ring-1 ring-marca-gris-oscuro/5 hover:ring-marca-amarillo">
                                <span class="font-medium text-marca-negro">#{{ $order->id }} · {{ $order->customer->name ?? '—' }} (cancelado y ya cobrado)</span>
                                <span class="font-semibold text-marca-rojo">{{ $order->formatted_total }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ===== Stock ===== --}}
    @if ($activeTab === 'stock')
        <div class="w-full space-y-3 pt-4">
            @if ($outOfStock->isEmpty() && $lowStock->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Stock suficiente en todos los repuestos.
                </p>
            @else
                @foreach ($outOfStock as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center gap-3 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm hover:ring-2 hover:ring-marca-rojo">
                        <span class="text-marca-negro"><strong>{{ $product->name }}</strong> está sin stock.</span>
                        <span class="ml-auto shrink-0 text-xs font-semibold text-marca-rojo">Reponer →</span>
                    </a>
                @endforeach
                @foreach ($lowStock as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center gap-3 rounded-xl bg-marca-mostaza/15 px-4 py-3 text-sm hover:ring-2 hover:ring-marca-mostaza">
                        <span class="text-marca-negro"><strong>{{ $product->name }}</strong> tiene poco stock ({{ $product->stock }}).</span>
                        <span class="ml-auto shrink-0 text-xs font-semibold text-marca-negro">Reponer →</span>
                    </a>
                @endforeach
            @endif
        </div>
    @endif

    {{-- ===== Vencimiento de gastos ===== --}}
    @if ($activeTab === 'gastos')
        <div class="w-full space-y-3 pt-4">
            @if ($dueExpenses->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay gastos recurrentes vencidos.
                </p>
            @else
                @foreach ($dueExpenses as $expense)
                    <a href="{{ route('admin.expenses.index') }}" class="flex items-center gap-3 rounded-xl bg-marca-mostaza/15 px-4 py-3 text-sm hover:ring-2 hover:ring-marca-mostaza">
                        <span class="text-marca-negro">Toca pagar <strong>{{ $expense->description }}</strong> ({{ \App\Models\Expense::FREQUENCIES[$expense->frequency] ?? $expense->frequency }}) — último pago {{ $expense->incurred_on->format('d/m/Y') }}.</span>
                        <span class="ml-auto shrink-0 text-xs font-semibold text-marca-negro">Ver gastos →</span>
                    </a>
                @endforeach
            @endif
        </div>
    @endif

    {{-- ===== Vencimiento de promociones ===== --}}
    @if ($activeTab === 'promociones')
        <div class="w-full space-y-3 pt-4">
            @if ($expiringPromotions->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay promociones por vencer en los próximos 7 días.
                </p>
            @else
                @foreach ($expiringPromotions as $promotion)
                    <a href="{{ route('admin.products.index', ['tab' => 'promociones']) }}" class="flex items-center gap-3 rounded-xl bg-marca-amarillo/10 px-4 py-3 text-sm hover:ring-2 hover:ring-marca-amarillo">
                        <span class="text-marca-negro"><strong>{{ $promotion->title }}</strong> vence el {{ $promotion->ends_at->format('d/m/Y') }}.</span>
                        <span class="ml-auto shrink-0 text-xs font-semibold text-marca-negro">Ver promociones →</span>
                    </a>
                @endforeach
            @endif
        </div>
    @endif
@endsection
