<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'Panel') · {{ $settings->nombre_local ?? 'Freddy Motos' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-marca-gris-claro font-sans text-marca-negro antialiased">

<x-offline-banner />

@php
    $nav = [
        ['label' => 'Dashboard',     'route' => 'admin.dashboard',     'active' => 'admin.dashboard',   'icon' => 'M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v5h6V4h-6z'],
        ['label' => 'Productos',      'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Categorías',     'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'M4 6h16M4 12h16M4 18h10'],
        ['label' => 'Promociones',    'route' => 'admin.promotions.index', 'active' => 'admin.promotions.*', 'icon' => 'M7 7h.01M3 11l8-8h6a2 2 0 012 2v6l-8 8a2 2 0 01-2.83 0l-5.17-5.17A2 2 0 013 11z'],
        ['label' => 'Pedidos',        'route' => 'admin.orders.index',  'active' => 'admin.orders.*',    'icon' => 'M9 5h6a2 2 0 012 2v12l-5-3-5 3V7a2 2 0 012-2z'],
        ['label' => 'Clientes',       'route' => 'admin.customers.index', 'active' => 'admin.customers.*', 'icon' => 'M17 20h5v-1a4 4 0 00-4-4h-1m-4 5H2v-1a4 4 0 014-4h6a4 4 0 014 4v1zm-1-13a3 3 0 11-6 0 3 3 0 016 0zm7 2a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'],
        ['label' => 'Proveedores',    'route' => 'admin.suppliers.index', 'active' => 'admin.suppliers.*', 'icon' => 'M20 7l-8-4-8 4v10l8 4 8-4V7zM4 7l8 4m0 0l8-4m-8 4v10'],
        ['label' => 'Envíos',         'route' => 'admin.shipping.index', 'active' => 'admin.shipping.*', 'icon' => 'M3 7h11v8H3V7zm11 3h4l3 3v2h-7m-7 2a2 2 0 104 0 2 2 0 00-4 0zm10 0a2 2 0 104 0 2 2 0 00-4 0z'],
        ['label' => 'Gastos',         'route' => 'admin.expenses.index', 'active' => 'admin.expenses.*', 'icon' => 'M3 10h18M7 15h4m-4 0v.01M3 6h18v12H3z'],
        ['label' => 'Movimientos',    'route' => 'admin.activity.index', 'active' => 'admin.activity.*', 'icon' => 'M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Taller',         'route' => 'admin.workshop.index', 'active' => 'admin.workshop.*', 'icon' => 'M21.75 6.75a4.5 4.5 0 01-4.884 4.484c-1.076-.091-2.264.071-2.95.904l-7.152 8.684a2.548 2.548 0 11-3.586-3.586l8.684-7.152c.833-.686.995-1.874.904-2.95a4.5 4.5 0 016.336-4.486l-3.276 3.276a3.004 3.004 0 002.25 2.25l3.276-3.276c.256.565.398 1.192.398 1.852z'],
        ['label' => 'Configuración',  'route' => 'admin.settings.edit', 'active' => 'admin.settings.*', 'icon' => 'M10.3 4.3a1 1 0 011.4 0l1 1a1 1 0 001 .3l1.4-.2a1 1 0 011 .6l.6 1.3a1 1 0 00.7.6l1.4.4a1 1 0 01.7 1.2l-.3 1.4a1 1 0 00.2 1l1 1a1 1 0 010 1.4l-1 1a1 1 0 00-.3 1l.2 1.4a1 1 0 01-.6 1l-1.3.6a1 1 0 00-.6.7l-.4 1.4a1 1 0 01-1.2.7l-1.4-.3a1 1 0 00-1 .2l-1 1a1 1 0 01-1.4 0l-1-1a1 1 0 00-1-.3l-1.4.2a1 1 0 01-1-.6l-.6-1.3a1 1 0 00-.7-.6l-1.4-.4a1 1 0 01-.7-1.2l.3-1.4a1 1 0 00-.2-1l-1-1a1 1 0 010-1.4l1-1a1 1 0 00.3-1L4.3 8a1 1 0 01.6-1l1.3-.6a1 1 0 00.6-.7l.4-1.4zM12 15a3 3 0 100-6 3 3 0 000 6z'],
    ];
@endphp

<input type="checkbox" id="admin-sidebar-toggle" class="peer hidden">

{{-- Overlay: a cualquier tamaño de pantalla, no solo mobile --}}
<label for="admin-sidebar-toggle" class="fixed inset-0 z-30 hidden bg-marca-negro/50 peer-checked:block"></label>

{{-- Sidebar: menú hamburguesa siempre (se abre/cierra igual en cualquier tamaño de pantalla) --}}
<aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-marca-negro text-marca-blanco transition-transform duration-200 peer-checked:translate-x-0">
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-marca-blanco/10 px-5">
        <span class="text-lg font-extrabold tracking-tight">
            {{ \Illuminate\Support\Str::of($settings->nombre_local ?? 'Freddy Motos')->upper() }}
        </span>
        <span class="rounded bg-marca-mostaza px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-marca-negro">Admin</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($nav as $item)
            @php $isActive = $item['active'] && request()->routeIs($item['active']); @endphp
            @if ($item['route'])
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                          {{ $isActive ? 'bg-marca-amarillo text-marca-negro' : 'text-marca-blanco/70 hover:bg-marca-blanco/10 hover:text-marca-blanco' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @else
                <span class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-marca-blanco/35">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                    <span class="ml-auto rounded bg-marca-blanco/10 px-1.5 py-0.5 text-[10px] font-semibold uppercase">Pronto</span>
                </span>
            @endif
        @endforeach
    </nav>

    <div class="border-t border-marca-blanco/10 px-5 py-3 text-xs text-marca-blanco/40">
        <a href="{{ route('home') }}" class="transition hover:text-marca-blanco">← Ver el sitio</a>
    </div>
</aside>

{{-- Contenido: ancho completo, el sidebar ya no reserva espacio fijo --}}
<div>
    <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-marca-gris-oscuro/10 bg-marca-blanco px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <label for="admin-sidebar-toggle" class="cursor-pointer rounded-lg p-2 text-marca-gris-oscuro hover:bg-marca-gris-claro" aria-label="Abrir menú">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </label>
            <h1 class="text-base font-bold text-marca-negro sm:text-lg">@yield('page-heading', 'Panel')</h1>
        </div>

        <div class="flex items-center gap-3">
            <span class="hidden text-sm font-medium text-marca-gris-oscuro sm:inline">{{ auth()->user()->name }}</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-marca-mostaza text-xs font-bold text-marca-negro">
                {{ \Illuminate\Support\Str::of(auth()->user()->name)->substr(0, 1)->upper() }}
            </span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-marca-gris-oscuro/20 px-3 py-1.5 text-xs font-semibold text-marca-gris-oscuro transition hover:border-marca-rojo hover:text-marca-rojo">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    <main class="p-4 sm:p-6 lg:p-8">
        @yield('content')
    </main>
</div>

{{-- Aviso flotante abajo a la derecha, se saca solo a los 20 segundos --}}
@if (session('status') || session('error'))
    @php $isError = (bool) session('error'); @endphp
    <div id="admin-toast" role="status"
         class="fixed bottom-4 right-4 z-[100] max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition
                {{ $isError ? 'bg-marca-rojo text-marca-blanco' : 'bg-marca-negro text-marca-blanco' }}">
        {{ session('error') ?? session('status') }}
    </div>
    <script>
        setTimeout(function () {
            var toast = document.getElementById('admin-toast');
            if (!toast) return;
            toast.style.opacity = '0';
            setTimeout(function () { toast.remove(); }, 300);
        }, 20000);
    </script>
@endif

</body>
</html>
