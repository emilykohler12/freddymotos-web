@extends('layouts.admin')

@section('title', $product->exists ? 'Editar repuesto' : 'Nuevo repuesto')
@section('page-heading', $product->exists ? 'Editar repuesto' : 'Nuevo repuesto')

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

        <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
              enctype="multipart/form-data" class="space-y-6 rounded-2xl bg-marca-blanco p-6 shadow-sm ring-1 ring-marca-gris-oscuro/5">
            @csrf
            @if ($product->exists) @method('PUT') @endif

            <div>
                <label for="name" class="{{ $lbl }}">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required class="{{ $field }}">
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="sku" class="{{ $lbl }}">Código / SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="brand" class="{{ $lbl }}">Marca</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}" required class="{{ $field }}">
                </div>
            </div>

            <div>
                <label for="description" class="{{ $lbl }}">Descripción</label>
                <textarea id="description" name="description" rows="3" class="{{ $field }}">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="price" class="{{ $lbl }}">Precio de venta</label>
                    <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $product->price) }}" required class="{{ $field }}">
                </div>
                <div>
                    <label for="cost_price" class="{{ $lbl }}">Precio de compra</label>
                    <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="{{ $field }}">
                </div>
            </div>

            @if ($product->exists)
                <div class="flex items-center justify-between rounded-xl bg-marca-gris-claro px-4 py-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Stock actual</p>
                        <p class="text-lg font-extrabold text-marca-negro">{{ $product->stock }} unidades</p>
                    </div>
                    <a href="{{ route('admin.activity.index') }}#tab-inventario" class="text-xs font-semibold text-marca-rojo hover:underline">Ajustar en Inventario →</a>
                </div>
            @else
                <p class="-mt-2 text-xs text-marca-gris-oscuro">
                    El stock inicial se carga después desde
                    <a href="{{ route('admin.activity.index') }}#tab-inventario" class="font-semibold text-marca-rojo hover:underline">Movimientos → Inventario</a>.
                </p>
            @endif
            <p class="-mt-4 text-xs text-marca-gris-oscuro">
                Los descuentos y combos (2x1, % OFF, etc.) se cargan desde
                <a href="{{ route('admin.promotions.index') }}" class="font-semibold text-marca-rojo hover:underline">Promociones</a>.
            </p>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="category_input" class="{{ $lbl }}">Categoría</label>
                    <input type="text" id="category_input" placeholder="Buscar o escribir…" autocomplete="off"
                           class="{{ $field }}" data-category-search>
                    <input type="hidden" id="category_id" name="category_id" required
                           value="{{ old('category_id', $product->category_id) }}">
                    <div class="absolute z-10 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-marca-gris-oscuro/20 bg-marca-blanco shadow-md"
                         data-category-dropdown style="display: none; position: absolute;">
                    </div>
                </div>
                <div>
                    <label for="supplier_id" class="{{ $lbl }}">Proveedor</label>
                    <select id="supplier_id" name="supplier_id" class="{{ $field }}">
                        <option value="">Sin especificar</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <script>
                (function () {
                    var categories = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values());
                    var input = document.getElementById('category_input');
                    var hiddenInput = document.getElementById('category_id');
                    var dropdown = document.querySelector('[data-category-dropdown]');
                    var currentSelected = parseInt(hiddenInput.value) || null;

                    function updateDropdown() {
                        var query = input.value.toLowerCase();
                        var filtered = categories.filter(function (cat) {
                            return cat.name.toLowerCase().includes(query);
                        });

                        dropdown.innerHTML = '';

                        if (filtered.length === 0) {
                            dropdown.style.display = 'none';
                            return;
                        }

                        filtered.forEach(function (cat) {
                            var option = document.createElement('div');
                            option.className = 'px-3 py-2 cursor-pointer hover:bg-marca-amarillo/20 text-sm';
                            option.textContent = cat.name;
                            option.addEventListener('click', function () {
                                input.value = cat.name;
                                hiddenInput.value = cat.id;
                                currentSelected = cat.id;
                                dropdown.style.display = 'none';
                            });
                            dropdown.appendChild(option);
                        });

                        dropdown.style.display = 'block';
                    }

                    function setDisplayValue() {
                        if (currentSelected) {
                            var selected = categories.find(function (c) { return c.id === currentSelected; });
                            if (selected) input.value = selected.name;
                        }
                    }

                    input.addEventListener('focus', updateDropdown);
                    input.addEventListener('input', updateDropdown);

                    document.addEventListener('click', function (e) {
                        if (e.target !== input && e.target !== dropdown && !dropdown.contains(e.target)) {
                            dropdown.style.display = 'none';
                        }
                    });

                    setDisplayValue();
                })();
            </script>

            <div>
                <label for="compatible_model" class="{{ $lbl }}">Modelo de moto compatible <span class="normal-case text-marca-gris-oscuro/50">(uno por línea — se muestran como items)</span></label>
                <textarea id="compatible_model" name="compatible_model" rows="3" placeholder="Ej: Honda CB 250 Twister&#10;Honda XR 250&#10;Yamaha YBR 125" class="{{ $field }}">{{ old('compatible_model', $product->compatible_model) }}</textarea>
            </div>

            {{-- Detalles de la categoría (ej: Colores en Cascos, Medidas en Neumáticos). Se muestran solo los de la categoría elegida. --}}
            @if ($categories->pluck('attributes')->flatten()->isNotEmpty())
                <div data-category-attributes-wrapper>
                    <p class="{{ $lbl }}">Detalles de la categoría</p>
                    <div class="grid grid-cols-1 gap-4 rounded-xl bg-marca-gris-claro p-4 sm:grid-cols-2">
                        @forelse ($categories as $category)
                            @if ($category->attributes->isNotEmpty())
                                <template data-category-attributes-group="{{ $category->id }}">
                                    @foreach ($category->attributes as $attribute)
                                        <div>
                                            <label for="attr-{{ $attribute->id }}" class="{{ $lbl }}">{{ $attribute->name }}</label>
                                            <input type="text" id="attr-{{ $attribute->id }}" name="attributes[{{ $attribute->id }}]"
                                                   value="{{ old('attributes.' . $attribute->id, $attributeValues[$attribute->id] ?? '') }}" class="{{ $field }}">
                                        </div>
                                    @endforeach
                                </template>
                            @endif
                        @empty
                        @endforelse
                        <p data-category-attributes-empty class="col-span-full text-sm text-marca-gris-oscuro">Esta categoría no tiene detalles configurados. Se agregan desde Categorías.</p>
                    </div>
                </div>

                <script>
                    (function () {
                        var wrapper = document.querySelector('[data-category-attributes-wrapper]');
                        var container = wrapper.querySelector('.grid');
                        var emptyMsg = wrapper.querySelector('[data-category-attributes-empty]');
                        var templates = wrapper.querySelectorAll('template[data-category-attributes-group]');
                        var categorySelect = document.getElementById('category_id');

                        function render() {
                            container.querySelectorAll('[data-rendered-group]').forEach(function (el) { el.remove(); });
                            var categoryId = categorySelect.value;
                            var matched = false;

                            templates.forEach(function (tpl) {
                                if (tpl.getAttribute('data-category-attributes-group') === categoryId) {
                                    matched = true;
                                    var clone = tpl.content.cloneNode(true);
                                    var group = document.createElement('div');
                                    group.setAttribute('data-rendered-group', '');
                                    group.className = 'contents';
                                    group.appendChild(clone);
                                    container.appendChild(group);
                                }
                            });

                            emptyMsg.classList.toggle('hidden', matched);
                        }

                        categorySelect.addEventListener('change', render);
                        render();
                    })();
                </script>
            @endif

            <div>
                <label for="image" class="{{ $lbl }}">Imagen</label>
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mb-2 h-24 w-24 rounded-lg object-cover ring-1 ring-marca-gris-oscuro/10">
                @endif
                <input type="file" id="image" name="image" accept="image/*" class="{{ $field }}">
            </div>

            <div class="flex flex-wrap gap-6 border-t border-marca-gris-claro pt-5">
                <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                    <input type="checkbox" name="active" value="1" @checked(old('active', $product->exists ? $product->active : true)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                    Activo
                </label>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    {{ $product->exists ? 'Guardar cambios' : 'Crear repuesto' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
