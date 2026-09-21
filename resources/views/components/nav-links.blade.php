{{-- Links de navegación compartidos entre el navbar del Hero (Home) y el navbar --}}
{{-- de las páginas interiores (x-site-nav), para que nunca se desincronicen. --}}
{{-- "Ingresar" / cuenta queda siempre al lado de "Nosotros". --}}

@php
    $link = 'rounded-lg px-3 py-2 text-sm font-medium text-marca-blanco/90 transition hover:bg-marca-blanco/10 hover:text-marca-blanco lg:px-0 lg:hover:bg-transparent';
    $linkActivo = 'rounded-lg px-3 py-2 text-sm font-semibold text-marca-blanco lg:px-0';
@endphp

<a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? $linkActivo : $link }}">Inicio</a>
<a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? $linkActivo : $link }}">Productos</a>
<a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? $linkActivo : $link }}">Categorías</a>
<a href="{{ route('workshop.index') }}" class="{{ request()->routeIs('workshop.*') ? $linkActivo : $link }}">Taller</a>
<a href="{{ url('/#sobre-nosotros') }}" class="{{ $link }}">Nosotros</a>
@auth
    @if (auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="{{ $link }}">Panel</a>
    @else
        <form method="POST" action="{{ route('logout') }}" class="contents">
            @csrf
            <button type="submit" class="{{ $link }} text-left">Salir ({{ \Illuminate\Support\Str::of(auth()->user()->name)->words(1, '') }})</button>
        </form>
    @endif
@else
    <a href="{{ route('login') }}" class="{{ $link }}">Ingresar</a>
@endauth
