{{-- Nombre del negocio con el mismo tratamiento tipografico en todo el sitio: --}}
{{-- primera palabra en blanco, el resto en negro (fondo mostaza/amarillo del navbar). --}}
{{-- Uso: <x-brand-name /> (toma $settings->nombre_local) o <x-brand-name :nombre="$otro" />. --}}

@props(['nombre' => null])

@php
    $settings = $settings ?? \App\Models\SiteSetting::current();
    $texto = trim($nombre ?? $settings->nombre_local ?? '');
    $palabras = $texto !== '' ? explode(' ', $texto, 2) : [];
@endphp

@if ($texto !== '')
    <span {{ $attributes->merge(['class' => 'text-xl font-extrabold tracking-tight sm:text-2xl']) }}>
        <span class="text-marca-blanco">{{ $palabras[0] }}</span><span class="text-marca-negro">{{ isset($palabras[1]) ? ' ' . $palabras[1] : '' }}</span>
    </span>
@endif
