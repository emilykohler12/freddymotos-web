<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            ['description' => 'Alquiler del local', 'category' => 'Fijos', 'amount' => 350000, 'incurred_on' => now()->startOfMonth()->addDays(1)],
            ['description' => 'Luz y agua', 'category' => 'Servicios', 'amount' => 48000, 'incurred_on' => now()->startOfMonth()->addDays(4)],
            ['description' => 'Reposición de mercadería', 'category' => 'Mercadería', 'amount' => 620000, 'incurred_on' => now()->startOfMonth()->addDays(6)],
            ['description' => 'Combustible reparto', 'category' => 'Logística', 'amount' => 32000, 'incurred_on' => now()->subDays(2)],
            ['description' => 'Mantenimiento cartel', 'category' => 'Varios', 'amount' => 25000, 'incurred_on' => now()->subMonth()->subDays(3)],
        ];

        foreach ($expenses as $expense) {
            Expense::updateOrCreate(
                ['description' => $expense['description'], 'incurred_on' => $expense['incurred_on']->toDateString()],
                $expense,
            );
        }
    }
}
