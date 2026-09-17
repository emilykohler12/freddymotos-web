<?php

namespace App\Services;

use App\Models\Order;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use RuntimeException;

/**
 * Integración con el SDK oficial de Mercado Pago para PHP (mercadopago/dx-php).
 *
 * Instalar:  composer require mercadopago/dx-php
 * Configurar en .env:  MERCADOPAGO_ACCESS_TOKEN=TEST-xxxxxxxx  (credencial de prueba)
 */
class MercadoPagoService
{
    public function __construct()
    {
        $token = (string) config('services.mercadopago.access_token');

        if ($token === '') {
            throw new RuntimeException('Falta MERCADOPAGO_ACCESS_TOKEN en el .env.');
        }

        MercadoPagoConfig::setAccessToken($token);
    }

    /**
     * Crea la preferencia de pago para un pedido y devuelve su id + init_point.
     *
     * @return array{id:string,init_point:string}
     */
    public function createPreference(Order $order): array
    {
        $order->loadMissing('items');

        $items = $order->items
            ->map(fn ($item) => [
                'id' => (string) ($item->product_id ?? $item->id),
                'title' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'currency_id' => (string) config('services.mercadopago.currency', 'ARS'),
            ])
            ->values()
            ->all();

        $backUrl = route('checkout.success', $order);

        $preference = (new PreferenceClient())->create([
            'items' => $items,
            'external_reference' => (string) $order->id,
            'notification_url' => route('webhook.mercadopago'),
            'back_urls' => [
                'success' => $backUrl . '?status=success',
                'failure' => $backUrl . '?status=failure',
                'pending' => $backUrl . '?status=pending',
            ],
            // 'auto_return' => 'approved', // activar en producción (requiere URL pública)
        ]);

        return [
            'id' => (string) $preference->id,
            'init_point' => (string) $preference->init_point,
        ];
    }

    /**
     * Consulta un pago por id.
     *
     * @return array{status:string,external_reference:?string}|null
     */
    public function getPayment(string $paymentId): ?array
    {
        try {
            $payment = (new PaymentClient())->get((int) $paymentId);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        return [
            'status' => (string) $payment->status,
            'external_reference' => $payment->external_reference,
        ];
    }
}
