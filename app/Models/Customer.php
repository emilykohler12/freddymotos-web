<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'dni_cuit',
        'address',
        'city',
        'province',
        'postal_code',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Total gastado en pedidos pagos. */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->orders()->paid()->sum('total');
    }

    public function getFormattedTotalSpentAttribute(): string
    {
        return '$ ' . number_format($this->total_spent, 0, ',', '.');
    }

    public function getLastOrderAtAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->orders()->latest()->value('created_at');
    }

    /** Clientes "atendidos": los que tienen al menos un pedido pago. */
    public function scopeConCompra($query)
    {
        return $query->whereHas('orders', fn ($q) => $q->paid());
    }
}
