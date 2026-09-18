<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'reason' => ['required', 'in:' . implode(',', array_keys(StockMovement::REASONS))],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $isEntrada = $data['reason'] === StockMovement::REASON_COMPRA;
        $change = $isEntrada ? $data['quantity'] : -$data['quantity'];

        $redirect = redirect()->route('admin.activity.index', ['tab' => 'inventario']);

        if (! $isEntrada && $data['quantity'] > $product->stock) {
            return $redirect->with('error', "No hay {$data['quantity']} unidades de «{$product->name}» para descontar (stock actual: {$product->stock}).");
        }

        DB::transaction(function () use ($product, $data, $change) {
            $product->increment('stock', $change);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth('web')->id(),
                'reason' => $data['reason'],
                'quantity_change' => $change,
                'note' => $data['note'] ?? null,
            ]);
        });

        return $redirect->with('status', 'Stock actualizado.');
    }
}
