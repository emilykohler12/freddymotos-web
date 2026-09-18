<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['banco', 'cbu_alias', 'titular_cuenta', 'mp_public_key', 'mp_access_token']);
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('banco')->nullable()->after('payment_methods');
            $table->string('cbu_alias')->nullable()->after('banco');
            $table->string('titular_cuenta')->nullable()->after('cbu_alias');
            $table->string('mp_public_key')->nullable()->after('titular_cuenta');
            $table->string('mp_access_token')->nullable()->after('mp_public_key');
        });
    }
};
