<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /** Crea una fila en `categories` por cada valor distinto de `products.category` y linkea `category_id`. */
    public function up(): void
    {
        $names = DB::table('products')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        foreach ($names as $order => $name) {
            $id = DB::table('categories')->insertGetId([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $order,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('products')->where('category', $name)->update(['category_id' => $id]);
        }
    }

    public function down(): void
    {
        // No-op: es una migración de datos, no de esquema.
    }
};
