<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Webhook de Mercado Pago: POST /webhook/mercadopago
 * MP avisa cambios de un pago; el estado real se consulta a la API de MP (no se confía en el body).
 *
 * (En local, MP no puede llegar a esta URL; usar ngrok o probar en producción.)
 */
class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mp): JsonResponse
    {
        // MP envía distintos formatos: { type: "payment", data: { id } } o ?topic=payment&id=
        $type = $request->input('type', $request->input('topic'));
        $paymentId = $request->input('data.id', $request->input('id'));

        if ($type !== 'payment' || ! $paymentId) {
            return response()->json(['ignored' => true]);
        }

        $payment = $mp->getPayment((string) $paymentId);

        // No se pudo consultar a MP: 503 para que reintente la notificación en vez de perder el pago.
        if (! $payment) {
            return response()->json(['ok' => false], 503);
        }

        if (! $payment['external_reference']) {
            return response()->json(['ok' => false], 200);
        }

        DB::transaction(function () use ($payment, $paymentId) {
            // Fila bloqueada: dos notificaciones simultáneas del mismo pago no se pisan.
            $order = Order::whereKey((int) $payment['external_reference'])->lockForUpdate()->first();

            if (! $order) {
                return;
            }

            match ($payment['status']) {
                'approved' => $this->approved($order, $payment, (string) $paymentId),
                'refunded', 'charged_back' => $this->refunded($order),
                'rejected', 'cancelled' => $this->rejected($order),
                default => null, // pending / in_process: todavía no hay nada que hacer
            };
        });

        return response()->json(['ok' => true]);
    }

    private function approved(Order $order, array $payment, string $paymentId): void
    {
        $currency = (string) config('services.mercadopago.currency', 'ARS');

        // Un pago por otro monto o moneda no se da por bueno: queda pendiente y se avisa.
        if (abs($payment['amount'] - (float) $order->total) > 0.009 || ($payment['currency'] !== '' && $payment['currency'] !== $currency)) {
            report(new RuntimeException("Pago de Mercado Pago con monto o moneda distintos a los del pedido #{$order->id}."));

            return;
        }

        if ($order->isPaid()) {
            return;
        }

        $order->markPaid($paymentId);

        // Cobrado sobre un pedido que ya estaba cancelado: no se puede cumplir, queda para reembolsar.
        if ($order->status === Order::STATUS_CANCELADO) {
            $order->update(['refund_status' => Order::REFUND_STATUS_PENDIENTE]);
        }
    }

    private function refunded(Order $order): void
    {
        if ($order->isPaid() && $order->refund_status !== Order::REFUND_STATUS_REEMBOLSADO) {
            $order->update(['refund_status' => Order::REFUND_STATUS_REEMBOLSADO]);
        }
    }

    private function rejected(Order $order): void
    {
        if (! $order->isPaid() && $order->payment_status !== Order::PAYMENT_STATUS_RECHAZADO) {
            $order->update(['payment_status' => Order::PAYMENT_STATUS_RECHAZADO]);
        }
    }
}
