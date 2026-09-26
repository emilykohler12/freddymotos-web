<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mechanic extends Model
{
    use SoftDeletes;
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
