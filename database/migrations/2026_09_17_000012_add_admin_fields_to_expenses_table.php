<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('type')->default('gasto')->after('description'); // gasto | ingreso
            $table->string('frequency')->nullable()->after('amount'); // unica | mensual | anual
            $table->foreignId('expense_category_id')->nullable()->after('category')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expense_category_id');
            $table->dropColumn(['type', 'frequency']);
        });
    }
};
