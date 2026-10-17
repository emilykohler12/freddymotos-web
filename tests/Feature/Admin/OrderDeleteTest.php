<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_paid_order_removes_it_from_income_and_restores_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $product = Product::create(['name' => 'Bujía', 'slug' => 'bujia', 'category' => 'Motor', 'brand' => 'Test','price' => 1000, 'stock' => 3, 'active' => true]);

        $order = $customer->orders()->create([
            'status' => Order::STATUS_PENDIENTE,
            'payment_status' => Order::PAYMENT_STATUS_PAGADO,
            'delivery_method' => 'retiro',
            'payment_method' => 'efectivo',
            'origin' => Order::ORIGIN_LOCAL,
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'total' => 1000,
            'paid_at' => now(),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 1000,
            'quantity' => 2,
            'subtotal' => 2000,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.orders.destroy', $order), ['from' => 'ingresos']);

        $response->assertRedirect(route('admin.sales.index', ['tab' => 'ingresos']));
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertSame(5, $product->fresh()->stock);
    }
}
