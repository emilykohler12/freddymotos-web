<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El descuento por producto se maneja ahora desde Promociones.
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });

        // Modelo de moto compatible: puede ser una lista larga (varios modelos).
        Schema::table('products', function (Blueprint $table) {
            $table->text('compatible_model')->nullable()->change();
        });

        // Imagen de categoría, para mostrarla en la sección de categorías del Home.
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->string('compatible_model')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
