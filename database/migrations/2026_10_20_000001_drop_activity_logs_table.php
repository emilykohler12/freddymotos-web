<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El modelo ActivityLog se eliminó (se escribía pero nada lo leía) y el código que
     * escribía en esta tabla ya está desplegado, así que se puede borrar.
     * Los registros viejos se pierden: no hay forma de recuperarlos salvo desde un backup.
     */
    public function up(): void
    {
        Schema::dropIfExists('activity_logs');
    }

    /** Recrea la tabla vacía (la estructura final, con read_at); los datos no vuelven. */
    public function down(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('description');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
};
