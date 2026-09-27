<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mechanic_jobs', function (Blueprint $table) {
            $table->string('moto')->nullable()->change();
            $table->text('problema')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mechanic_jobs', function (Blueprint $table) {
            $table->string('moto')->nullable(false)->change();
            $table->text('problema')->nullable(false)->change();
        });
    }
};
