<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Carrito guardado en la sesión del usuario (no requiere login).
 * En la sesión solo se guarda un mapa [product_id => cantidad];
 * los datos del producto (precio, stock, nombre) se resuelven frescos
 * desde la base cada vez que se leen las líneas.
 */
class Cart
{
    private const KEY = 'cart';

    /** @return array<int,int> */
    private function raw(): array
    {
        return array_map('intval', session()->get(self::KEY, []));
    }

    private function persist(array $items): void
    {
        $items = array_filter($items, fn ($qty) => $qty > 0);

        $items === []
            ? session()->forget(self::KEY)
            : session()->put(self::KEY, $items);
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $items = $this->raw();
        $items[$productId] = ($items[$productId] ?? 0) + max(1, $quantity);
        $this->persist($items);
    }

    /** Fija la cantidad exacta de una línea (0 = quitar). */
    public function setQuantity(int $productId, int $quantity): void
    {
        $items = $this->raw();
        $items[$productId] = max(0, $quantity);
        $this->persist($items);
    }

    public function remove(int $productId): void
    {
        $items = $this->raw();
        unset($items[$productId]);
        $this->persist($items);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /**
     * Líneas del carrito con el producto resuelto.
     *
     * @return Collection<int,object{product:Product,quantity:int,unit_price:float,subtotal:float}>
     */
    public function lines(): Collection
    {
        $items = $this->raw();

        if ($items === []) {
            return collect();
        }

        $products = Product::query()->whereIn('id', array_keys($items))->get()->keyBy('id');

        return collect($items)
            ->filter(fn ($qty, $id) => $products->has((int) $id))
            ->map(function ($qty, $id) use ($products) {
                /** @var Product $product */
                $product = $products->get((int) $id);
                $unit = (float) $product->price;

                return (object) [
                    'product' => $product,
                    'quantity' => (int) $qty,
                    'unit_price' => $unit,
                    'subtotal' => $unit * (int) $qty,
                ];
            })
            ->values();
    }

    /** Cantidad total de unidades (para el badge del navbar). */
    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function subtotal(): float
    {
        return (float) $this->lines()->sum('subtotal');
    }

    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }
}
