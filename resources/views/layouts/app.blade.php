<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="google-site-verification" content="AsK9xUZEvCjQzmsPWBRRg8mW--E0HUPFLT5qQG7f3Uw" />
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: (($settings->nombre_local ?? 'Freddy Motos') . ' — Repuestos de motos');
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: 'Repuestos y accesorios para motos en ' . ($settings->nombre_local ?? 'Freddy Motos') . '. Comprá online, pagá con Mercado Pago o coordiná por WhatsApp.';
        $seoImage = trim($__env->yieldContent('og_image')) ?: asset('images/hero-moto.jpg');
        $seoCanonical = url()->current();
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    @hasSection('noindex')
        <meta name="robots" content="noindex, nofollow">
    @else
        <link rel="canonical" href="{{ $seoCanonical }}">
    @endif
    <meta property="og:type" content="{{ trim($__env->yieldContent('og_type')) ?: 'website' }}">
    <meta property="og:site_name" content="{{ $settings->nombre_local ?? 'Freddy Motos' }}">
    <meta property="og:locale" content="es_AR">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ url($seoImage) }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Tailwind + assets via Vite. Ejecutar: npm run dev (o npm run build) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-marca-blanco font-sans text-marca-negro antialiased">

    <x-offline-banner />

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
    <div id="toast-root" class="pointer-events-none fixed bottom-4 right-4 z-[100] flex flex-col items-end gap-2 px-4 sm:px-0"></div>

    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
