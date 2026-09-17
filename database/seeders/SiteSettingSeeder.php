<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Una sola fila (id = 1) con valores por defecto razonables.
        // El admin los editará desde el panel más adelante.
        SiteSetting::updateOrCreate(
            ['id' => 1],
            SiteSetting::defaults(),
        );

        SiteSetting::flush(); // por si el seeder corre con eventos deshabilitados
    }
}
