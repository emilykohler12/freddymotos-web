<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancelTest extends TestCase
{
    use RefreshDatabase;

    private function orderWithTwoUnits(string $status = Order::STATUS_PENDIENTE): array
    {
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $product = Product::create(['name' => 'Bujía', 'slug' => 'bujia', 'category' => 'Motor', 'brand' => 'Test', 'price' => 1000, 'stock' => 3, 'active' => true]);

        $order = $customer->orders()->create([
            'status' => $status,
            'payment_status' => Order::PAYMENT_STATUS_PENDIENTE,
            'delivery_method' => 'retiro',
            'payment_method' => 'whatsapp',
            'origin' => Order::ORIGIN_WHATSAPP,
            'subtotal' => 2000,
            'shipping_cost' => 0,
            'total' => 2000,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 1000,
            'quantity' => 2,
            'subtotal' => 2000,
        ]);

        return [$order, $product];
    }

    private function setStatus(User $admin, Order $order, string $status)
    {
        return $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => $status,
            'payment_status' => Order::PAYMENT_STATUS_PENDIENTE,
        ]);
    }

    public function test_cancelling_an_order_returns_its_stock_only_once(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$order, $product] = $this->orderWithTwoUnits();

        $this->setStatus($admin, $order, Order::STATUS_CANCELADO)->assertRedirect();
        $this->assertSame(5, $product->fresh()->stock);

        // Guardar de nuevo el mismo estado no devuelve el stock otra vez.
        $this->setStatus($admin, $order, Order::STATUS_CANCELADO)->assertRedirect();
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_deleting_a_cancelled_order_does_not_return_stock_twice(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$order, $product] = $this->orderWithTwoUnits();

        $this->setStatus($admin, $order, Order::STATUS_CANCELADO);
        $this->actingAs($admin)->delete(route('admin.orders.destroy', $order))->assertRedirect();

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_reopening_a_cancelled_order_discounts_stock_again(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$order, $product] = $this->orderWithTwoUnits();

        $this->setStatus($admin, $order, Order::STATUS_CANCELADO);
        $this->setStatus($admin, $order, Order::STATUS_PENDIENTE);

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_other_status_changes_do_not_touch_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        [$order, $product] = $this->orderWithTwoUnits();

        $this->setStatus($admin, $order, Order::STATUS_ENVIADO);
        $this->setStatus($admin, $order, Order::STATUS_ENTREGADO);

        $this->assertSame(3, $product->fresh()->stock);
    }
}
