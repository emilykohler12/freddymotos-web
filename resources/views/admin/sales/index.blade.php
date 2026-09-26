@extends('layouts.admin')

@section('title', 'Ventas')
@section('page-heading', 'Ventas')

@section('content')
    @php
        $estados = [
            'pendiente' => 'bg-marca-mostaza/15 text-marca-mostaza',
            'enviado' => 'bg-marca-amarillo/20 text-marca-negro', 'entregado' => 'bg-marca-amarillo/20 text-marca-negro',
            'pagado' => 'bg-marca-amarillo/20 text-marca-negro', 'cancelado' => 'bg-marca-rojo/10 text-marca-rojo',
            'rechazado' => 'bg-marca-rojo/10 text-marca-rojo',
        ];
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
        $paymentMethodLabels = ['mercadopago' => 'Mercado Pago', 'whatsapp' => 'A coordinar (WhatsApp)'] + \App\Models\Order::REAL_PAYMENT_METHODS;
        $paymentStatuses = ['pendiente' => 'Pendiente', 'pagado' => 'Pagado', 'rechazado' => 'Rechazado'];
        $statuses = ['pendiente' => 'Pendiente', 'enviado' => 'Enviado', 'entregado' => 'Entregado', 'cancelado' => 'Cancelado'];
    @endphp

    <div class="mb-4 flex justify-end">
        @if ($activeTab === 'local')
            <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Cargar pedido
            </a>
        @elseif ($activeTab === 'otros')
            <a href="{{ route('admin.expenses.index', ['tab' => 'otros']) }}" class="inline-flex items-center gap-2 rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Cargar ingreso
            </a>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="flex flex-wrap items-start gap-2 mb-6">
        <a href="{{ route('admin.sales.index', ['tab' => 'web']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'web' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Web
        </a>

        <a href="{{ route('admin.sales.index', ['tab' => 'whatsapp']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'whatsapp' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            WhatsApp
        </a>

        <a href="{{ route('admin.sales.index', ['tab' => 'local']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'local' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0cm0 -1.667m0 1.667H9"/></svg>
            Local
        </a>

        <a href="{{ route('admin.sales.index', ['tab' => 'ingresos']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'ingresos' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Ingresos
        </a>

        <a href="{{ route('admin.sales.index', ['tab' => 'otros']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'otros' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2 0-3 1-3 2s1 2 3 2 3 1 3 2-1 2-3 2m0-10V6m0 12v-2m8-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Otros Ingresos
        </a>

        {{-- Web Tab --}}
        @if ($activeTab === 'web')
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="web">
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por N° o cliente..." class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            </form>

            @if ($web->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ventas desde el sitio web.
                </p>
            @else
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
                                @foreach ($web as $order)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $web->links() }}</div>
            @endif
        </div>
        @endif

        {{-- WhatsApp Tab --}}
        @if ($activeTab === 'whatsapp')
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="whatsapp">
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por N° o cliente..." class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            </form>

            @if ($whatsapp->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ventas desde WhatsApp.
                </p>
            @else
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
                                @foreach ($whatsapp as $order)
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $whatsapp->links() }}</div>
            @endif
        </div>
        @endif

        {{-- Local Tab --}}
        @if ($activeTab === 'local')
        <div class="w-full pt-4">
            <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                Ventas locales disponibles próximamente.
            </p>
        </div>
        @endif

        {{-- Ingresos Tab --}}
        @if ($activeTab === 'ingresos')
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="ingresos">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            </form>

            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <span class="inline-block h-1.5 w-8 rounded-full bg-marca-amarillo"></span>
                <p class="mt-3 text-sm text-marca-gris-oscuro">Total cobrado en ventas</p>
                <p class="mt-1 text-2xl font-extrabold text-marca-negro">
                    {{ $money($paidOrders->sum('total')) }}
                </p>
            </div>

            @if ($paidOrders->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ingresos de ventas.
                </p>
            @else
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
                                @foreach ($paidOrders as $order)
                                    <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', $order) }}'">
                                        <td class="px-5 py-3 font-medium text-marca-negro">#{{ $order->id }}</td>
                                        <td class="px-5 py-3 text-marca-gris-oscuro">{{ optional($order->paid_at)->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-5 py-3 text-marca-negro">{{ $order->customer->name ?? '—' }}</td>
                                        <td class="px-5 py-3 font-semibold text-marca-negro">{{ $order->formatted_total }}</td>
                                        <td class="px-5 py-3 text-marca-gris-oscuro">{{ $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                        <td class="px-5 py-3 text-right text-xs font-semibold text-marca-rojo">Ver →</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $paidOrders->links() }}</div>
            @endif
        </div>
        @endif

        {{-- Otros Ingresos Tab --}}
        @if ($activeTab === 'otros')
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="otros">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
            </form>

            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <span class="inline-block h-1.5 w-8 rounded-full bg-marca-amarillo"></span>
                <p class="mt-3 text-sm text-marca-gris-oscuro">Total en otros ingresos</p>
                <p class="mt-1 text-2xl font-extrabold text-marca-negro">
                    {{ $money($otherIncome->sum('amount')) }}
                </p>
            </div>

            @if ($otherIncome->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay otros ingresos registrados.
                </p>
            @else
                <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Descripción</th>
                                    <th class="px-5 py-3 font-semibold">Fecha</th>
                                    <th class="px-5 py-3 font-semibold">Monto</th>
                                    <th class="px-5 py-3 font-semibold"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-marca-gris-claro">
                                @foreach ($otherIncome as $income)
                                    <tr class="hover:bg-marca-gris-claro/40">
                                        <td class="px-5 py-3 font-medium text-marca-negro">{{ $income->description }}</td>
                                        <td class="px-5 py-3 text-marca-gris-oscuro">{{ $income->incurred_on->format('d/m/Y') }}</td>
                                        <td class="px-5 py-3 font-semibold text-marca-negro">{{ $money($income->amount) }}</td>
                                        <td class="px-5 py-3 text-right text-xs font-semibold text-marca-amarillo">
                                            <a href="{{ route('admin.expenses.index') }}" class="hover:text-marca-negro">Editar →</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $otherIncome->links() }}</div>
            @endif
        </div>
        @endif
    </div>
@endsection
