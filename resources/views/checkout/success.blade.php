@extends('layouts.app')

@section('title', 'Pedido recibido — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-2xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">

            @php
                $pagado = $order->isPaid();
                $mpFailed = $mpStatus === 'failure';
            @endphp

            <div class="rounded-3xl bg-marca-gris-claro p-8 text-center sm:p-10">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full
                             {{ $mpFailed ? 'bg-marca-rojo text-marca-blanco' : 'bg-marca-amarillo text-marca-negro' }}">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        @if ($mpFailed)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        @endif
                    </svg>
                </span>

                <h1 class="mt-5 text-2xl font-extrabold text-marca-negro sm:text-3xl">
                    @if ($mpFailed)
                        No se pudo completar el pago
                    @elseif ($pagado)
                        ¡Pago confirmado!
                    @else
                        ¡Pedido recibido!
                    @endif
                </h1>

                <p class="mt-2 text-sm text-marca-gris-oscuro">
                    Pedido <span class="font-bold text-marca-negro">#{{ $order->id }}</span> ·
                    Estado: <span class="font-bold text-marca-negro">{{ ucfirst($order->status) }}</span>
                </p>

                @if ($mpFailed)
                    <p class="mt-4 text-sm text-marca-gris-oscuro">El pago con Mercado Pago no se concretó. Podés reintentar desde el carrito o coordinar por WhatsApp.</p>
                @elseif ($order->payment_method === \App\Models\Order::PAYMENT_WHATSAPP)
                    <p class="mt-4 text-sm text-marca-gris-oscuro">Te esperamos en WhatsApp para coordinar el pago y la entrega.</p>
                    @if ($whatsappLink)
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
                           class="mt-5 inline-flex items-center justify-center rounded-full bg-marca-amarillo px-7 py-3 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Abrir WhatsApp
                        </a>
                    @endif
                @elseif (! $pagado)
                    <p class="mt-4 text-sm text-marca-gris-oscuro">Estamos esperando la confirmación de Mercado Pago. Apenas se acredite, el pedido pasa a “pagado”.</p>
                @endif
            </div>

            {{-- Detalle --}}
            <div class="mt-6 rounded-2xl border border-marca-gris-claro p-6">
                <h2 class="text-sm font-bold uppercase tracking-wide text-marca-gris-oscuro">Detalle</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-3">
                            <span class="text-marca-gris-oscuro">{{ $item->quantity }} × {{ $item->product_name }}</span>
                            <span class="font-semibold text-marca-negro">{{ $item->formatted_subtotal }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-3 flex justify-between border-t border-marca-gris-claro pt-3 text-base font-extrabold text-marca-negro">
                    <span>Total</span>
                    <span>{{ $order->formatted_total }}</span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">
                    ← Seguir comprando
                </a>
            </div>
        </div>
    </main>

    <x-footer-tienda />
@endsection
