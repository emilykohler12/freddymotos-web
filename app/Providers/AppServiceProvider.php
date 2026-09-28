<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\Cart;
use App\Support\Sorting;
use Illuminate\Support\Facades\DB;
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

        // SQLite (local y tests): el chain de 31 REPLACE() de Sorting::foldedName()
        // desborda el stack del parser en algunos builds de sqlite3 (ej. el de
        // Ubuntu que usan los runners de GitHub Actions), aunque ande bien en
        // Windows. Se resuelve con una función nativa registrada en PHP en vez
        // de una expresión SQL gigante. Postgres (producción) no lo necesita:
        // ahí el REPLACE() encadenado anda sin problema.
        //
        // El try/catch es necesario: durante el build de Docker (composer
        // dump-autoload → package:discover) todavía no hay variables de entorno
        // cargadas, así que la config cae al default de Laravel ("sqlite") sin
        // que exista ningún archivo de base de datos. Sin este try/catch, esa
        // conexión fallida tira abajo el build entero en Render.
        if (config('database.default') === 'sqlite') {
            try {
                DB::connection()->getPdo()->sqliteCreateFunction('folded_name', fn (?string $value) => Sorting::fold((string) $value), 1);
            } catch (\Throwable) {
                // Sin base de datos disponible todavía (ej. build-time): no hay nada que registrar.
            }
        }
    }
}
