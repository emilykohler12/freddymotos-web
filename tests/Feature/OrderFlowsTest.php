<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowsTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5): Product
    {
        return Product::create(['name' => 'Bujía', 'slug' => 'bujia', 'category' => 'Motor', 'brand' => 'Test', 'price' => 1000, 'stock' => $stock, 'active' => true]);
    }

    public function test_whatsapp_checkout_creates_the_order_and_discounts_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->product();

        $response = $this->actingAs($user)
            ->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'name' => 'Cliente',
                'phone' => '1155550000',
                'delivery_method' => Order::DELIVERY_RETIRO,
                'payment_method' => Order::PAYMENT_WHATSAPP,
            ]);

        $response->assertRedirectContains('https://wa.me/');
        $this->assertDatabaseHas('orders', ['origin' => Order::ORIGIN_WHATSAPP, 'subtotal' => 2000, 'total' => 2000]);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_admin_can_load_a_local_order_and_it_discounts_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $product = $this->product();

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'delivery_method' => 'retiro',
            'product_id' => [$product->id],
            'quantity' => [$product->id => 2],
            'price' => [$product->id => ''],
            'real_payment_method' => 'efectivo',
        ]);

        $order = Order::first();
        $response->assertRedirect(route('admin.orders.show', [$order, 'from' => 'local']));
        $this->assertSame(Order::ORIGIN_LOCAL, $order->origin);
        $this->assertSame(Order::PAYMENT_STATUS_PAGADO, $order->payment_status);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_mark_paid_sets_payment_status_and_mercadopago_id(): void
    {
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $order = $customer->orders()->create([
            'status' => Order::STATUS_PENDIENTE,
            'delivery_method' => 'retiro',
            'payment_method' => Order::PAYMENT_MERCADOPAGO,
            'origin' => Order::ORIGIN_WEB,
            'subtotal' => 1000,
            'shipping_cost' => 0,
            'total' => 1000,
        ]);

        $order->markPaid('123456');

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertSame('123456', $order->mp_payment_id);
        $this->assertNotNull($order->paid_at);
    }
}
