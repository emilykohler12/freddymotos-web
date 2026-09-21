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
        // AdminUserSeeder crea un login de prueba con contraseña fija y conocida
        // (está en el código, público en GitHub): solo se corre en local. En
        // producción, el admin real se crea a mano por tinker con su propia
        // contraseña (ver la guía de deploy).
        if ($this->command->getLaravel()->environment('local')) {
            $this->call([
                AdminUserSeeder::class,
            ]);
        }
    }
}
