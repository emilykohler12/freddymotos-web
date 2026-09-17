<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    public const TYPE_GASTO = 'gasto';
    public const TYPE_INGRESO = 'ingreso';

    protected $fillable = [
        'name',
        'type',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
