<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company',
        'cuit',
        'phone',
        'email',
        'address',
        'payment_terms',
        'notes',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(SupplierPurchase::class);
    }

    /** Deuda total = compras registradas - pagos registrados. */
    public function getDebtAttribute(): float
    {
        return (float) $this->purchases()->sum('amount') - (float) $this->purchases()->sum('paid_amount');
    }

    public function getFormattedDebtAttribute(): string
    {
        return '$ ' . number_format($this->debt, 0, ',', '.');
    }
}
