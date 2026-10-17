<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // SiteSettingSeeder, ProductSeeder y ExpenseSeeder quedan afuera a propósito:
        // esos datos (contacto, redes, categorías, productos, gastos) los carga el
        // admin desde el panel, no vienen de ejemplo. Se pueden correr a mano si
        // hace falta una tienda de prueba: `php artisan db:seed --class=ProductSeeder`.
        //
        // El admin se crea a mano por tinker con su propia contraseña (no va en el
        // código: el repo es público).
    }
}
