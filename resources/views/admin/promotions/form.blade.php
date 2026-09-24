@extends('layouts.admin')

@section('title', $promotion->exists ? 'Editar promoción' : 'Nueva promoción')
@section('page-heading', $promotion->exists ? 'Editar promoción' : 'Nueva promoción')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
        $selectedProducts = old('product_ids', $promotion->products->pluck('id')->all());
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

        <form method="POST" action="{{ $promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}"
              class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            @if ($promotion->exists) @method('PUT') @endif

            <div>
                <label for="title" class="{{ $lbl }}">Nombre de la promoción</label>
                <input type="text" id="title" name="title" value="{{ old('title', $promotion->title) }}" placeholder="Ej: Cascos 2x1, 20% OFF en frenos" required class="{{ $field }}">
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Tipo de descuento</h2>

                <div class="mb-4">
                    <label for="template" class="{{ $lbl }}">Plantilla rápida <span class="normal-case text-marca-gris-oscuro/50">(opcional: completa tipo y valor por vos)</span></label>
                    <select id="template" class="max-w-xs {{ $field }}">
                        <option value="">Elegir…</option>
                        <option value="nxm:2:1">2x1</option>
                        <option value="nxm:3:2">3x2</option>
                        <option value="nxm:4:3">4x3</option>
                        <option value="percentage:10">10% OFF</option>
                        <option value="percentage:15">15% OFF</option>
                        <option value="percentage:20">20% OFF</option>
                        <option value="percentage:25">25% OFF</option>
                        <option value="percentage:30">30% OFF</option>
                        <option value="percentage:40">40% OFF</option>
                        <option value="percentage:50">50% OFF</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="type" class="{{ $lbl }}">Tipo</label>
                        <select id="type" name="type" required class="{{ $field }}">
                            <option value="percentage" @selected(old('type', $promotion->type) === 'percentage')>Porcentaje (%)</option>
                            <option value="fixed" @selected(old('type', $promotion->type) === 'fixed')>Monto fijo ($)</option>
                            <option value="nxm" @selected(old('type', $promotion->type) === 'nxm')>Cantidad x cantidad (2x1, 3x2...)</option>
                        </select>
                    </div>
                    <div>
                        <label for="value" class="{{ $lbl }}">Valor <span class="normal-case text-marca-gris-oscuro/50">(% o $, según el tipo)</span></label>
                        <input type="number" step="0.01" min="0" id="value" name="value" value="{{ old('value', $promotion->value) }}" class="{{ $field }}">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="buy_quantity" class="{{ $lbl }}">Paga</label>
                            <input type="number" min="2" id="buy_quantity" name="buy_quantity" value="{{ old('buy_quantity', $promotion->buy_quantity) }}" placeholder="Ej: 2" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="pay_quantity" class="{{ $lbl }}">Lleva</label>
                            <input type="number" min="1" id="pay_quantity" name="pay_quantity" value="{{ old('pay_quantity', $promotion->pay_quantity) }}" placeholder="Ej: 1" class="{{ $field }}">
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-xs text-marca-gris-oscuro">
                    Usá "Valor" si el tipo es Porcentaje o Monto fijo. Usá "Paga" / "Lleva" si el tipo es Cantidad x cantidad (ej. 2x1).
                </p>
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">A qué aplica</h2>
                <div class="max-w-xs">
                    <label for="scope" class="{{ $lbl }}">Alcance</label>
                    <select id="scope" name="scope" required class="{{ $field }}">
                        <option value="all" @selected(old('scope', $promotion->scope) === 'all')>Todo el catálogo</option>
                        <option value="category" @selected(old('scope', $promotion->scope) === 'category')>Una categoría</option>
                        <option value="products" @selected(old('scope', $promotion->scope) === 'products')>Productos puntuales</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label for="category_id" class="{{ $lbl }}">Categoría <span class="normal-case text-marca-gris-oscuro/50">(si el alcance es "Una categoría")</span></label>
                    <select id="category_id" name="category_id" class="max-w-xs {{ $field }}">
                        <option value="">Elegir…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $promotion->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label class="{{ $lbl }}">Productos <span class="normal-case text-marca-gris-oscuro/50">(si el alcance es "Productos puntuales")</span></label>
                    <div class="max-h-48 columns-1 gap-4 overflow-y-auto rounded-lg border border-marca-gris-oscuro/20 p-3 sm:columns-2 lg:columns-3">
                        @forelse ($products as $product)
                            <label class="mb-1 flex items-center gap-2 break-inside-avoid text-sm text-marca-negro">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedProducts)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                                {{ $product->name }}
                            </label>
                        @empty
                            <p class="text-sm text-marca-gris-oscuro">Todavía no hay productos cargados.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-marca-negro">Vigencia</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="starts_at" class="{{ $lbl }}">Desde <span class="normal-case text-marca-gris-oscuro/50">(opcional)</span></label>
                        <input type="date" id="starts_at" name="starts_at" value="{{ old('starts_at', optional($promotion->starts_at)->toDateString()) }}" class="{{ $field }}">
                    </div>
                    <div>
                        <label for="ends_at" class="{{ $lbl }}">Hasta <span class="normal-case text-marca-gris-oscuro/50">(opcional)</span></label>
                        <input type="date" id="ends_at" name="ends_at" value="{{ old('ends_at', optional($promotion->ends_at)->toDateString()) }}" class="{{ $field }}">
                    </div>
                </div>
            </div>

            <div class="border-t border-marca-gris-claro pt-5">
                <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                    <input type="checkbox" name="active" value="1" @checked(old('active', $promotion->exists ? $promotion->active : true)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                    Activa (se muestra en el Home)
                </label>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    {{ $promotion->exists ? 'Guardar cambios' : 'Crear promoción' }}
                </button>
                <a href="{{ route('admin.promotions.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        (function () {
            var templateSelect = document.getElementById('template');
            var titleInput = document.getElementById('title');
            var typeSelect = document.getElementById('type');
            var valueInput = document.getElementById('value');
            var buyInput = document.getElementById('buy_quantity');
            var payInput = document.getElementById('pay_quantity');

            templateSelect.addEventListener('change', function () {
                if (!templateSelect.value) return;
                var parts = templateSelect.value.split(':');
                var type = parts[0];

                typeSelect.value = type;

                if (type === 'nxm') {
                    buyInput.value = parts[1];
                    payInput.value = parts[2];
                    valueInput.value = '';
                    if (!titleInput.value) titleInput.value = parts[1] + 'x' + parts[2];
                } else {
                    valueInput.value = parts[1];
                    buyInput.value = '';
                    payInput.value = '';
                    if (!titleInput.value) titleInput.value = parts[1] + '% OFF';
                }
            });
        })();
    </script>
@endsection
