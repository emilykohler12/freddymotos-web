<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Expense extends Model
{
    public const TYPE_GASTO = 'gasto';
    public const TYPE_INGRESO = 'ingreso';

    public const FREQUENCIES = [
        'unica' => 'Única vez',
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
        return $this->paid && in_array($this->frequency, [null, 'unica', 'anio'], true);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$ ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /** Próxima fecha en la que corresponde pagar este gasto recurrente, o null si es único. */
    public function nextDueDate(): ?Carbon
    {
        $base = $this->incurred_on->copy();

        return match ($this->frequency) {
            'dia' => $base->addDay(),
            'semana' => $base->addWeek(),
            'quincena' => $base->addDays(15),
            'mes' => $base->addMonth(),
            'trimestre' => $base->addMonths(3),
            'semestre' => $base->addMonths(6),
            'anio' => $base->addYear(),
            default => null,
        };
    }

    /** Gastos recurrentes (luz, agua, etc.) cuyo próximo pago ya venció. */
    public static function due(): Collection
    {
        return static::where('type', self::TYPE_GASTO)
            ->whereNotNull('frequency')
            ->where('frequency', '!=', 'unica')
            ->get()
            ->groupBy('description')
            ->map(fn ($group) => $group->sortByDesc('incurred_on')->first())
            ->map(function (self $expense) {
                $expense->next_due_on = $expense->nextDueDate();

                return $expense;
            })
            ->filter(fn (self $expense) => $expense->next_due_on && now()->greaterThanOrEqualTo($expense->next_due_on))
            ->sortBy('next_due_on')
            ->values();
    }
}
