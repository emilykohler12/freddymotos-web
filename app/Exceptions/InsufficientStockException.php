<?php

namespace App\Exceptions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Falta stock para completar un pedido. Se lanza dentro de una DB::transaction (que se revierte)
 * y se muestra como un mensaje al usuario, en el checkout o en el panel admin.
 */
class InsufficientStockException extends RuntimeException
{
    public function __construct(public readonly string $productName, public readonly int $available)
    {
        parent::__construct("No hay stock suficiente de «{$productName}» (disponible: {$available}).");
    }

    public function render(Request $request): RedirectResponse
    {
        if ($request->routeIs('checkout.store')) {
            return redirect()->route('cart.index')->with('error', $this->getMessage());
        }

        return back()->withInput()->with('error', $this->getMessage());
    }
}
