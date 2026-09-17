@extends('layouts.admin')

@section('title', 'Clientes')
@section('page-heading', 'Clientes')

@section('content')
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex flex-1 gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, teléfono o email..."
                   class="min-w-[220px] flex-1 rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none">
            <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-sm font-semibold text-marca-negro hover:border-marca-amarillo">Buscar</button>
        </form>
        <a href="{{ route('admin.customers.create') }}" class="shrink-0 rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            + Añadir cliente
        </a>
    </div>


    <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nombre</th>
                        <th class="px-5 py-3 font-semibold">Teléfono</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Ciudad</th>
                        <th class="px-5 py-3 font-semibold">Pedidos</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-marca-gris-claro">
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="px-5 py-3 font-medium text-marca-negro">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="hover:text-marca-rojo">{{ $customer->name }}</a>
                            </td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $customer->phone }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $customer->email ?: '—' }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $customer->city ?: '—' }}</td>
                            <td class="px-5 py-3 text-marca-negro">{{ $customer->orders_count }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</a>
                                    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('¿Eliminar {{ $customer->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-marca-gris-oscuro">Todavía no hay clientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
