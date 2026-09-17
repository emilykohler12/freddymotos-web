<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('slug');
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            $table->boolean('active')->default(true)->after('is_featured');
            $table->foreignId('category_id')->nullable()->after('category')->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->after('brand')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['sku', 'cost_price', 'active']);
        });
    }
};
