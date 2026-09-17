<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($settings->nombre_local ?? 'FREDDY MOTOS') . ' — Repuestos de motos')</title>

    {{-- Tailwind + assets via Vite. Ejecutar: npm run dev (o npm run build) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-marca-blanco font-sans text-marca-negro antialiased">

    {{-- Mensajes flash (alta/actualización de carrito, errores de checkout, etc.) --}}
    @if (session('status') || session('error'))
        <div class="fixed inset-x-0 top-20 z-[90] flex justify-center px-4">
            <div class="max-w-lg rounded-xl px-4 py-3 text-sm font-semibold shadow-lg
                        {{ session('error') ? 'bg-marca-rojo text-marca-blanco' : 'bg-marca-negro text-marca-blanco' }}">
                {{ session('error') ?? session('status') }}
            </div>
        </div>
    @endif

    {{-- Contenedor de toasts (lo llena resources/js/cart.js) --}}
    <div id="toast-root" class="pointer-events-none fixed inset-x-0 top-4 z-[100] flex flex-col items-center gap-2 px-4"></div>

    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
