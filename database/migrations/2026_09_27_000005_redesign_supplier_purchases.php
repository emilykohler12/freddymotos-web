<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_purchases', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('supplier_id')->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->nullable()->after('product_id');
            $table->string('status')->default('pendiente')->after('paid_amount');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn(['quantity', 'status']);
        });
    }
};
