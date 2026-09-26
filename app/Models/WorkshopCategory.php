<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function getFormattedPriceAttribute(): ?string
    {
        return $this->price !== null ? '$ ' . number_format((float) $this->price, 0, ',', '.') : null;
    }
}
