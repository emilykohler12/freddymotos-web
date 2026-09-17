@extends('layouts.app')

@section('title', 'Checkout — ' . $settings->nombre_local)

@section('content')
    <x-site-nav />

    <main class="w-full bg-marca-blanco">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">

            <h1 class="mb-8 text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">Finalizar compra</h1>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">
                    Revisá los datos: {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                @csrf

                {{-- ============ Columna izquierda: datos ============ --}}
                <div class="space-y-8 lg:col-span-2">

                    {{-- Datos del cliente --}}
                    <section class="rounded-2xl bg-marca-gris-claro p-6">
                        <h2 class="text-lg font-extrabold text-marca-negro">Tus datos</h2>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nombre y apellido *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                       class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                            </div>
                            <div>
                                <label for="phone" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Teléfono *</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                                       class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                            </div>
                            <div>
                                <label for="email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email (opcional)</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                            </div>
                        </div>
                    </section>

                    {{-- Método de entrega --}}
                    <section class="rounded-2xl bg-marca-gris-claro p-6" data-delivery>
                        <h2 class="text-lg font-extrabold text-marca-negro">Entrega</h2>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-transparent bg-marca-blanco p-4 transition has-[:checked]:border-marca-amarillo">
                                <input type="radio" name="delivery_method" value="retiro" class="mt-1 accent-marca-rojo"
                                       {{ old('delivery_method', 'retiro') === 'retiro' ? 'checked' : '' }} required>
                                <span>
                                    <span class="block text-sm font-bold text-marca-negro">Retiro en el local</span>
                                    <span class="block text-xs text-marca-gris-oscuro">{{ $settings->direccion }}</span>
                                </span>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-transparent bg-marca-blanco p-4 transition has-[:checked]:border-marca-amarillo">
                                <input type="radio" name="delivery_method" value="envio" class="mt-1 accent-marca-rojo"
                                       {{ old('delivery_method') === 'envio' ? 'checked' : '' }}>
                                <span>
                                    <span class="block text-sm font-bold text-marca-negro">Envío a domicilio</span>
                                    <span class="block text-xs text-marca-gris-oscuro">Coordinamos el costo después</span>
                                </span>
                            </label>
                        </div>

                        <div class="mt-4" data-address-field>
                            <label for="address" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">
                                Dirección <span data-address-required class="hidden">*</span>
                            </label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                   placeholder="Calle, número, localidad"
                                   class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">
                        </div>
                    </section>

                    {{-- Método de pago --}}
                    <section class="rounded-2xl bg-marca-gris-claro p-6">
                        <h2 class="text-lg font-extrabold text-marca-negro">Pago</h2>
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer flex-col gap-1 rounded-xl border-2 border-transparent bg-marca-blanco p-4 transition has-[:checked]:border-marca-rojo has-[:checked]:bg-marca-rojo/5">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="payment_method" value="mercadopago" class="accent-marca-rojo"
                                           {{ old('payment_method', 'mercadopago') === 'mercadopago' ? 'checked' : '' }} required>
                                    <span class="text-sm font-bold text-marca-negro">Pagar online</span>
                                </span>
                                <span class="pl-6 text-xs text-marca-gris-oscuro">Con Mercado Pago (tarjeta, débito, dinero en cuenta).</span>
                            </label>
                            <label class="flex cursor-pointer flex-col gap-1 rounded-xl border-2 border-transparent bg-marca-blanco p-4 transition has-[:checked]:border-marca-rojo has-[:checked]:bg-marca-rojo/5">
                                <span class="flex items-center gap-2">
                                    <input type="radio" name="payment_method" value="whatsapp" class="accent-marca-rojo"
                                           {{ old('payment_method') === 'whatsapp' ? 'checked' : '' }}>
                                    <span class="text-sm font-bold text-marca-negro">Coordinar por WhatsApp</span>
                                </span>
                                <span class="pl-6 text-xs text-marca-gris-oscuro">Te contactamos para acordar pago y entrega. Sin pago online.</span>
                            </label>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Aclaraciones (opcional)</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="w-full rounded-lg border border-marca-gris-oscuro/15 bg-marca-blanco px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40">{{ old('notes') }}</textarea>
                        </div>
                    </section>
                </div>

                {{-- ============ Columna derecha: resumen ============ --}}
                <aside class="lg:col-span-1">
                    <div class="sticky top-6 rounded-2xl bg-marca-negro p-6 text-marca-blanco">
                        <h2 class="text-lg font-extrabold">Tu pedido</h2>

                        <ul class="mt-4 space-y-3 border-b border-marca-blanco/15 pb-4 text-sm">
                            @foreach ($lines as $line)
                                <li class="flex justify-between gap-3">
                                    <span class="text-marca-blanco/80">{{ $line->quantity }} × {{ $line->product->name }}</span>
                                    <span class="shrink-0 font-semibold">$ {{ number_format($line->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 flex justify-between text-base font-extrabold">
                            <span>Total</span>
                            <span>$ {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" data-submit-btn
                                class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-marca-amarillo px-6 py-3.5 text-base font-extrabold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Pagar con Mercado Pago
                        </button>

                        <a href="{{ route('cart.index') }}" class="mt-3 block text-center text-xs font-semibold text-marca-blanco/60 transition hover:text-marca-blanco">
                            Volver al carrito
                        </a>
                    </div>
                </aside>
            </form>
        </div>
    </main>

    <x-footer-tienda />

    <script>
        (function () {
            const btn = document.querySelector('[data-submit-btn]');
            const addrRequired = document.querySelector('[data-address-required]');
            const addrInput = document.getElementById('address');

            function sync() {
                const pay = document.querySelector('input[name="payment_method"]:checked')?.value;
                btn.textContent = pay === 'whatsapp' ? 'Enviar pedido por WhatsApp' : 'Pagar con Mercado Pago';

                const delivery = document.querySelector('input[name="delivery_method"]:checked')?.value;
                const envio = delivery === 'envio';
                addrRequired.classList.toggle('hidden', !envio);
                if (addrInput) addrInput.required = envio;
            }

            document.querySelectorAll('input[name="payment_method"], input[name="delivery_method"]')
                .forEach((el) => el.addEventListener('change', sync));
            sync();
        })();
    </script>
@endsection
