<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(MechanicJob::class);
    }
}
