<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCompany extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'contact_phone',
        'contact_email',
        'service_type',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function zones(): HasMany
    {
        return $this->hasMany(ShippingZone::class);
    }
}
