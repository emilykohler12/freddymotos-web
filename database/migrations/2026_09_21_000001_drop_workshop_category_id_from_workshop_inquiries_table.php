<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Las consultas del Home pasan a ser generales, no atadas a una categoría de taller.
        Schema::table('workshop_inquiries', function (Blueprint $table) {
            $table->dropForeign(['workshop_category_id']);
            $table->dropColumn('workshop_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('workshop_inquiries', function (Blueprint $table) {
            $table->foreignId('workshop_category_id')->nullable()->after('email')->constrained()->nullOnDelete();
        });
    }
};
