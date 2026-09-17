<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // `status` pasa a ser el estado del PEDIDO (pendiente/procesando/enviado/entregado/cancelado).
            $table->string('payment_status')->default('pendiente')->after('status'); // pendiente | pagado | rechazado
            $table->decimal('discount', 12, 2)->default(0)->after('subtotal');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('discount');
            $table->foreignId('shipping_zone_id')->nullable()->after('shipping_cost')->constrained()->nullOnDelete();
        });

        // Los pedidos ya pagados (por el webhook de MP) quedan reflejados en el nuevo campo.
        DB::table('orders')->where('status', 'pagado')->update(['payment_status' => 'pagado']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_zone_id');
            $table->dropColumn(['payment_status', 'discount', 'shipping_cost']);
        });
    }
};
