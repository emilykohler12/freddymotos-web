@extends('layouts.admin')

@section('title', 'Gastos')
@section('page-heading', 'Gastos')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <p class="mb-6 text-sm text-marca-gris-oscuro">
        Total del mes: <strong class="text-marca-negro">{{ $money($totalThisMonth) }}</strong>
    </p>

    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="expenses-tab" id="tab-categoria" class="peer/categoria hidden" @checked($activeTab === 'categoria')>
        <label for="tab-categoria" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/categoria:bg-marca-negro peer-checked/categoria:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
            Categoría
        </label>

        <dialog id="newCategoryModal" class="w-full max-w-sm rounded-2xl p-0 backdrop:bg-marca-negro/50">
            <div class="p-5">
                <h2 class="mb-4 text-sm font-bold text-marca-negro">Nueva categoría</h2>
                <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="type" value="gasto">
                    <input type="text" name="name" placeholder="Nombre de la categoría" required class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                    <select name="parent_id" class="w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
                        <option value="">Principal (sin padre)</option>
                        @foreach ($categories as $cat)
                            @if ($cat->parent_id === null)
                                <option value="{{ $cat->id }}">→ Subcategoría de: {{ $cat->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('newCategoryModal').close()" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold text-marca-negro transition hover:border-marca-amarillo">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-marca-amarillo px-4 py-2 text-xs font-semibold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">Crear</button>
                    </div>
                </form>
            </div>
        </dialog>

        <input type="radio" name="expenses-tab" id="tab-nuevo-gasto" class="peer/nuevogasto hidden" @checked($activeTab === 'nuevo-gasto')>
        <label for="tab-nuevo-gasto" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/nuevogasto:bg-marca-negro peer-checked/nuevogasto:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo gasto
        </label>

        <input type="radio" name="expenses-tab" id="tab-pendientes" class="peer/pendientes hidden" @checked($activeTab === 'pendientes')>
        <label for="tab-pendientes" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pendientes:bg-marca-negro peer-checked/pendientes:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pendientes a pagar
        </label>

        <input type="radio" name="expenses-tab" id="tab-pagadas" class="peer/pagadas hidden" @checked($activeTab === 'pagadas')>
        <label for="tab-pagadas" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/pagadas:bg-marca-negro peer-checked/pagadas:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Pagadas
        </label>

        {{-- ===== Categoría ===== --}}
        <div class="hidden w-full pt-4 peer-checked/categoria:block">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-bold text-marca-negro">Categorías existentes</h2>
                            <button type="button" onclick="document.getElementById('newCategoryModal').showModal()" class="rounded-lg bg-marca-amarillo px-3 py-1.5 text-xs font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">+ Nueva</button>
                        </div>
                        <form method="GET" data-autosubmit class="flex gap-2">
                            <input type="hidden" name="tab" value="categoria">
                            <input type="text" name="category_search" value="{{ $categorySearch }}" placeholder="Buscar..." class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs focus:border-marca-amarillo focus:outline-none">
                            <select name="category_sort" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs">
                                <option value="name_asc" @selected($categorySort === 'name_asc')>A-Z</option>
                                <option value="name_desc" @selected($categorySort === 'name_desc')>Z-A</option>
                            </select>
                        </form>
                    </div>
                    <div class="space-y-3" id="categoriesList">
                        @forelse ($categoryList as $category)
                            <div class="rounded-lg bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5" draggable="true" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <input type="text" class="flex-1 bg-transparent text-marca-negro font-semibold border-0 p-0 focus:ring-0 focus:outline-none text-sm category-name-input" value="{{ $category->name }}" data-category-id="{{ $category->id }}">
                                    <button type="button" class="text-xs font-semibold text-marca-rojo hover:underline category-delete-btn" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">Eliminar</button>
                                </div>
                                @if ($category->children->isNotEmpty())
                                    <ul class="space-y-2 pl-3 border-l-2 border-marca-gris-claro">
                                        @foreach ($category->children as $child)
                                            <li class="flex items-center justify-between gap-2 rounded px-2 py-2 bg-marca-gris-claro text-xs" draggable="true" data-child-id="{{ $child->id }}" data-parent-id="{{ $category->id }}" data-child-name="{{ $child->name }}">
                                                <input type="text" class="flex-1 bg-transparent text-marca-gris-oscuro font-medium border-0 p-0 focus:ring-0 focus:outline-none text-xs category-name-input" value="{{ $child->name }}" data-category-id="{{ $child->id }}">
                                                <button type="button" class="text-xs font-semibold text-marca-rojo hover:underline category-delete-btn" data-category-id="{{ $child->id }}" data-category-name="{{ $child->name }}">Eliminar</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @empty
                            <div class="py-6 text-center text-marca-gris-oscuro rounded-lg bg-marca-blanco p-4 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                                {{ $categorySearch !== '' ? 'No hay categorías que coincidan con "'.$categorySearch.'".' : 'Sin categorías todavía.' }}
                            </div>
                        @endforelse
                    </div>

                    <script>
                        // Editar nombre de categoría inline
                        document.querySelectorAll('.category-name-input').forEach(function(input) {
                            const originalValue = input.value;
                            input.addEventListener('blur', function() {
                                if (this.value !== originalValue && this.value.trim()) {
                                    const categoryId = this.getAttribute('data-category-id');
                                    const formData = new FormData();
                                    formData.append('_method', 'PUT');
                                    formData.append('_token', '{{ csrf_token() }}');
                                    formData.append('name', this.value);

                                    fetch('/admin/gastos-categorias/' + categoryId, {
                                        method: 'POST',
                                        body: formData
                                    }).then(r => r.ok ? location.reload() : alert('Error al actualizar'));
                                }
                            });
                        });

                        // Eliminar categoría
                        document.querySelectorAll('.category-delete-btn').forEach(function(btn) {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                var categoryId = this.getAttribute('data-category-id');
                                var categoryName = this.getAttribute('data-category-name');

                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '/admin/gastos-categorias/' + categoryId;
                                form.innerHTML = '@csrf @method("DELETE")<input type="hidden" name="type" value="gasto">';
                                form.setAttribute('data-confirm', '¿Eliminar ' + categoryName + '?');

                                document.body.appendChild(form);
                                form.requestSubmit();
                            });
                        });

                        // Drag and drop para categorías y subcategorías
                        var draggedElement = null;

                        document.querySelectorAll('[data-category-id], [data-child-id]').forEach(function(el) {
                            el.addEventListener('dragstart', function(e) {
                                draggedElement = this;
                                this.style.opacity = '0.5';
                            });
                            el.addEventListener('dragend', function(e) {
                                this.style.opacity = '1';
                                draggedElement = null;
                            });
                        });

                        // Permitir drop en categorías padre (para mover subcategorías o categorías)
                        document.querySelectorAll('[data-category-id]').forEach(function(el) {
                            el.addEventListener('dragover', function(e) {
                                e.preventDefault();
                                if (draggedElement && draggedElement !== this) {
                                    this.style.backgroundColor = '#fff3cd';
                                }
                            });
                            el.addEventListener('dragleave', function(e) {
                                this.style.backgroundColor = '';
                            });
                            el.addEventListener('drop', function(e) {
                                e.preventDefault();
                                this.style.backgroundColor = '';

                                if (!draggedElement || draggedElement === this) return;

                                var newParentId = this.getAttribute('data-category-id');
                                var childId = draggedElement.getAttribute('data-child-id') || draggedElement.getAttribute('data-category-id');

                                if (childId && newParentId && childId !== newParentId) {
                                    var formData = new FormData();
                                    formData.append('_method', 'PUT');
                                    formData.append('_token', '{{ csrf_token() }}');
                                    formData.append('parent_id', newParentId);

                                    fetch('/admin/gastos-categorias/' + childId, {
                                        method: 'POST',
                                        body: formData
                                    }).then(r => r.ok ? location.reload() : alert('Error al mover'));
                                }
                            });
                        });
                    </script>
                </div>

            </div>
        </div>

        {{-- ===== Nuevo gasto ===== --}}
        <div class="hidden w-full pt-4 peer-checked/nuevogasto:block">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Registrar gasto</h2>
                    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="type" value="gasto">
                        <input type="hidden" name="tab" value="nuevo-gasto">
                        <input type="text" name="description" placeholder="Descripción" required class="{{ $field }}">
                        <input type="number" step="0.01" min="0" name="amount" placeholder="Monto" required class="{{ $field }}">
                        <select name="expense_category_id" class="{{ $field }}">
                            <option value="">Sin categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select name="frequency" required class="{{ $field }}">
                            @foreach ($frequencies as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="incurred_on" value="{{ now()->toDateString() }}" required class="{{ $field }}">
                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Registrar
                        </button>
                    </form>
                </div>

                <div class="space-y-4 lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-sm font-bold text-marca-negro">Registro de gastos</h2>
                        <form method="GET" data-autosubmit class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="tab" value="nuevo-gasto">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por descripción..." class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs focus:border-marca-amarillo focus:outline-none">
                            <label for="frequency" class="text-xs text-marca-gris-oscuro">Frecuencia:</label>
                            <select id="frequency" name="frequency" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs">
                                <option value="">Todas</option>
                                @foreach ($frequencies as $value => $label)
                                    <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    @if ($allExpenses->isEmpty())
                        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            Todavía no hay gastos registrados.
                        </p>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($allExpenses as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'nuevo-gasto'])
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Pendientes a pagar (separadas por categoría) ===== --}}
        <div class="hidden w-full space-y-8 pt-4 peer-checked/pendientes:block">
            @foreach ($pendingByCategory as $group)
                @if ($group['items']->isNotEmpty())
                    <div>
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">{{ $group['category']->name }}</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($group['items'] as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pendientes'])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($pendingWithoutCategory->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Sin categoría</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($pendingWithoutCategory as $item)
                            @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pendientes'])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($pendingByCategory->every(fn ($g) => $g['items']->isEmpty()) && $pendingWithoutCategory->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    No hay gastos pendientes de pago.
                </p>
            @endif
        </div>

        {{-- ===== Pagadas (separadas por categoría) ===== --}}
        <div class="hidden w-full space-y-8 pt-4 peer-checked/pagadas:block">
            @foreach ($paidByCategory as $group)
                @if ($group['items']->isNotEmpty())
                    <div>
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">{{ $group['category']->name }}</h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach ($group['items'] as $item)
                                @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pagadas'])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            @if ($paidWithoutCategory->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-marca-negro">Sin categoría</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($paidWithoutCategory as $item)
                            @include('admin.expenses._card', ['item' => $item, 'categories' => $categories, 'activeTab' => 'pagadas'])
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($paidByCategory->every(fn ($g) => $g['items']->isEmpty()) && $paidWithoutCategory->isEmpty())
                <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    Todavía no hay gastos pagados.
                </p>
            @endif
        </div>
    </div>
@endsection
