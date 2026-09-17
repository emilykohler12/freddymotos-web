<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Cart $cart): View
    {
        return view('cart.index', [
            'lines' => $cart->lines(),
            'subtotal' => $cart->subtotal(),
        ]);
    }

    /** POST /carrito/agregar — usado por las tarjetas de producto (fetch) y como fallback normal. */
    public function add(Request $request, Cart $cart): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $quantity = (int) ($data['quantity'] ?? 1);

        if ($product->stock < 1) {
            return $this->respond($request, $cart, 'Este producto está sin stock.', ok: false, status: 422);
        }

        $cart->add($product->id, min($quantity, $product->stock));

        return $this->respond($request, $cart, "«{$product->name}» se agregó al carrito.");
    }

    public function update(Request $request, Product $product, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $quantity = min((int) $data['quantity'], max($product->stock, 0));
        $cart->setQuantity($product->id, $quantity);

        return back()->with('status', 'Carrito actualizado.');
    }

    public function remove(Product $product, Cart $cart): RedirectResponse
    {
        $cart->remove($product->id);

        return back()->with('status', 'Producto quitado del carrito.');
    }

    public function clear(Cart $cart): RedirectResponse
    {
        $cart->clear();

        return back()->with('status', 'Vaciaste el carrito.');
    }

    private function respond(Request $request, Cart $cart, string $message, bool $ok = true, int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => $ok,
                'message' => $message,
                'count' => $cart->count(),
                'subtotal' => $cart->subtotal(),
            ], $status);
        }

        return back()->with($ok ? 'status' : 'error', $message);
    }
}
