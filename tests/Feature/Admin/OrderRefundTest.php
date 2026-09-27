<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderRefundTest extends TestCase
{
    use RefreshDatabase;

    private function paidOrder(): Order
    {
        $customer = Customer::create(['name' => 'Cliente Test', 'phone' => '123456']);

        return Order::create([
            'customer_id' => $customer->id,
            'status' => Order::STATUS_ENVIADO,
            'payment_status' => Order::PAYMENT_STATUS_PAGADO,
            'delivery_method' => Order::DELIVERY_RETIRO,
            'payment_method' => Order::PAYMENT_EFECTIVO,
            'origin' => Order::ORIGIN_WEB,
            'total' => 1000,
        ]);
    }

    public function test_cannot_request_refund_on_unpaid_order(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->paidOrder();
        $order->update(['payment_status' => Order::PAYMENT_STATUS_PENDIENTE]);

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => $order->status,
            'payment_status' => Order::PAYMENT_STATUS_PENDIENTE,
            'refund_status' => Order::REFUND_STATUS_PENDIENTE,
        ]);

        $this->assertNull($order->fresh()->refund_status);
    }

    public function test_can_mark_paid_order_for_refund_and_appears_in_movimientos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->paidOrder();

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => $order->status,
            'payment_status' => Order::PAYMENT_STATUS_PAGADO,
            'refund_status' => Order::REFUND_STATUS_PENDIENTE,
        ])->assertRedirect();

        $this->assertSame(Order::REFUND_STATUS_PENDIENTE, $order->fresh()->refund_status);

        $response = $this->actingAs($admin)->get(route('admin.activity.index', ['tab' => 'reembolso']));
        $response->assertOk();
        $response->assertSee('Cliente Test');
    }

    public function test_marking_refund_as_resolved_removes_it_from_the_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->paidOrder();
        $order->update(['refund_status' => Order::REFUND_STATUS_PENDIENTE]);

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => $order->status,
            'payment_status' => Order::PAYMENT_STATUS_PAGADO,
            'refund_status' => Order::REFUND_STATUS_REEMBOLSADO,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.activity.index', ['tab' => 'reembolso']));
        $response->assertOk();
        $response->assertDontSee('Cliente Test');
    }
}
