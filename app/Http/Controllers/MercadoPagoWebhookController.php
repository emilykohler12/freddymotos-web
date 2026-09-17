<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Webhook de Mercado Pago: POST /webhook/mercadopago
 * Cuando MP confirma un pago aprobado, marca el pedido como "pagado".
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

        if (! $payment || ! $payment['external_reference']) {
            return response()->json(['ok' => false], 200);
        }

        $order = Order::find((int) $payment['external_reference']);

        if ($order && $payment['status'] === 'approved' && ! $order->isPaid()) {
            $order->markPaid((string) $paymentId);
        }

        return response()->json(['ok' => true]);
    }
}
