@extends('layouts.admin')

@section('title', 'Nuevo pedido')
@section('page-heading', 'Cargar pedido por WhatsApp')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
    @endphp

    <div>
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf

            {{-- Cliente --}}
            <div class="border-b border-marca-gris-claro pb-6">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Cliente</h2>
                <div>
                    <label for="customer_id" class="{{ $lbl }}">Cliente existente</label>
                    <select id="customer_id" name="customer_id" class="{{ $field }}">
                        <option value="">— Cargar uno nuevo —</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }} · {{ $customer->phone }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="new_customer_name" class="{{ $lbl }}">Nombre <span class="normal-case text-marca-gris-oscuro/50">(si es cliente nuevo)</span></label>
                        <input type="text" id="new_customer_name" name="new_customer_name" value="{{ old('new_customer_name') }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="new_customer_phone" class="{{ $lbl }}">Teléfono</label>
                        <input type="tel" id="new_customer_phone" name="new_customer_phone" value="{{ old('new_customer_phone') }}" placeholder="Solo números" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="new_customer_email" class="{{ $lbl }}">Email</label>
                        <input type="email" id="new_customer_email" name="new_customer_email" value="{{ old('new_customer_email') }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            {{-- Entrega --}}
            <div class="border-b border-marca-gris-claro pb-6" data-delivery>
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Entrega</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-transparent bg-marca-gris-claro p-4 transition has-[:checked]:border-marca-amarillo">
                        <input type="radio" name="delivery_method" value="retiro" class="mt-1 accent-marca-rojo" {{ old('delivery_method', 'retiro') === 'retiro' ? 'checked' : '' }} required>
                        <span class="text-sm font-bold text-marca-negro">Retiro en el local</span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 border-transparent bg-marca-gris-claro p-4 transition has-[:checked]:border-marca-amarillo">
                        <input type="radio" name="delivery_method" value="envio" class="mt-1 accent-marca-rojo" {{ old('delivery_method') === 'envio' ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-marca-negro">Envío a domicilio</span>
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2" data-shipping-fields>
                    <div>
                        <label for="shipping_zone_id" class="{{ $lbl }}">Empresa / zona de envío</label>
                        <select id="shipping_zone_id" name="shipping_zone_id" class="{{ $field }}">
                            <option value="">Elegir…</option>
                            @foreach ($companies as $company)
                                @foreach ($company->zones as $zone)
                                    <option value="{{ $zone->id }}" @selected(old('shipping_zone_id') == $zone->id)>
                                        {{ $company->name }} — {{ $zone->name }} ({{ '$ ' . number_format($zone->price, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="shipping_address" class="{{ $lbl }}">Dirección</label>
                        <input type="text" id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            {{-- Productos --}}
            <div class="border-b border-marca-gris-claro pb-6">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Productos</h2>
                <p class="mb-3 text-xs text-marca-gris-oscuro">Tildá los productos, ajustá cantidad y precio si negociaste algo distinto al de catálogo.</p>
                <div class="max-h-96 space-y-2 overflow-y-auto rounded-lg border border-marca-gris-oscuro/20 p-3">
                    @foreach ($products as $product)
                        <div class="grid grid-cols-1 items-center gap-2 border-b border-marca-gris-claro pb-2 last:border-0 sm:grid-cols-[1fr_auto_auto]">
                            <label class="flex items-center gap-2 text-sm text-marca-negro">
                                <input type="checkbox" name="product_id[]" value="{{ $product->id }}" class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                                {{ $product->name }}
                            </label>
                            <input type="number" min="1" value="1" name="quantity[{{ $product->id }}]" placeholder="Cant." class="w-24 rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-sm">
                            <input type="number" step="0.01" min="0" value="{{ $product->price }}" name="price[{{ $product->id }}]" placeholder="Precio" class="w-28 rounded-lg border border-marca-gris-oscuro/20 px-2 py-1.5 text-sm">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pago --}}
            <div class="border-b border-marca-gris-claro pb-6">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Pago</h2>
                <div class="max-w-xs">
                    <label for="real_payment_method" class="{{ $lbl }}">Cómo pagó</label>
                    <select id="real_payment_method" name="real_payment_method" required class="{{ $field }}">
                        <option value="">Elegir…</option>
                        @foreach ($realPaymentMethods as $value => $label)
                            <option value="{{ $value }}" @selected(old('real_payment_method') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="mt-2 text-xs text-marca-gris-oscuro">El pedido queda cargado como pagado de una.</p>
            </div>

            <div>
                <label for="notes" class="{{ $lbl }}">Aclaraciones (opcional)</label>
                <textarea id="notes" name="notes" rows="2" class="{{ $field }}">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Cargar pedido
                </button>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const fields = document.querySelector('[data-shipping-fields]');
            function sync() {
                const envio = document.querySelector('input[name="delivery_method"]:checked')?.value === 'envio';
                fields.classList.toggle('hidden', !envio);
            }
            document.querySelectorAll('input[name="delivery_method"]').forEach((el) => el.addEventListener('change', sync));
            sync();
        })();
    </script>
@endsection
