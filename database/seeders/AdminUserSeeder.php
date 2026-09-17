<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario admin de prueba para entrar al panel.
        User::updateOrCreate(
            ['email' => 'admin@freddymotos.com'],
            [
                'name' => 'Administrador',
                'password' => 'admin123', // el cast 'hashed' del modelo lo encripta
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
