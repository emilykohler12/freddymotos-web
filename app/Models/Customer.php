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
        return (float) $this->orders()
            ->where(function ($q) {
                $q->where('payment_status', Order::PAYMENT_STATUS_PAGADO)
                    ->orWhere('status', Order::STATUS_PAGADO);
            })
            ->sum('total');
    }

    public function getFormattedTotalSpentAttribute(): string
    {
        return '$ ' . number_format($this->total_spent, 0, ',', '.');
    }

    public function getLastOrderAtAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->orders()->latest()->value('created_at');
    }
}
