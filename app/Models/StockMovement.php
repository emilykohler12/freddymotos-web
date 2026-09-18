<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const REASON_COMPRA = 'compra';
    public const REASON_VENTA_LOCAL = 'venta_local';
    public const REASON_AJUSTE = 'ajuste';

    public const REASONS = [
        self::REASON_COMPRA => 'Compra / reposición',
        self::REASON_VENTA_LOCAL => 'Venta en el local',
        self::REASON_AJUSTE => 'Ajuste',
    ];

    protected $fillable = [
        'product_id',
        'user_id',
        'reason',
        'quantity_change',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
