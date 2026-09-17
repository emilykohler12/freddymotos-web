<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\Cart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Datos disponibles en todas las vistas:
        //  - $settings   → configuración del sitio (SiteSetting, una sola fila)
        //  - $cartCount  → unidades en el carrito de la sesión
        View::composer('*', function ($view) {
            $view->with('settings', SiteSetting::current());
            $view->with('cartCount', app(Cart::class)->count());
        });
    }
}
