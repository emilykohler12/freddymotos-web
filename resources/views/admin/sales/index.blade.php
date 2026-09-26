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
            <button type="button" onclick="document.getElementById('nuevoIngresoForm').classList.toggle('hidden')" class="inline-flex items-center gap-2 rounded-lg bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Cargar ingreso
            </button>
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
                <select name="sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="recent" @selected($sort === 'recent')>Más reciente</option>
                    <option value="oldest" @selected($sort === 'oldest')>Más antiguo</option>
                    <option value="name_asc" @selected($sort === 'name_asc')>Cliente A-Z</option>
                    <option value="name_desc" @selected($sort === 'name_desc')>Cliente Z-A</option>
                </select>
                <select name="shipment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los envíos</option>
                    <option value="pending" @selected($shipment === 'pending')>Envío pendiente</option>
                </select>
                <select name="payment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los pagos</option>
                    <option value="pending" @selected($payment === 'pending')>Pago pendiente</option>
                </select>
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
                                    <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', [$order, 'from' => 'web']) }}'">
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
                <select name="sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="recent" @selected($sort === 'recent')>Más reciente</option>
                    <option value="oldest" @selected($sort === 'oldest')>Más antiguo</option>
                    <option value="name_asc" @selected($sort === 'name_asc')>Cliente A-Z</option>
                    <option value="name_desc" @selected($sort === 'name_desc')>Cliente Z-A</option>
                </select>
                <select name="shipment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los envíos</option>
                    <option value="pending" @selected($shipment === 'pending')>Envío pendiente</option>
                </select>
                <select name="payment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los pagos</option>
                    <option value="pending" @selected($payment === 'pending')>Pago pendiente</option>
                </select>
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
                                    <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', [$order, 'from' => 'whatsapp']) }}'">
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
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="local">
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por N° o cliente..." class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                <select name="sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="recent" @selected($sort === 'recent')>Más reciente</option>
                    <option value="oldest" @selected($sort === 'oldest')>Más antiguo</option>
                    <option value="name_asc" @selected($sort === 'name_asc')>Cliente A-Z</option>
                    <option value="name_desc" @selected($sort === 'name_desc')>Cliente Z-A</option>
                </select>
                <select name="shipment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los envíos</option>
                    <option value="pending" @selected($shipment === 'pending')>Envío pendiente</option>
                </select>
                <select name="payment" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="">Todos los pagos</option>
                    <option value="pending" @selected($payment === 'pending')>Pago pendiente</option>
                </select>
            </form>

            @if ($local->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay ventas locales cargadas.
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
                                @foreach ($local as $order)
                                    <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', [$order, 'from' => 'local']) }}'">
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
                <div>{{ $local->links() }}</div>
            @endif
        </div>
        @endif

        {{-- Ingresos Tab --}}
        @if ($activeTab === 'ingresos')
        <div class="w-full space-y-5 pt-4">
            <form method="GET" data-autosubmit class="flex flex-wrap gap-2">
                <input type="hidden" name="tab" value="ingresos">
                <input type="text" name="ingresos_search" value="{{ $ingresosSearch }}" placeholder="Buscar por cliente o N° pedido..." class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                <select name="ingresos_sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <option value="recent" @selected($ingresosSort === 'recent')>Más reciente</option>
                    <option value="oldest" @selected($ingresosSort === 'oldest')>Más antiguo</option>
                    <option value="name_asc" @selected($ingresosSort === 'name_asc')>Cliente A-Z</option>
                    <option value="name_desc" @selected($ingresosSort === 'name_desc')>Cliente Z-A</option>
                </select>
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
                                    <tr class="cursor-pointer hover:bg-marca-gris-claro/40" onclick="location.href='{{ route('admin.orders.show', [$order, 'from' => 'ingresos']) }}'">
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

            <form id="nuevoIngresoForm" method="POST" action="{{ route('admin.expenses.store') }}" class="hidden space-y-4 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                @csrf
                <input type="hidden" name="type" value="ingreso">
                <h3 class="font-bold text-marca-negro">Nuevo ingreso</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <input type="text" name="description" placeholder="Descripción" required class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <input type="date" name="incurred_on" value="{{ date('Y-m-d') }}" required class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <input type="number" name="amount" placeholder="Monto" step="0.01" required class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <button type="submit" class="rounded-lg bg-marca-amarillo px-4 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Guardar</button>
                </div>
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
                                            <button type="button" onclick="document.getElementById('editIngresoForm{{ $income->id }}').classList.toggle('hidden')" class="hover:text-marca-negro">Editar →</button>
                                        </td>
                                    </tr>
                                    <tr id="editIngresoForm{{ $income->id }}" class="hidden">
                                        <td colspan="4" class="px-5 py-4">
                                            <form method="POST" action="{{ route('admin.expenses.update', $income) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="tab" value="otros">
                                                <input type="text" name="description" value="{{ $income->description }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                                                <input type="date" name="incurred_on" value="{{ $income->incurred_on->format('Y-m-d') }}" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                                                <input type="number" name="amount" value="{{ $income->amount }}" step="0.01" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                                                <div class="col-span-full flex gap-2">
                                                    <button type="submit" class="rounded-lg bg-marca-amarillo px-4 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Guardar</button>
                                                    <button type="button" onclick="document.getElementById('editIngresoForm{{ $income->id }}').classList.toggle('hidden')" class="rounded-lg bg-marca-gris-claro px-4 py-2 text-sm font-bold text-marca-gris-oscuro transition hover:bg-marca-rojo hover:text-marca-blanco">Cancelar</button>
                                                </div>
                                            </form>
                                            <form method="POST" action="{{ route('admin.expenses.destroy', $income) }}" data-confirm="¿Eliminar este ingreso?" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="tab" value="otros">
                                                <button type="submit" class="rounded-lg bg-marca-rojo px-4 py-2 text-sm font-bold text-marca-blanco transition hover:bg-marca-negro">Eliminar</button>
                                            </form>
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
