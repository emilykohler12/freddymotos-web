<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Promotion extends Model
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';
    public const TYPE_NXM = 'nxm';

    public const SCOPE_ALL = 'all';
    public const SCOPE_CATEGORY = 'category';
    public const SCOPE_PRODUCTS = 'products';

    protected $fillable = [
        'title',
        'type',
        'value',
        'buy_quantity',
        'pay_quantity',
        'scope',
        'category_id',
        'active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'buy_quantity' => 'integer',
            'pay_quantity' => 'integer',
            'active' => 'boolean',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    /** Promociones activas y dentro de su rango de fechas (si tiene). */
    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where('active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()));
    }

    /** Texto corto del descuento: "20% OFF", "$ 1.000 OFF", "2x1". */
    public function getLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENTAGE => rtrim(rtrim(number_format((float) $this->value, 2, ',', '.'), '0'), ',') . '% OFF',
            self::TYPE_FIXED => '$ ' . number_format((float) $this->value, 0, ',', '.') . ' OFF',
            self::TYPE_NXM => "{$this->buy_quantity}x{$this->pay_quantity}",
            default => '',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PERCENTAGE => 'Porcentaje',
            self::TYPE_FIXED => 'Monto fijo',
            self::TYPE_NXM => 'Cantidad x cantidad (ej. 2x1)',
            default => $this->type,
        };
    }

    /** A qué aplica: todo el catálogo, una categoría, o productos puntuales. */
    public function getScopeLabelAttribute(): string
    {
        return match ($this->scope) {
            self::SCOPE_CATEGORY => $this->category?->name ?? 'Una categoría',
            self::SCOPE_PRODUCTS => $this->products->count() . ' ' . Str::plural('producto', $this->products->count()),
            default => 'Todo el catálogo',
        };
    }

    /** A dónde manda el botón "Ver productos" de la tarjeta en el Home. */
    public function getTargetUrlAttribute(): string
    {
        if ($this->scope === self::SCOPE_CATEGORY && $this->category) {
            return route('products.index', ['category' => $this->category->name]);
        }

        return route('products.index');
    }
}
