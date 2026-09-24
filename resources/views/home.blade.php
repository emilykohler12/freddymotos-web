@extends('layouts.app')

@section('title', $settings->nombre_local . ' — Inicio')

@section('content')
    {{-- El home siempre muestra el nombre de marca en texto (no el logo subido). --}}
    <x-hero :nombre="$settings->nombre_local" />

    {{-- 1. Repuestos más vendidos (ranking real por ventas pagas) --}}
    <x-productos-destacados />

    {{-- 2. Promociones (repuestos con precio de oferta cargado) --}}
    <x-promociones />

    {{-- 3. Categorías destacadas + "ver todas" --}}
    <x-categorias-destacadas />

    {{-- 3b. Marcas más vendidas (ranking real por ventas pagas) --}}
    <x-marcas-destacadas />

    {{-- 4. Tira de 4 beneficios --}}
    <x-banner-confianza />

    {{-- 4b. Consultas: formulario general de contacto --}}
    <x-consultas />

    {{-- 5. Sobre nosotros --}}
    <x-sobre-nosotros />

    {{-- 6. Footer --}}
    <x-footer-tienda />

    {{-- 7. Crédito de desarrollo --}}
    <x-credito-desarrollo />
@endsection
