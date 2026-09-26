@extends('layouts.admin')

@section('title', 'Repuestos')
@section('page-heading', 'Repuestos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $sorts = [
            'name_asc' => 'Nombre A-Z', 'name_desc' => 'Nombre Z-A',
            'price_asc' => 'Precio menor-mayor', 'price_desc' => 'Precio mayor-menor',
            'stock' => 'Stock', 'brand' => 'Marca',
        ];
    @endphp

    {{-- Tabs --}}
    <div class="flex flex-wrap items-start gap-2 mb-6">
        <a href="{{ route('admin.products.index', ['tab' => 'categorias']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'categorias' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
            Categorías
        </a>

        <a href="{{ route('admin.products.index', ['tab' => 'repuestos']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'repuestos' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Repuestos
        </a>

        <a href="{{ route('admin.products.index', ['tab' => 'stock']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'stock' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Stock
        </a>

        <a href="{{ route('admin.products.index', ['tab' => 'promociones']) }}" class="flex cursor-pointer items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeTab === 'promociones' ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-gris-claro text-marca-gris-oscuro hover:bg-marca-gris-claro/70' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M3 11l8-8h6a2 2 0 012 2v6l-8 8a2 2 0 01-2.83 0l-5.17-5.17A2 2 0 013 11z"/></svg>
            Promociones
        </a>

        {{-- Categorías Tab --}}
        @if ($activeTab === 'categorias')
        <div class="w-full pt-4">
            <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data"
                  class="mb-6 flex max-w-xl flex-col gap-3 rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5 sm:flex-row sm:items-center">
                @csrf
                <input type="hidden" name="tab" value="categorias">
                <input type="text" name="name" placeholder="Nombre de la nueva categoría" required class="{{ $field }}">
                <input type="file" name="image" accept="image/*" class="text-xs {{ $field }}">
                <button type="submit" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Crear
                </button>
            </form>

            <form method="GET" action="{{ route('admin.products.index') }}" data-autosubmit
                  class="mb-6 flex w-fit flex-col gap-3 rounded-2xl bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5 sm:flex-row sm:items-center">
                <input type="hidden" name="tab" value="categorias">
                <input type="text" name="search" placeholder="Buscar categoría..." class="{{ $field }} sm:w-56">
                <select name="sort" class="{{ $field }} sm:w-40">
                    <option value="name_asc">Nombre A-Z</option>
                    <option value="name_desc">Nombre Z-A</option>
                </select>
            </form>

            @if ($categories->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Todavía no hay categorías.
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($categories as $category)
                        <details class="group rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                                <span class="flex min-w-0 items-center gap-3">
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-10 w-10 shrink-0 rounded-lg object-cover ring-1 ring-marca-gris-oscuro/10">
                                    @else
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-marca-gris-claro text-marca-gris-oscuro/50">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h.01M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/></svg>
                                        </span>
                                    @endif
                                    <span class="truncate font-medium text-marca-negro">{{ $category->name }}</span>
                                </span>
                                <span class="flex shrink-0 items-center gap-2">
                                    <span class="rounded-full bg-marca-gris-claro px-2.5 py-1 text-xs font-semibold text-marca-gris-oscuro">
                                        {{ $category->products_count }} {{ Str::plural('producto', $category->products_count) }}
                                    </span>
                                    <svg class="h-4 w-4 shrink-0 text-marca-gris-oscuro transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </summary>

                            <div class="mt-4 space-y-3">
                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" value="{{ $category->name }}" required class="{{ $field }}">
                                    <input type="file" name="image" accept="image/*" class="text-xs {{ $field }}">
                                    <button type="submit" class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-xs font-semibold text-marca-negro transition hover:border-marca-amarillo">
                                        Guardar
                                    </button>
                                </form>

                                <div class="border-t border-marca-gris-claro pt-3">
                                    <h4 class="mb-2 text-xs font-bold uppercase text-marca-gris-oscuro">Detalles de la categoría</h4>
                                    @if ($category->attributes->isNotEmpty())
                                        <div class="mb-2 space-y-1.5">
                                            @foreach ($category->attributes as $attribute)
                                                <form method="POST" action="{{ route('admin.categories.attributes.update', [$category, $attribute]) }}" class="flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="name" value="{{ $attribute->name }}" required class="{{ $field }} text-xs py-1.5">
                                                    <button type="submit" class="shrink-0 rounded px-2 py-1.5 text-xs font-semibold text-marca-negro hover:bg-marca-amarillo/20">OK</button>
                                                    <button type="button" onclick="document.getElementById('del-attr-{{ $attribute->id }}').submit()" class="shrink-0 rounded px-2 py-1.5 text-xs font-semibold text-marca-rojo hover:bg-marca-rojo/10">✕</button>
                                                </form>
                                            @endforeach
                                        </div>
                                        @foreach ($category->attributes as $attribute)
                                            <form id="del-attr-{{ $attribute->id }}" style="display: none;" method="POST" action="{{ route('admin.categories.attributes.destroy', [$category, $attribute]) }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endforeach
                                    @endif
                                    <form method="POST" action="{{ route('admin.categories.attributes.store', $category) }}" class="flex gap-2">
                                        @csrf
                                        <input type="text" name="name" placeholder="Nuevo detalle..." required class="{{ $field }} text-xs py-1.5">
                                        <button type="submit" class="shrink-0 rounded-lg bg-marca-amarillo px-3 py-1.5 text-xs font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">+</button>
                                    </form>
                                </div>

                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="¿Eliminar la categoría {{ $category->name }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-lg border border-marca-rojo/30 px-3 py-2 text-xs font-semibold text-marca-rojo transition hover:bg-marca-rojo hover:text-marca-blanco">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </div>
        @endif

        {{-- Repuestos Tab --}}
        @if ($activeTab === 'repuestos')
        <div class="w-full pt-4">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" data-autosubmit class="flex flex-1 flex-wrap gap-2">
                    <input type="hidden" name="tab" value="repuestos">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre..."
                           class="min-w-[180px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <select name="sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm">
                        @foreach ($sorts as $value => $label)
                            <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('admin.products.create') }}" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    + Añadir repuesto
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Repuesto</th>
                                <th class="px-5 py-3 font-semibold">Categoría</th>
                                <th class="px-5 py-3 font-semibold">Marca</th>
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
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->category }}</td>
                                    <td class="px-5 py-3 text-marca-gris-oscuro">{{ $product->brand }}</td>
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
                                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="¿Desactivar {{ $product->name }}?" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-10 text-center text-marca-gris-oscuro">No hay repuestos que coincidan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $products->links() }}</div>
        </div>
        @endif

        {{-- Stock Tab --}}
        @if ($activeTab === 'stock')
        <div class="w-full pt-4">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Ajustar stock</h2>
                    <form method="POST" action="{{ route('admin.stock-movements.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label for="stock-product" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Producto</label>
                            <select id="stock-product" name="product_id" required class="{{ $field }}">
                                <option value="">Elegir…</option>
                                @foreach ($inventoryProducts as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (stock: {{ $product->stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Motivo</label>
                            <div class="flex flex-wrap gap-4">
                                @foreach ($stockReasons as $value => $label)
                                    @if ($value !== \App\Models\StockMovement::REASON_AJUSTE)
                                        <label class="flex items-center gap-2 text-sm font-medium text-marca-negro">
                                            <input type="radio" name="reason" value="{{ $value }}" @checked($loop->first) required class="h-4 w-4 border-marca-gris-oscuro/30 text-marca-amarillo focus:ring-marca-amarillo">
                                            {{ $label }}
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="stock-quantity" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Cantidad</label>
                            <input type="number" id="stock-quantity" name="quantity" min="1" required class="{{ $field }}">
                        </div>

                        <div>
                            <label for="stock-note" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nota (opcional)</label>
                            <input type="text" id="stock-note" name="note" maxlength="255" class="{{ $field }}">
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Guardar movimiento
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Últimos movimientos</h2>
                    @if ($stockMovements->isEmpty())
                        <p class="text-sm text-marca-gris-oscuro">Todavía no hay movimientos de stock.</p>
                    @else
                        <ul class="divide-y divide-marca-gris-claro text-sm">
                            @foreach ($stockMovements as $movement)
                                <li class="flex items-center justify-between gap-3 py-2.5">
                                    <div>
                                        <p class="font-medium text-marca-negro">{{ $movement->product->name ?? 'Producto eliminado' }}</p>
                                        <p class="text-xs text-marca-gris-oscuro">
                                            {{ $movement->reason_label }} · {{ $movement->user->name ?? 'Sistema' }} · {{ $movement->created_at->diffForHumans() }}
                                            @if ($movement->note) · {{ $movement->note }} @endif
                                        </p>
                                    </div>
                                    <span class="shrink-0 font-bold {{ $movement->quantity_change >= 0 ? 'text-marca-amarillo' : 'text-marca-rojo' }}">
                                        {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ $movement->quantity_change }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4">
                @if ($outOfStock->isNotEmpty())
                    <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <h3 class="mb-3 text-sm font-bold text-marca-rojo">Sin stock</h3>
                        <ul class="space-y-2">
                            @foreach ($outOfStock as $product)
                                <li class="flex items-center justify-between rounded-lg border border-marca-rojo/20 bg-marca-rojo/5 px-4 py-3">
                                    <div>
                                        <p class="font-medium text-marca-negro">{{ $product->name }}</p>
                                        <p class="text-xs text-marca-gris-oscuro">{{ $product->brand }}</p>
                                    </div>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-semibold text-marca-rojo hover:text-marca-negro">Editar</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($lowStock->isNotEmpty())
                    <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <h3 class="mb-3 text-sm font-bold text-marca-mostaza">Stock bajo (≤5 unidades)</h3>
                        <ul class="space-y-2">
                            @foreach ($lowStock as $product)
                                <li class="flex items-center justify-between rounded-lg border border-marca-mostaza/20 bg-marca-mostaza/5 px-4 py-3">
                                    <div>
                                        <p class="font-medium text-marca-negro">{{ $product->name }}</p>
                                        <p class="text-xs text-marca-gris-oscuro">{{ $product->stock }} unidades · {{ $product->brand }}</p>
                                    </div>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-semibold text-marca-mostaza hover:text-marca-negro">Editar</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($outOfStock->isEmpty() && $lowStock->isEmpty())
                    <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        Stock suficiente en todos los productos.
                    </p>
                @endif
            </div>
        </div>
        @endif

        {{-- Promociones Tab --}}
        @if ($activeTab === 'promociones')
        <div class="w-full pt-4">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" data-autosubmit class="flex flex-1 flex-wrap gap-2">
                    <input type="hidden" name="tab" value="promociones">
                    <input type="text" name="promotion_search" value="{{ $promotionSearch }}" placeholder="Buscar promoción..." class="min-w-[180px] flex-1 {{ $field }}">
                    <select name="promotion_status" class="{{ $field }}">
                        <option value="" @selected($promotionStatus === '')>Activas e inactivas</option>
                        <option value="active" @selected($promotionStatus === 'active')>Solo activas</option>
                        <option value="inactive" @selected($promotionStatus === 'inactive')>Solo inactivas</option>
                    </select>
                    <select name="promotion_type" class="{{ $field }}">
                        <option value="" @selected($promotionType === '')>Todos los descuentos</option>
                        <option value="percentage" @selected($promotionType === 'percentage')>Porcentaje (%)</option>
                        <option value="fixed" @selected($promotionType === 'fixed')>Monto fijo</option>
                        <option value="nxm" @selected($promotionType === 'nxm')>Combo (2x1, 3x2...)</option>
                    </select>
                    <select name="promotion_sort" class="{{ $field }}">
                        <option value="recent" @selected($promotionSort === 'recent')>Más recientes</option>
                        <option value="title_asc" @selected($promotionSort === 'title_asc')>Nombre A-Z</option>
                        <option value="title_desc" @selected($promotionSort === 'title_desc')>Nombre Z-A</option>
                    </select>
                </form>
                <a href="{{ route('admin.promotions.create') }}" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    + Nueva promoción
                </a>
            </div>

            @if ($promotions->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    @if ($promotionSearch !== '' || $promotionStatus !== '' || $promotionType !== '')
                        No hay promociones que coincidan con el filtro.
                    @else
                        Todavía no hay promociones. Las que actives van a aparecer en la sección "Promociones" del Home.
                    @endif
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($promotions as $promotion)
                        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-marca-negro">{{ $promotion->title }}</p>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $promotion->active ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-gris-claro text-marca-gris-oscuro' }}">
                                    {{ $promotion->active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                            <p class="mt-2 inline-block rounded-full bg-marca-rojo px-3 py-1 text-sm font-extrabold text-marca-blanco">{{ $promotion->label }}</p>
                            <dl class="mt-3 space-y-1 text-sm text-marca-gris-oscuro">
                                <div>Tipo: <span class="font-medium text-marca-negro">{{ $promotion->type_label }}</span></div>
                                <div>Aplica a: <span class="font-medium text-marca-negro">{{ $promotion->scope_label }}</span></div>
                                @if ($promotion->starts_at || $promotion->ends_at)
                                    <div>Vigencia:
                                        <span class="font-medium text-marca-negro">
                                            {{ optional($promotion->starts_at)->format('d/m/Y') ?? 'sin inicio' }}
                                            –
                                            {{ optional($promotion->ends_at)->format('d/m/Y') ?? 'sin fin' }}
                                        </span>
                                    </div>
                                @endif
                            </dl>
                            <div class="mt-3 flex items-center gap-3 border-t border-marca-gris-claro pt-3">
                                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</a>
                                <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" data-confirm="¿Eliminar {{ $promotion->title }}?" class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
@endsection
