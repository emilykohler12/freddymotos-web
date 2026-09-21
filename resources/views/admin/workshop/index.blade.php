@extends('layouts.admin')

@section('title', 'Taller')
@section('page-heading', 'Taller')

@section('content')
    @php
        $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
        $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    @endphp

    <div class="flex flex-wrap items-start gap-2">
        <input type="radio" name="workshop-tab" id="tab-categorias" class="peer/categorias hidden" @checked($activeTab === 'categorias')>
        <label for="tab-categorias" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/categorias:bg-marca-negro peer-checked/categorias:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.3 4.3a1 1 0 011.4 0l1 1a1 1 0 001 .3l1.4-.2a1 1 0 011 .6l.6 1.3a1 1 0 00.7.6l1.4.4a1 1 0 01.7 1.2l-.3 1.4a1 1 0 00.2 1l1 1a1 1 0 010 1.4l-1 1a1 1 0 00-.3 1l.2 1.4a1 1 0 01-.6 1l-1.3.6a1 1 0 00-.6.7l-.4 1.4a1 1 0 01-1.2.7l-1.4-.3a1 1 0 00-1 .2l-1 1a1 1 0 01-1.4 0l-1-1a1 1 0 00-1-.3l-1.4.2a1 1 0 01-1-.6l-.6-1.3a1 1 0 00-.7-.6l-1.4-.4a1 1 0 01-.7-1.2l.3-1.4a1 1 0 00-.2-1l-1-1a1 1 0 010-1.4l1-1a1 1 0 00.3-1L4.3 8a1 1 0 01.6-1l1.3-.6a1 1 0 00.6-.7l.4-1.4zM12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
            Categorías
        </label>

        <input type="radio" name="workshop-tab" id="tab-mecanicos" class="peer/mecanicos hidden" @checked($activeTab === 'mecanicos')>
        <label for="tab-mecanicos" class="flex cursor-pointer items-center gap-2 rounded-full bg-marca-gris-claro px-4 py-2 text-sm font-semibold text-marca-gris-oscuro transition hover:bg-marca-gris-claro/70 peer-checked/mecanicos:bg-marca-negro peer-checked/mecanicos:text-marca-blanco">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z"/></svg>
            Mecánicos
        </label>

        {{-- ===== Categorías ===== --}}
        <div class="hidden w-full pt-4 peer-checked/categorias:block">
            <p class="mb-4 max-w-2xl text-sm text-marca-gris-oscuro">
                Estas categorías se muestran en la página pública de Taller (accesible desde "Taller" en el menú del sitio).
            </p>
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    @if ($categories->isEmpty())
                        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            Todavía no hay categorías de taller.
                        </p>
                    @else
                        <ul class="divide-y divide-marca-gris-claro rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            @foreach ($categories as $categoria)
                                <li class="flex items-center justify-between gap-3 px-5 py-4">
                                    <div>
                                        <p class="font-semibold text-marca-negro">{{ $categoria->name }}</p>
                                        @if ($categoria->description)
                                            <p class="text-sm text-marca-gris-oscuro">{{ $categoria->description }}</p>
                                        @endif
                                        @if ($categoria->formatted_price)
                                            <p class="text-xs text-marca-gris-oscuro/70">{{ $categoria->formatted_price }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('admin.workshop.categories.destroy', $categoria) }}" onsubmit="return confirm('¿Eliminar {{ $categoria->name }}?');" class="inline-flex shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                    <h2 class="mb-3 text-sm font-bold text-marca-negro">Nueva categoría</h2>
                    <form method="POST" action="{{ route('admin.workshop.categories.store') }}" class="space-y-3">
                        @csrf
                        <input type="text" name="name" placeholder="Nombre (ej: Cambio de aceite)" required class="{{ $field }}">
                        <textarea name="description" rows="2" placeholder="Descripción (opcional)" class="{{ $field }}"></textarea>
                        <input type="number" step="0.01" min="0" name="price" placeholder="Precio (opcional)" class="{{ $field }}">
                        <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                            Crear
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===== Mecánicos (interno, no se muestra en el sitio público) ===== --}}
        <div class="hidden w-full pt-4 peer-checked/mecanicos:block">
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    @if ($mechanics->isEmpty())
                        <p class="rounded-2xl bg-marca-blanco px-5 py-10 text-center text-sm text-marca-gris-oscuro shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            Todavía no hay mecánicos cargados.
                        </p>
                    @else
                        @foreach ($mechanics as $mechanic)
                            <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                                <div class="flex items-start justify-between gap-2 border-b border-marca-gris-claro pb-3">
                                    <div>
                                        <p class="font-bold text-marca-negro">{{ $mechanic->name }}</p>
                                        <p class="text-xs text-marca-gris-oscuro">
                                            {{ $mechanic->phone ?: 'Sin teléfono' }}
                                            @if ($mechanic->email) · {{ $mechanic->email }} @endif
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.mechanics.destroy', $mechanic) }}" onsubmit="return confirm('¿Eliminar a {{ $mechanic->name }} y todos sus trabajos registrados?');" class="shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                    </form>
                                </div>

                                @if ($mechanic->jobs->isEmpty())
                                    <p class="pt-3 text-sm text-marca-gris-oscuro">Sin trabajos registrados todavía.</p>
                                @else
                                    <ul class="divide-y divide-marca-gris-claro">
                                        @foreach ($mechanic->jobs as $job)
                                            <li class="py-3">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="font-semibold text-marca-negro">{{ $job->moto }}</p>
                                                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $job->pagado ? 'bg-marca-amarillo/20 text-marca-negro' : 'bg-marca-rojo/10 text-marca-rojo' }}">
                                                        {{ $job->pagado ? 'Pagado' : 'A pagar' }}
                                                    </span>
                                                </div>
                                                <dl class="mt-1 space-y-1 text-sm text-marca-gris-oscuro">
                                                    <div><span class="font-medium text-marca-negro">Problema:</span> {{ $job->problema }}</div>
                                                    @if ($job->repuestos)
                                                        <div><span class="font-medium text-marca-negro">Repuestos:</span> {{ $job->repuestos }}</div>
                                                    @endif
                                                    <div>Debe cobrar: <span class="font-medium text-marca-negro">{{ $job->formatted_monto }}</span></div>
                                                    <div class="text-xs">{{ $job->created_at->diffForHumans() }}</div>
                                                </dl>
                                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                                    <details class="[&_summary]:list-none">
                                                        <summary class="cursor-pointer text-xs font-semibold text-marca-negro hover:text-marca-amarillo [&::-webkit-details-marker]:hidden">Editar</summary>
                                                        <form method="POST" action="{{ route('admin.mechanic-jobs.update', $job) }}" class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                                            @csrf @method('PUT')
                                                            <input type="hidden" name="mechanic_id" value="{{ $mechanic->id }}">
                                                            <input type="text" name="moto" value="{{ $job->moto }}" required placeholder="Moto" class="sm:col-span-2 {{ $field }}">
                                                            <textarea name="problema" rows="2" required placeholder="Qué necesitaba y por qué" class="sm:col-span-2 {{ $field }}">{{ $job->problema }}</textarea>
                                                            <textarea name="repuestos" rows="2" placeholder="Repuestos que necesitó" class="sm:col-span-2 {{ $field }}">{{ $job->repuestos }}</textarea>
                                                            <input type="number" step="0.01" min="0" name="monto_a_pagar" value="{{ $job->monto_a_pagar }}" required placeholder="Monto" class="{{ $field }}">
                                                            <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Guardar</button>
                                                        </form>
                                                    </details>
                                                    <form method="POST" action="{{ route('admin.mechanic-jobs.toggle-paid', $job) }}" class="inline-flex">
                                                        @csrf
                                                        <button type="submit" class="text-xs font-semibold text-marca-gris-oscuro hover:text-marca-negro">
                                                            {{ $job->pagado ? 'Marcar como pendiente' : 'Marcar pagado' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.mechanic-jobs.destroy', $job) }}" onsubmit="return confirm('¿Eliminar este trabajo?');" class="ml-auto inline-flex">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                        <h2 class="mb-3 text-sm font-bold text-marca-negro">Añadir mecánico</h2>
                        <form method="POST" action="{{ route('admin.mechanics.store') }}" class="space-y-3">
                            @csrf
                            <input type="text" name="name" placeholder="Nombre" required class="{{ $field }}">
                            <input type="text" name="phone" placeholder="Teléfono" class="{{ $field }}">
                            <input type="email" name="email" placeholder="Correo (opcional)" class="{{ $field }}">
                            <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                                Añadir
                            </button>
                        </form>
                    </div>

                    @if ($mechanics->isNotEmpty())
                        <div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
                            <h2 class="mb-3 text-sm font-bold text-marca-negro">Registrar trabajo</h2>
                            <form method="POST" action="{{ route('admin.mechanic-jobs.store') }}" class="space-y-3">
                                @csrf
                                <select name="mechanic_id" required class="{{ $field }}">
                                    <option value="">Mecánico…</option>
                                    @foreach ($mechanics as $mechanic)
                                        <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="moto" placeholder="Moto (ej: Honda Wave 110)" required class="{{ $field }}">
                                <textarea name="problema" rows="2" placeholder="Qué necesitaba arreglarse y por qué" required class="{{ $field }}"></textarea>
                                <textarea name="repuestos" rows="2" placeholder="Repuestos que necesitó (opcional)" class="{{ $field }}"></textarea>
                                <input type="number" step="0.01" min="0" name="monto_a_pagar" placeholder="Monto" required class="{{ $field }}">
                                <button type="submit" class="w-full rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                                    Registrar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
