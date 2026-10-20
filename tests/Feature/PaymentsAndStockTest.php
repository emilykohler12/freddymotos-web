<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsAndStockTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5): Product
    {
        return Product::create(['name' => 'Bujía', 'slug' => 'bujia', 'category' => 'Motor', 'brand' => 'Test', 'price' => 1000, 'stock' => $stock, 'active' => true]);
    }

    /** Pedido de MP con un ítem de 2 unidades (1800 con promoción en vez de 2000) y envío de 300. */
    private function mercadoPagoOrder(array $overrides = []): Order
    {
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $product = Product::first() ?? $this->product();

        $order = $customer->orders()->create($overrides + [
            'status' => Order::STATUS_PENDIENTE,
            'payment_status' => Order::PAYMENT_STATUS_PENDIENTE,
            'delivery_method' => 'envio',
            'payment_method' => Order::PAYMENT_MERCADOPAGO,
            'origin' => Order::ORIGIN_WEB,
            'subtotal' => 1800,
            'shipping_cost' => 300,
            'total' => 2100,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 1000,
            'quantity' => 2,
            'subtotal' => 1800,
        ]);

        return $order;
    }

    private function webhook(array $payment, ?string $id = '555')
    {
        $this->mock(MercadoPagoService::class, function ($mock) use ($payment, $id) {
            $mock->shouldReceive('getPayment')->with($id)->andReturn($payment);
        });

        return $this->postJson(route('webhook.mercadopago'), ['type' => 'payment', 'data' => ['id' => $id]]);
    }

    private function payment(Order $order, string $status, float $amount = 2100.0): array
    {
        return ['status' => $status, 'external_reference' => (string) $order->id, 'amount' => $amount, 'currency' => 'ARS'];
    }

    /* ---------------- Monto de la preferencia ---------------- */

    public function test_preference_items_add_up_to_the_order_total_with_promotion_and_shipping(): void
    {
        config(['services.mercadopago.access_token' => 'TEST-token']);
        $order = $this->mercadoPagoOrder();

        $items = app(MercadoPagoService::class)->buildItems($order);

        $this->assertSame(2100.0, round(array_sum(array_column($items, 'unit_price')), 2));
        $this->assertSame(['Bujía x2', 'Envío'], array_column($items, 'title'));
        $this->assertSame([1, 1], array_column($items, 'quantity'));
    }

    public function test_preference_fails_instead_of_charging_a_different_amount(): void
    {
        config(['services.mercadopago.access_token' => 'TEST-token']);
        $order = $this->mercadoPagoOrder(['total' => 9999]);

        $this->expectException(\RuntimeException::class);

        app(MercadoPagoService::class)->buildItems($order);
    }

    /* ---------------- Webhook ---------------- */

    public function test_approved_payment_with_the_right_amount_marks_the_order_paid(): void
    {
        $order = $this->mercadoPagoOrder();

        $this->webhook($this->payment($order, 'approved'))->assertOk();

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertSame('555', $order->mp_payment_id);
    }

    public function test_approved_payment_with_a_different_amount_is_not_marked_paid(): void
    {
        $order = $this->mercadoPagoOrder();

        $this->webhook($this->payment($order, 'approved', 2000.0))->assertOk();

        $this->assertFalse($order->fresh()->isPaid());
    }

    public function test_approved_payment_on_a_cancelled_order_is_flagged_for_refund(): void
    {
        $order = $this->mercadoPagoOrder(['status' => Order::STATUS_CANCELADO]);

        $this->webhook($this->payment($order, 'approved'))->assertOk();

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertSame(Order::REFUND_STATUS_PENDIENTE, $order->refund_status);
    }

    public function test_webhook_answers_503_when_mercado_pago_cannot_be_queried(): void
    {
        $this->mock(MercadoPagoService::class, fn ($mock) => $mock->shouldReceive('getPayment')->andReturn(null));

        $this->postJson(route('webhook.mercadopago'), ['type' => 'payment', 'data' => ['id' => '555']])
            ->assertStatus(503);
    }

    public function test_refunded_and_charged_back_payments_mark_the_refund(): void
    {
        foreach (['refunded', 'charged_back'] as $status) {
            $order = $this->mercadoPagoOrder(['payment_status' => Order::PAYMENT_STATUS_PAGADO, 'paid_at' => now()]);

            $this->webhook($this->payment($order, $status))->assertOk();

            $this->assertSame(Order::REFUND_STATUS_REEMBOLSADO, $order->fresh()->refund_status);
        }
    }

    public function test_rejected_payment_marks_the_order_rejected_but_never_a_paid_one(): void
    {
        $pending = $this->mercadoPagoOrder();
        $this->webhook($this->payment($pending, 'rejected'))->assertOk();
        $this->assertSame(Order::PAYMENT_STATUS_RECHAZADO, $pending->fresh()->payment_status);

        $paid = $this->mercadoPagoOrder(['payment_status' => Order::PAYMENT_STATUS_PAGADO, 'paid_at' => now()]);
        $this->webhook($this->payment($paid, 'rejected'))->assertOk();
        $this->assertSame(Order::PAYMENT_STATUS_PAGADO, $paid->fresh()->payment_status);
    }

    /* ---------------- Checkout ---------------- */

    private function checkout(User $user, Product $product, int $quantity, string $method)
    {
        return $this->actingAs($user)
            ->withSession(['cart' => [$product->id => $quantity]])
            ->post(route('checkout.store'), [
                'name' => 'Cliente',
                'phone' => '1155550000',
                'delivery_method' => Order::DELIVERY_RETIRO,
                'payment_method' => $method,
            ]);
    }

    public function test_failed_mercado_pago_checkout_cancels_the_order_and_returns_the_stock(): void
    {
        $product = $this->product();
        $this->mock(MercadoPagoService::class, fn ($mock) => $mock->shouldReceive('createPreference')->andThrow(new \RuntimeException('MP caído')));

        $this->checkout(User::factory()->create(), $product, 2, Order::PAYMENT_MERCADOPAGO)
            ->assertRedirect(route('checkout.show'))
            ->assertSessionHas('error')
            ->assertSessionHas('cart');

        $order = Order::first();
        $this->assertSame(Order::STATUS_CANCELADO, $order->status);
        $this->assertSame(Order::PAYMENT_STATUS_RECHAZADO, $order->payment_status);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_checkout_form_carries_a_unique_token(): void
    {
        $product = $this->product();

        $html = $this->actingAs(User::factory()->create())
            ->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout.show'))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression('/name="checkout_token" value="[0-9a-f-]{36}"/', $html);
    }

    public function test_submitting_the_same_checkout_form_twice_creates_only_one_order(): void
    {
        $product = $this->product();
        $user = User::factory()->create();
        $payload = [
            'name' => 'Cliente',
            'phone' => '1155550000',
            'delivery_method' => Order::DELIVERY_RETIRO,
            'payment_method' => Order::PAYMENT_WHATSAPP,
            'checkout_token' => '3f1c2d4e-5a6b-4c7d-8e9f-0a1b2c3d4e5f',
        ];

        // Doble clic: las dos peticiones llegan con el carrito todavía lleno.
        $this->actingAs($user)->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), $payload)
            ->assertRedirectContains('https://wa.me/');

        $this->actingAs($user)->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), $payload)
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('status');

        $this->assertSame(1, Order::count());
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_checkout_rejects_quantities_above_the_available_stock(): void
    {
        $product = $this->product(1);

        $this->checkout(User::factory()->create(), $product, 3, Order::PAYMENT_WHATSAPP)
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_admin_local_order_is_rejected_when_stock_is_not_enough(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['name' => 'Cliente', 'phone' => '1155550000']);
        $product = $this->product(1);

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'delivery_method' => 'retiro',
            'product_id' => [$product->id],
            'quantity' => [$product->id => 3],
            'price' => [$product->id => ''],
            'real_payment_method' => 'efectivo',
        ])->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_reopening_a_cancelled_order_without_stock_is_rejected(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = $this->product(0);
        $order = $this->mercadoPagoOrder(['status' => Order::STATUS_CANCELADO]);

        $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => Order::STATUS_PENDIENTE,
            'payment_status' => Order::PAYMENT_STATUS_PENDIENTE,
        ])->assertSessionHas('error');

        $this->assertSame(Order::STATUS_CANCELADO, $order->fresh()->status);
        $this->assertSame(0, $product->fresh()->stock);
    }

    /* ---------------- Sentry y límites ---------------- */

    public function test_sentry_never_captures_the_request_body(): void
    {
        $this->assertSame('none', config('sentry.max_request_body_size'));
    }

    public function test_registration_and_inquiries_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('register'), [])->assertSessionHasErrors();
        }
        $this->post(route('register'), [])->assertStatus(429);

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('workshop.inquiries.store'), [])->assertSessionHasErrors();
        }
        $this->post(route('workshop.inquiries.store'), [])->assertStatus(429);
    }
}
