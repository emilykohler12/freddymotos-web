<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foto real que reemplaza la ilustración de placeholder en el Hero del Home.
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_photo_path')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('hero_photo_path');
        });
    }
};
