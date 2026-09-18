<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category',
        'category_id',
        'brand',
        'supplier_id',
        'compatible_model',
        'description',
        'price',
        'cost_price',
        'stock',
        'image_path',
        'is_featured',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock' => 'integer',
            'is_featured' => 'boolean',
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // El catálogo público sigue filtrando por el string `category`; lo mantenemos
        // sincronizado con la categoría real que elige el admin.
        static::saving(function (Product $product) {
            if ($product->category_id) {
                $product->category = Category::find($product->category_id)?->name ?? $product->category;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Promoción vigente aplicable a este producto (puntual > categoría > todo el catálogo). */
    public function activePromotion(): ?Promotion
    {
        return Promotion::vigentes()
            ->where(function ($q) {
                $q->where('scope', Promotion::SCOPE_ALL)
                    ->orWhere(fn ($q2) => $q2->where('scope', Promotion::SCOPE_CATEGORY)->where('category_id', $this->category_id))
                    ->orWhereHas('products', fn ($q2) => $q2->where('products.id', $this->id));
            })
            ->orderByRaw("CASE scope WHEN 'products' THEN 0 WHEN 'category' THEN 1 ELSE 2 END")
            ->first();
    }

    /**
     * Precio efectivo para una cantidad, aplicando la promoción vigente (si hay).
     *
     * @return array{subtotal:float, original_subtotal:float, promotion:?Promotion}
     */
    public function priceFor(int $quantity): array
    {
        $unit = (float) $this->price;
        $original = $unit * $quantity;
        $promotion = $this->activePromotion();

        if (! $promotion) {
            return ['subtotal' => $original, 'original_subtotal' => $original, 'promotion' => null];
        }

        $subtotal = match ($promotion->type) {
            Promotion::TYPE_PERCENTAGE => $original * (1 - min(100, (float) $promotion->value) / 100),
            Promotion::TYPE_FIXED => max(0, $unit - (float) $promotion->value) * $quantity,
            Promotion::TYPE_NXM => (function () use ($quantity, $promotion, $unit) {
                $buy = max(1, (int) $promotion->buy_quantity);
                $pay = min($buy, max(0, (int) $promotion->pay_quantity));
                $groups = intdiv($quantity, $buy);
                $remainder = $quantity % $buy;

                return (($groups * $pay) + $remainder) * $unit;
            })(),
            default => $original,
        };

        return ['subtotal' => round($subtotal, 2), 'original_subtotal' => $original, 'promotion' => $promotion];
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product');
    }

    /** Usar el slug en las URLs: /producto/{slug} */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ---------- Accessors de presentación ---------- */

    /** URL de la imagen subida por el admin, o null para mostrar el placeholder. */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    /** Precio formateado estilo AR: $ 12.500 */
    public function getFormattedPriceAttribute(): string
    {
        return '$ ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /** Modelos de moto compatible como lista: admite separarlos por coma o por línea. */
    public function getCompatibleModelsAttribute(): array
    {
        return collect(preg_split('/[\n,]+/', (string) $this->compatible_model))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    /* ---------- Scopes ---------- */

    /**
     * Aplica los filtros del catálogo.
     *
     * @param  array{search?:string,category?:string,brand?:string,min?:mixed,max?:mixed}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('compatible_model', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn (Builder $q, string $c) => $q->where('category', $c))
            ->when($filters['brand'] ?? null, fn (Builder $q, string $b) => $q->where('brand', $b))
            ->when(is_numeric($filters['min'] ?? null), fn (Builder $q) => $q->where('price', '>=', $filters['min']))
            ->when(is_numeric($filters['max'] ?? null), fn (Builder $q) => $q->where('price', '<=', $filters['max']));
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('active', true);
    }

    /* ---------- Helpers para los <select> de filtros ---------- */

    /** @return \Illuminate\Support\Collection<int,string> */
    public static function categories()
    {
        return static::query()->distinct()->orderBy('category')->pluck('category');
    }

    /** @return \Illuminate\Support\Collection<int,string> */
    public static function brands()
    {
        return static::query()->distinct()->orderBy('brand')->pluck('brand');
    }
}
