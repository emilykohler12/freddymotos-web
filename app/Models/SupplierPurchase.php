<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPurchase extends Model
{
    public const STATUS_PENDIENTE = 'pendiente';
    public const STATUS_PAGADO = 'pagado';
    public const STATUS_CANCELADO = 'cancelado';

    public const STATUSES = [
        self::STATUS_PENDIENTE => 'Pendiente',
        self::STATUS_PAGADO => 'Pagado',
        self::STATUS_CANCELADO => 'Cancelado',
    ];

    protected $fillable = [
        'supplier_id',
        'product_id',
        'quantity',
        'description',
        'amount',
        'paid_amount',
        'status',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'quantity' => 'integer',
            'purchased_at' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
