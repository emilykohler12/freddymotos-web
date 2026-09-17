@extends('layouts.admin')

@section('title', 'Productos')
@section('page-heading', 'Productos / Inventario')

@section('content')
    @php
        $sorts = [
            'name_asc' => 'Nombre A-Z', 'name_desc' => 'Nombre Z-A',
            'price_asc' => 'Precio menor-mayor', 'price_desc' => 'Precio mayor-menor',
            'stock' => 'Stock', 'category' => 'Categoría', 'brand' => 'Marca',
        ];
    @endphp

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex flex-1 flex-wrap gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre..."
                   class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
            <select name="category_id" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="sort" onchange="this.form.submit()" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                @foreach ($sorts as $value => $label)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-sm font-semibold text-marca-negro hover:border-marca-amarillo">Filtrar</button>
        </form>
        <a href="{{ route('admin.products.create') }}" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            + Añadir producto
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Producto</th>
                        <th class="px-5 py-3 font-semibold">SKU</th>
                        <th class="px-5 py-3 font-semibold">Categoría</th>
                        <th class="px-5 py-3 font-semibold">Marca</th>
                        <th class="px-5 py-3 font-semibold">Modelo</th>
                        <th class="px-5 py-3 font-semibold">Precio</th>
                        <th class="px-5 py-3 font-semibold">Stock</th>
                        <th class="px-5 py-3 font-semibold">Estado</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-marca-gris-claro">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-5 py-3 font-medium text-marca-negro">{{ $product->name }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->sku ?: '—' }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->category }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->brand }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->compatible_models ? implode(', ', $product->compatible_models) : '—' }}</td>
                            <td class="px-5 py-3 text-marca-negro">{{ $product->formatted_price }}</td>
                            <td class="px-5 py-3">
                                <span class="font-semibold {{ $product->stock <= 0 ? 'text-marca-rojo' : ($product->stock <= 5 ? 'text-marca-mostaza' : 'text-marca-negro') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->active ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                    {{ $product->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar {{ $product->name }}?');" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-10 text-center text-marca-gris-oscuro">No hay productos que coincidan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
@endsection
