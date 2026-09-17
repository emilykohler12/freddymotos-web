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

        // SiteSettingSeeder y ProductSeeder quedan afuera a propósito: esos datos
        // (contacto, redes, horarios, categorías, productos) los carga el admin
        // desde el panel, no vienen de ejemplo. Se pueden correr a mano si hace
        // falta una tienda de prueba: `php artisan db:seed --class=ProductSeeder`.
        $this->call([
            AdminUserSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
