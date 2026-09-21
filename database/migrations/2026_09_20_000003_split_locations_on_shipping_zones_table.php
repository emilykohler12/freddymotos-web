<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->string('provincia')->nullable()->after('name');
            $table->string('localidad')->nullable()->after('provincia');
        });

        // El texto libre que había en "locations" (ej. una localidad) se conserva
        // en el nuevo campo "localidad"; el admin completa la provincia a mano.
        foreach (DB::table('shipping_zones')->get(['id', 'locations']) as $zone) {
            if ($zone->locations) {
                DB::table('shipping_zones')->where('id', $zone->id)->update(['localidad' => $zone->locations]);
            }
        }

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->dropColumn('locations');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->text('locations')->nullable();
        });

        foreach (DB::table('shipping_zones')->get(['id', 'localidad']) as $zone) {
            if ($zone->localidad) {
                DB::table('shipping_zones')->where('id', $zone->id)->update(['locations' => $zone->localidad]);
            }
        }

        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->dropColumn(['provincia', 'localidad']);
        });
    }
};
