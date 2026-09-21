{{-- Seccion "Promociones" - Home. Promociones reales cargadas por el admin en /admin/promociones --}}
{{-- (porcentaje, monto fijo o Nx M como 2x1/3x2). Si no hay ninguna activa, no se muestra. --}}

@php
    try {
        $promos = \App\Models\Promotion::with(['category', 'products'])->vigentes()->latest()->take(4)->get();
    } catch (\Throwable $e) {
        $promos = collect();
    }
@endphp

@if ($promos->isNotEmpty())
<section class="w-full bg-marca-gris-claro py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mx-auto mb-10 max-w-2xl text-center sm:mb-14">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-marca-rojo">
                Aprovechá
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">
                Promociones
            </h2>
            <p class="mt-3 text-base text-marca-gris-oscuro">
                Descuentos y combos activos ahora mismo.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($promos as $promo)
                <div class="flex flex-col overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5 transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-center bg-gradient-to-br from-marca-rojo to-marca-bordo px-4 py-8 text-center">
                        <span class="text-3xl font-extrabold tracking-tight text-marca-blanco">{{ $promo->label }}</span>
                    </div>
                    @php $singleProduct = $promo->scope === \App\Models\Promotion::SCOPE_PRODUCTS && $promo->products->count() === 1 ? $promo->products->first() : null; @endphp
                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <h3 class="font-bold text-marca-negro">{{ $promo->title }}</h3>
                        <p class="text-sm text-marca-gris-oscuro">Aplica a: {{ $promo->scope_label }}</p>
                        @if ($singleProduct)
                            <form action="{{ route('cart.add') }}" method="POST" data-cart-add class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $singleProduct->id }}">
                                <button type="submit" @disabled(! $singleProduct->in_stock)
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco disabled:cursor-not-allowed disabled:bg-marca-gris-claro disabled:text-marca-gris-oscuro/50 disabled:hover:bg-marca-gris-claro">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-9 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $singleProduct->in_stock ? 'Agregar al carrito' : 'Sin stock' }}
                                </button>
                            </form>
                        @else
                            <a href="{{ $promo->target_url }}" class="mt-auto inline-flex items-center justify-center gap-2 rounded-full bg-marca-amarillo px-4 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                                Ver productos
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
