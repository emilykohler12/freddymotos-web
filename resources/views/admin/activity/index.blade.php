@extends('layouts.admin')

@section('title', 'Movimientos')
@section('page-heading', 'Movimientos y notificaciones')

@section('content')
    <div class="overflow-hidden rounded-2xl bg-marca-blanco shadow-sm ring-1 ring-marca-gris-oscuro/5">
        @if ($logs->isEmpty())
            <p class="px-5 py-10 text-center text-sm text-marca-gris-oscuro">Todavía no hay movimientos registrados.</p>
        @else
            <ul class="divide-y divide-marca-gris-claro">
                @foreach ($logs as $log)
                    <li class="flex items-start gap-4 px-5 py-4">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-marca-gris-claro text-xs font-bold uppercase text-marca-gris-oscuro">
                            {{ Str::substr($log->type, 0, 1) }}
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-marca-negro">{{ $log->description }}</p>
                            <p class="mt-0.5 text-xs text-marca-gris-oscuro">
                                {{ ucfirst($log->type) }} · {{ $log->user->name ?? 'Sistema' }} · {{ $log->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
