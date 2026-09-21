<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MechanicJob extends Model
{
    protected $fillable = [
        'mechanic_id',
        'moto',
        'problema',
        'product_id',
        'quantity',
        'monto_a_pagar',
        'pagado',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'monto_a_pagar' => 'decimal:2',
            'pagado' => 'boolean',
        ];
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedMontoAttribute(): string
    {
        return '$ ' . number_format((float) $this->monto_a_pagar, 0, ',', '.');
    }
}
