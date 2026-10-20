<?php

namespace Tests\Feature;

use App\Services\MercadoPagoService;
use Tests\TestCase;

class WebhookSignatureAndHeadersTest extends TestCase
{
    private function signed(string $secret, string $id = '555', string $requestId = 'req-1', string $ts = '1700000000'): array
    {
        $v1 = hash_hmac('sha256', "id:{$id};request-id:{$requestId};ts:{$ts};", $secret);

        return ['x-signature' => "ts={$ts},v1={$v1}", 'x-request-id' => $requestId];
    }

    private function sendWebhook(array $headers)
    {
        $this->mock(MercadoPagoService::class, fn ($m) => $m->shouldReceive('getPayment')->andReturn(null));

        return $this->postJson(route('webhook.mercadopago'), ['type' => 'payment', 'data' => ['id' => '555']], $headers);
    }

    public function test_valid_signature_reaches_the_controller(): void
    {
        config(['services.mercadopago.webhook_secret' => 'clave']);

        // 503 = pasó la firma y el controlador no pudo consultar a MP (mock).
        $this->sendWebhook($this->signed('clave'))->assertStatus(503);
    }

    public function test_invalid_or_missing_signature_is_rejected_when_secret_is_set(): void
    {
        config(['services.mercadopago.webhook_secret' => 'clave']);

        $this->sendWebhook($this->signed('otra-clave'))->assertStatus(401);
        $this->sendWebhook([])->assertStatus(401);
    }

    public function test_without_a_secret_the_webhook_is_not_blocked(): void
    {
        config(['services.mercadopago.webhook_secret' => null]);

        $this->sendWebhook([])->assertStatus(503);
    }

    public function test_responses_carry_security_headers(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }
}
