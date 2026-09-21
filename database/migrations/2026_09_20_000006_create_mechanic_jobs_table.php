<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un trabajo de taller asignado a un mecánico: qué moto, qué se le hizo,
        // qué repuestos necesitó y cuánto le debe el local por ese trabajo.
        Schema::create('mechanic_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mechanic_id')->constrained()->cascadeOnDelete();
            $table->string('moto');
            $table->text('problema');
            $table->text('repuestos')->nullable();
            $table->decimal('monto_a_pagar', 10, 2)->default(0);
            $table->boolean('pagado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mechanic_jobs');
    }
};
