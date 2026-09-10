<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FREDDY MOTOS — Repuestos de motos')</title>

    {{-- Tailwind + assets via Vite. Ejecutar: npm run dev (o npm run build) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-marca-blanco font-sans text-marca-negro antialiased">
    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
