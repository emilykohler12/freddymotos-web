<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('moneda')->default('ARS')->after('facebook_url');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('moneda'); // % de impuestos, ej. IVA
            $table->json('payment_methods')->nullable()->after('tax_rate'); // ['mercadopago','efectivo','transferencia']
            $table->string('banco')->nullable()->after('payment_methods');
            $table->string('cbu_alias')->nullable()->after('banco');
            $table->string('titular_cuenta')->nullable()->after('cbu_alias');
            $table->string('mp_public_key')->nullable()->after('titular_cuenta');
            $table->string('mp_access_token')->nullable()->after('mp_public_key');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'moneda', 'tax_rate', 'payment_methods', 'banco',
                'cbu_alias', 'titular_cuenta', 'mp_public_key', 'mp_access_token',
            ]);
        });
    }
};
