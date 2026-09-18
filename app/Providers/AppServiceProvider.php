<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\Cart;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport;

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

        // Brevo y Mailjet no tienen soporte nativo en Laravel: se registran como
        // transportes Symfony vía DSN, usando sus APIs (no SMTP).
        Mail::extend('brevo', function () {
            $key = config('services.brevo.key');

            return Transport::fromDsn('brevo+api://' . urlencode((string) $key) . '@default');
        });

        Mail::extend('mailjet', function () {
            $key = config('services.mailjet.key');
            $secret = config('services.mailjet.secret');

            return Transport::fromDsn('mailjet+api://' . urlencode((string) $key) . ':' . urlencode((string) $secret) . '@default');
        });
    }
}
