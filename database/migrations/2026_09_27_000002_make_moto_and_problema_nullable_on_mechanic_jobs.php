<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SQL directo en vez de Schema::change(): ese método necesita doctrine/dbal
     * para modificar columnas en Postgres, y el paquete no está instalado. Esto
     * evita agregar una dependencia solo para dos ALTER TABLE.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite no soporta ALTER COLUMN: hay que recrear la tabla. Como Laravel 11
            // ya sabe hacer esto de forma nativa para SQLite (sin doctrine/dbal), se
            // puede seguir usando Schema::table()->change() acá sin problema.
            Schema::table('mechanic_jobs', function ($table) {
                $table->string('moto')->nullable()->change();
                $table->text('problema')->nullable()->change();
            });

            return;
        }

        DB::statement('ALTER TABLE mechanic_jobs ALTER COLUMN moto DROP NOT NULL');
        DB::statement('ALTER TABLE mechanic_jobs ALTER COLUMN problema DROP NOT NULL');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('mechanic_jobs', function ($table) {
                $table->string('moto')->nullable(false)->change();
                $table->text('problema')->nullable(false)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE mechanic_jobs ALTER COLUMN moto SET NOT NULL');
        DB::statement('ALTER TABLE mechanic_jobs ALTER COLUMN problema SET NOT NULL');
    }
};
