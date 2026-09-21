@extends('layouts.admin')

@section('title', 'Proveedores')
@section('page-heading', 'Proveedores')

@section('content')
    <div class="mb-5 flex justify-end">
        <a href="{{ route('admin.suppliers.create') }}" class="rounded-lg bg-marca-amarillo px-5 py-2.5 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
            + Añadir proveedor
        </a>
    </div>


    <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-marca-gris-oscuro/60">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Nombre</th>
                        <th class="px-5 py-3 font-semibold">Empresa</th>
                        <th class="px-5 py-3 font-semibold">Teléfono</th>
                        <th class="px-5 py-3 font-semibold">Productos</th>
                        <th class="px-5 py-3 font-semibold">Deuda</th>
                        <th class="px-5 py-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-marca-gris-claro">
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="px-5 py-3 font-medium text-marca-negro">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="hover:text-marca-rojo">{{ $supplier->name }}</a>
                            </td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $supplier->company ?: '—' }}</td>
                            <td class="px-5 py-3 text-marca-gris-oscuro">{{ $supplier->phone ?: '—' }}</td>
                            <td class="px-5 py-3 text-marca-negro">{{ $supplier->products_count }}</td>
                            <td class="px-5 py-3 {{ $supplier->debt > 0 ? 'font-semibold text-marca-rojo' : 'text-marca-gris-oscuro' }}">{{ $supplier->formatted_debt }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-xs font-semibold text-marca-negro hover:text-marca-amarillo">Editar</a>
                                    <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" data-confirm="¿Eliminar {{ $supplier->name }}?" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-marca-gris-oscuro">Todavía no hay proveedores.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>
@endsection
