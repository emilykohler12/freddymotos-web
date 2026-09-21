{{-- Seccion "Marcas más vendidas" - Home. 100% real: ranking por unidades vendidas en pedidos pagos. --}}
{{-- Si todavía no hay ventas, la sección no se muestra (nada de datos de ejemplo). --}}

@php
    $marcas = \App\Models\OrderItem::query()
        ->join('products', 'products.id', '=', 'order_items.product_id')
        ->join('orders', 'orders.id', '=', 'order_items.order_id')
        ->where(fn ($q) => $q->where('orders.payment_status', \App\Models\Order::PAYMENT_STATUS_PAGADO)
            ->orWhere('orders.status', \App\Models\Order::STATUS_PAGADO))
        ->selectRaw('products.brand as brand, SUM(order_items.quantity) as total_qty')
        ->groupBy('products.brand')
        ->orderByDesc('total_qty')
        ->take(6)
        ->get();
    $marcasMax = max(1, (int) $marcas->max('total_qty'));
@endphp

@if ($marcas->isNotEmpty())
<section class="w-full bg-marca-bordo py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-10 sm:mb-12">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-amarillo">
                Lo que más elige la gente
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight text-marca-blanco sm:text-4xl">
                Marcas más vendidas
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($marcas as $i => $marca)
                <a href="{{ route('products.index', ['brand' => $marca->brand]) }}" class="group relative block overflow-hidden rounded-2xl bg-marca-blanco/5 p-5 ring-1 ring-marca-blanco/10 transition hover:bg-marca-blanco/10 hover:ring-marca-amarillo/50">
                    <span class="text-4xl font-black text-marca-blanco/10">#{{ $i + 1 }}</span>
                    <p class="mt-2 truncate text-lg font-extrabold text-marca-blanco" title="{{ $marca->brand }}">{{ $marca->brand }}</p>
                    <p class="text-xs text-marca-blanco/50">{{ $marca->total_qty }} {{ $marca->total_qty === 1 ? 'unidad vendida' : 'unidades vendidas' }}</p>
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-marca-blanco/10">
                        <div class="h-full rounded-full bg-marca-amarillo transition-all" style="width: {{ max(6, round(($marca->total_qty / $marcasMax) * 100)) }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
