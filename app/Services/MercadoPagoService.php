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
        $items = $this->buildItems($order);

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
     * Ítems de la preferencia, con el mismo monto que el pedido: cada línea va por su subtotal
     * (ya con la promoción aplicada) y el envío como un ítem aparte. Si no suman el total
     * del pedido, falla antes de cobrar un monto equivocado.
     *
     * @return list<array{id:string,title:string,quantity:int,unit_price:float,currency_id:string}>
     */
    public function buildItems(Order $order): array
    {
        $order->loadMissing('items');
        $currency = (string) config('services.mercadopago.currency', 'ARS');

        $items = $order->items
            ->map(fn ($item) => [
                'id' => (string) ($item->product_id ?? $item->id),
                'title' => $item->quantity > 1 ? "{$item->product_name} x{$item->quantity}" : $item->product_name,
                'quantity' => 1,
                'unit_price' => round((float) $item->subtotal, 2),
                'currency_id' => $currency,
            ])
            ->values()
            ->all();

        if ((float) $order->shipping_cost > 0) {
            $items[] = [
                'id' => 'envio',
                'title' => 'Envío',
                'quantity' => 1,
                'unit_price' => round((float) $order->shipping_cost, 2),
                'currency_id' => $currency,
            ];
        }

        $sum = round(array_sum(array_column($items, 'unit_price')), 2);

        if (abs($sum - round((float) $order->total, 2)) > 0.009) {
            throw new RuntimeException("Los ítems del pedido #{$order->id} no suman su total.");
        }

        return $items;
    }

    /**
     * Consulta un pago por id. Devuelve null si no se pudo consultar.
     *
     * @return array{status:string,external_reference:?string,amount:float,currency:string}|null
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
            'amount' => (float) $payment->transaction_amount,
            'currency' => (string) $payment->currency_id,
        ];
    }
}
