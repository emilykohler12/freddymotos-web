{{-- Tarjeta de un gasto individual: usada dentro de los bloques por categoría y en "Desactivados". --}}
@php
    $field = $field ?? 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2 text-sm focus:border-marca-amarillo focus:outline-none';
    $money = fn ($n) => '$ ' . number_format((float) $n, 0, ',', '.');
    $frequencies = \App\Models\Expense::FREQUENCIES;
    $cardTab = $activeTab ?? request('tab', 'categoria');
@endphp

<div class="rounded-2xl bg-marca-blanco p-5 shadow-sm ring-1 ring-marca-gris-oscuro/5">
    <div class="flex items-start justify-between gap-2">
        <p class="font-bold text-marca-negro">{{ $item->description }}</p>
        @if ($item->paid)
            <span class="shrink-0 rounded-full bg-marca-amarillo/20 px-2 py-0.5 text-[10px] font-bold uppercase text-marca-negro">Pagado</span>
        @endif
    </div>
    <dl class="mt-2 space-y-1 text-sm text-marca-gris-oscuro">
        <div>Monto: <span class="font-medium text-marca-negro">{{ $money($item->amount) }}</span></div>
        <div>Frecuencia: <span class="font-medium text-marca-negro">{{ $frequencies[$item->frequency] ?? 'Única' }}</span></div>
        <div>Fecha: <span class="font-medium text-marca-negro">{{ $item->incurred_on->format('d/m/Y') }}</span></div>
    </dl>
    <div class="mt-3 flex flex-wrap items-center gap-3 border-t border-marca-gris-claro pt-3">
        <details class="[&_summary]:list-none">
            <summary class="cursor-pointer text-xs font-semibold text-marca-negro hover:text-marca-amarillo [&::-webkit-details-marker]:hidden">Editar</summary>
            <form method="POST" action="{{ route('admin.expenses.update', $item) }}" class="mt-3 space-y-2">
                @csrf @method('PUT')
                <input type="hidden" name="type" value="{{ $item->type }}">
                <input type="hidden" name="tab" value="{{ $cardTab }}">
                <input type="text" name="description" value="{{ $item->description }}" required class="{{ $field }}">
                <input type="number" step="0.01" min="0" name="amount" value="{{ $item->amount }}" required class="{{ $field }}">
                <select name="expense_category_id" class="{{ $field }}">
                    <option value="">Sin categoría</option>
                    @foreach (($categories ?? \App\Models\ExpenseCategory::where('type', $item->type)->orderBy('name')->get()) as $category)
                        <option value="{{ $category->id }}" @selected($item->expense_category_id === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="frequency" required class="{{ $field }}">
                    @foreach ($frequencies as $value => $label)
                        <option value="{{ $value }}" @selected($item->frequency === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="date" name="incurred_on" value="{{ $item->incurred_on->toDateString() }}" required class="{{ $field }}">
                <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold hover:border-marca-amarillo">Guardar</button>
            </form>
        </details>

        @if ($item->type !== \App\Models\Expense::TYPE_INGRESO)
            <form method="POST" action="{{ route('admin.expenses.toggle-paid', $item) }}" class="inline-flex">
                @csrf
                <input type="hidden" name="tab" value="{{ $cardTab }}">
                <button type="submit" class="text-xs font-semibold text-marca-gris-oscuro hover:text-marca-negro">
                    {{ $item->paid ? 'Reactivar' : 'Marcar pagado' }}
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('admin.expenses.destroy', $item) }}" data-confirm="¿Eliminar este registro?" class="ml-auto inline-flex">
            @csrf @method('DELETE')
            <input type="hidden" name="tab" value="{{ $cardTab }}">
            <button type="submit" class="text-xs font-semibold text-marca-rojo hover:underline">Eliminar</button>
        </form>
    </div>
</div>
