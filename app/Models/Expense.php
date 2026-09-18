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
        'paid',
        'incurred_on',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'incurred_on' => 'date',
            'paid' => 'boolean',
        ];
    }

    /** Pagos únicos o anuales que ya se pagaron: se archivan, no vuelven a molestar. */
    public function isDeactivated(): bool
    {
        return $this->paid && in_array($this->frequency, [null, 'anio'], true);
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
