<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierPurchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_registering_a_purchase_selects_a_product_and_increments_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $supplier = Supplier::create(['name' => 'Proveedor Test']);
        $product = Product::create([
            'name' => 'Casco Integral', 'slug' => 'casco-integral', 'category' => 'Cascos',
            'brand' => 'Marca X', 'price' => 500, 'cost_price' => 100, 'stock' => 5,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.suppliers.purchases.store', $supplier), [
            'product_id' => $product->id,
            'quantity' => 3,
            'status' => SupplierPurchase::STATUS_PENDIENTE,
            'purchased_at' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertDatabaseHas('supplier_purchases', [
            'supplier_id' => $supplier->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'amount' => 300,
            'status' => 'pendiente',
        ]);
        $this->assertSame(300.0, $supplier->fresh()->debt);
    }

    public function test_cancelled_purchase_does_not_increment_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $supplier = Supplier::create(['name' => 'Proveedor Test']);
        $product = Product::create([
            'name' => 'Filtro de aire', 'slug' => 'filtro-de-aire', 'category' => 'Filtros',
            'brand' => 'Marca Y', 'price' => 200, 'cost_price' => 50, 'stock' => 10,
        ]);

        $this->actingAs($admin)->post(route('admin.suppliers.purchases.store', $supplier), [
            'product_id' => $product->id,
            'quantity' => 2,
            'status' => SupplierPurchase::STATUS_CANCELADO,
            'purchased_at' => now()->toDateString(),
        ]);

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0.0, $supplier->fresh()->debt);
    }

    public function test_paid_purchase_does_not_count_as_debt(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $supplier = Supplier::create(['name' => 'Proveedor Test']);
        $product = Product::create([
            'name' => 'Bujía', 'slug' => 'bujia', 'category' => 'Motor',
            'brand' => 'Marca Z', 'price' => 50, 'cost_price' => 10, 'stock' => 0,
        ]);

        $this->actingAs($admin)->post(route('admin.suppliers.purchases.store', $supplier), [
            'product_id' => $product->id,
            'quantity' => 5,
            'status' => SupplierPurchase::STATUS_PAGADO,
            'purchased_at' => now()->toDateString(),
        ]);

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0.0, $supplier->fresh()->debt);
    }
}
