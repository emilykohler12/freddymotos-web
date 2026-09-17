<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // percentage | fixed | nxm
            $table->decimal('value', 10, 2)->nullable();       // % o $ según el tipo
            $table->unsignedInteger('buy_quantity')->nullable(); // para nxm: paga X
            $table->unsignedInteger('pay_quantity')->nullable(); // para nxm: lleva Y
            $table->string('scope')->default('all');           // all | category | products
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('promotion_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['promotion_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_product');
        Schema::dropIfExists('promotions');
    }
};
