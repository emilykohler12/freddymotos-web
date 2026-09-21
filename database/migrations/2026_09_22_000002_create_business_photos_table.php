<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fotos del local que carga el admin desde Configuración. La más reciente
        // es la que se muestra en la sección "Sobre nosotros" del Home.
        Schema::create('business_photos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_photos');
    }
};
