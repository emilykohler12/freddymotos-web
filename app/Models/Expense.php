<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    public const TYPE_GASTO = 'gasto';
    public const TYPE_INGRESO = 'ingreso';

    public const FREQUENCIES = [
        'dia' => 'Diaria',
        'semana' => 'Semanal',
        'quincena' => 'Cada 15 días',
        'mes' => 'Mensual',
        'trimestre' => 'Cada 3 meses',
        'semestre' => 'Cada 6 meses',
        'anio' => 'Anual',
    ];

    protected $fillable = [
        'description',
        'type',
        'category',
        'expense_category_id',
        'amount',
        'frequency',
        'incurred_on',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'incurred_on' => 'date',
        ];
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$ ' . number_format((float) $this->amount, 0, ',', '.');
    }
}
