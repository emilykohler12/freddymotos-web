@extends('layouts.admin')

@section('title', $product->exists ? 'Editar producto' : 'Nuevo producto')
@section('page-heading', $product->exists ? 'Editar producto' : 'Nuevo producto')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
        $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro';
    @endphp

    <div class="max-w-3xl">
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

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label for="price" class="{{ $lbl }}">Precio de venta</label>
                    <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $product->price) }}" required class="{{ $field }}">
                </div>
                <div>
                    <label for="cost_price" class="{{ $lbl }}">Precio de compra</label>
                    <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" class="{{ $field }}">
                </div>
                <div>
                    <label for="stock" class="{{ $lbl }}">Stock</label>
                    <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required class="{{ $field }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="{{ $lbl }}">Categoría</label>
                    <select id="category_id" name="category_id" required class="{{ $field }}">
                        <option value="">Elegir…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
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

            <div>
                <label for="compatible_model" class="{{ $lbl }}">Modelo de moto compatible</label>
                <input type="text" id="compatible_model" name="compatible_model" value="{{ old('compatible_model', $product->compatible_model) }}" class="{{ $field }}">
            </div>

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
                <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) class="h-4 w-4 rounded border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                    Destacado (Home)
                </label>
            </div>

            <div class="flex items-center gap-3 border-t border-marca-gris-claro pt-5">
                <button type="submit" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    {{ $product->exists ? 'Guardar cambios' : 'Crear producto' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-marca-gris-oscuro transition hover:text-marca-rojo">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
