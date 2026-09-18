@extends('layouts.app')

@section('title', 'Carrito — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">

            <h1 class="mb-8 text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">Tu carrito</h1>

            @if ($lines->isEmpty())
                {{-- Carrito vacío --}}
                <div class="rounded-2xl bg-marca-gris-claro px-6 py-16 text-center">
                    <p class="text-lg font-semibold text-marca-negro">Todavía no agregaste productos.</p>
                    <p class="mt-1 text-sm text-marca-gris-oscuro">Explorá el catálogo y sumá lo que necesites para tu moto.</p>
                    <a href="{{ route('products.index') }}"
                       class="mt-6 inline-flex rounded-full bg-marca-amarillo px-7 py-3 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                        Volver al catálogo
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

                    {{-- Lista de líneas --}}
                    <div class="lg:col-span-2">
                        <ul class="divide-y divide-marca-gris-claro rounded-2xl bg-marca-gris-claro/50">
                            @foreach ($lines as $line)
                                <li class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:gap-5">

                                    {{-- Imagen chica --}}
                                    <a href="{{ route('products.show', $line->product) }}"
                                       class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-marca-gris-claro">
                                        @if ($line->product->image_url)
                                            <img src="{{ $line->product->image_url }}" alt="{{ $line->product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <svg viewBox="0 0 200 200" class="h-2/5 w-2/5 text-marca-gris-oscuro/25" fill="none" stroke="currentColor" stroke-width="4">
                                                <circle cx="100" cy="100" r="55"/><path d="M100 55v90M55 100h90" stroke-linecap="round"/>
                                            </svg>
                                        @endif
                                    </a>

                                    {{-- Nombre + precio unitario --}}
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('products.show', $line->product) }}" class="block text-sm font-semibold text-marca-negro transition hover:text-marca-rojo">
                                            {{ $line->product->name }}
                                        </a>
                                        <p class="mt-1 text-xs text-marca-gris-oscuro">
                                            {{ $line->product->formatted_price }} c/u · {{ $line->product->brand }}
                                        </p>
                                        @if ($line->promotion)
                                            <span class="mt-1 inline-block rounded-full bg-marca-rojo/10 px-2 py-0.5 text-[10px] font-bold uppercase text-marca-rojo">
                                                {{ $line->promotion->label }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Selector de cantidad --}}
                                    <form method="POST" action="{{ route('cart.update', $line->product) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $line->quantity }}" min="1" max="{{ max($line->product->stock, 1) }}"
                                               onchange="this.form.requestSubmit()"
                                               class="h-10 w-16 rounded-lg border border-marca-gris-oscuro/20 bg-marca-blanco text-center text-sm font-bold text-marca-negro focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                                        <button type="submit" class="hidden text-xs font-semibold text-marca-gris-oscuro underline sm:inline">Actualizar</button>
                                    </form>

                                    {{-- Subtotal de la línea --}}
                                    <div class="w-24 text-right">
                                        @if ($line->promotion)
                                            <p class="text-xs text-marca-gris-oscuro/60 line-through">$ {{ number_format($line->original_subtotal, 0, ',', '.') }}</p>
                                        @endif
                                        <p class="text-base font-extrabold text-marca-negro">
                                            $ {{ number_format($line->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Quitar --}}
                                    <form method="POST" action="{{ route('cart.remove', $line->product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Quitar del carrito"
                                                class="flex h-9 w-9 items-center justify-center rounded-full text-marca-gris-oscuro transition hover:bg-marca-rojo/10 hover:text-marca-rojo">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5h6v2m-8 0l1 12h8l1-12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">
                                ← Seguir comprando
                            </a>
                            <form method="POST" action="{{ route('cart.clear') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">
                                    Vaciar carrito
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Resumen --}}
                    <aside class="lg:col-span-1">
                        <div class="sticky top-6 rounded-2xl bg-marca-gris-claro p-6">
                            <h2 class="text-lg font-extrabold text-marca-negro">Resumen</h2>

                            <dl class="mt-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-marca-gris-oscuro">Subtotal</dt>
                                    <dd class="font-semibold text-marca-negro">$ {{ number_format($subtotal, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-marca-gris-oscuro">Envío</dt>
                                    <dd class="text-marca-gris-oscuro">Se calcula al coordinar</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex justify-between border-t border-marca-gris-oscuro/15 pt-4">
                                <span class="text-base font-extrabold text-marca-negro">Total</span>
                                <span class="text-xl font-extrabold text-marca-negro">$ {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            <a href="{{ route('checkout.show') }}"
                               class="mt-6 inline-flex w-full items-center justify-center rounded-full bg-marca-amarillo px-6 py-3.5 text-base font-extrabold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                                Ir a pagar
                            </a>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </main>

@endsection
