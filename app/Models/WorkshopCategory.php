<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopCategory extends Model
{
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
