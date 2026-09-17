@extends('layouts.app')

@section('title', 'FREDDY MOTOS — Inicio')

@section('content')
    {{-- Logo y nombre salen de SiteSetting ($settings, compartido globalmente). --}}
    <x-hero :logo="$settings->logo_url" :nombre="$settings->nombre_local" />

    {{-- 1. Categorías destacadas --}}
    <x-categorias-destacadas />

    {{-- 2. Productos destacados --}}
    <x-productos-destacados />

    {{-- 3. Banner de confianza --}}
    <x-banner-confianza />

    {{-- 4. Sobre nosotros --}}
    <x-sobre-nosotros />

    {{-- 5. Footer --}}
    <x-footer-tienda />
@endsection
