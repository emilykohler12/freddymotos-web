<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Valida la cabecera x-signature de las notificaciones de Mercado Pago.
 * Firma: HMAC-SHA256 de "id:{data.id};request-id:{x-request-id};ts:{ts};" con la clave secreta del webhook.
 *
 * Sin MERCADOPAGO_WEBHOOK_SECRET configurada no se rechaza nada (el webhook igual consulta
 * el pago a la API de MP), pero se deja un aviso en el log para acordarse de cargarla.
 */
class VerifyMercadoPagoSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.mercadopago.webhook_secret');

        if (! $secret) {
            Log::warning('MERCADOPAGO_WEBHOOK_SECRET no está configurada: el webhook no valida la firma.');

            return $next($request);
        }

        $parts = [];
        foreach (explode(',', (string) $request->header('x-signature')) as $piece) {
            [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, '');
            $parts[$key] = $value;
        }

        $ts = $parts['ts'] ?? '';
        $v1 = $parts['v1'] ?? '';
        $id = strtolower((string) $request->input('data.id', $request->input('id', '')));

        $manifest = "id:{$id};request-id:{$request->header('x-request-id')};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        if ($ts === '' || $v1 === '' || ! hash_equals($expected, $v1)) {
            abort(401, 'Firma inválida.');
        }

        return $next($request);
    }
}
