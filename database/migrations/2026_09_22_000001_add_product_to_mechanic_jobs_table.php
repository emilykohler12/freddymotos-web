<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El mecánico compra repuestos reales del catálogo; se reemplaza el texto libre
        // "repuestos" por un producto concreto (+ cantidad), que es lo que va a pagar.
        Schema::table('mechanic_jobs', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('moto')->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1)->after('product_id');
            $table->dropColumn('repuestos');
        });
    }

    public function down(): void
    {
        Schema::table('mechanic_jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('quantity');
            $table->text('repuestos')->nullable()->after('problema');
        });
    }
};
