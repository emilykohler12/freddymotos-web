@extends('layouts.app')

@section('title', $settings->nombre_local . ' — Inicio')

@section('content')
    {{-- El home siempre muestra el nombre de marca en texto (no el logo subido). --}}
    <x-hero :nombre="$settings->nombre_local" />

    {{-- 1. Productos más vendidos (ranking real por ventas pagas) --}}
    <x-productos-destacados />

    {{-- 2. Promociones (productos con precio de oferta cargado) --}}
    <x-promociones />

    {{-- 3. Categorías destacadas + "ver todas" --}}
    <x-categorias-destacadas />

    {{-- 4. Tira de 4 beneficios --}}
    <x-banner-confianza />

    {{-- 5. Sobre nosotros --}}
    <x-sobre-nosotros />

    {{-- 6. Footer --}}
    <x-footer-tienda />

    {{-- 7. Crédito de desarrollo --}}
    <x-credito-desarrollo />
@endsection
