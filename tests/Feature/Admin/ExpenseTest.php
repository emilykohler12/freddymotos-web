<?php

namespace Tests\Feature\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    /** Regresión: el formulario "Nuevo gasto" solo manda due_on, no incurred_on. */
    public function test_creating_expense_without_incurred_on_defaults_to_today(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.expenses.store'), [
            'type' => 'gasto',
            'tab' => 'nuevo-gasto',
            'description' => 'Gasto de test',
            'amount' => 1234,
            'frequency' => 'unica',
            'due_on' => now()->addDays(10)->toDateString(),
        ]);

        $response->assertRedirect(route('admin.expenses.index', ['tab' => 'nuevo-gasto']));
        $this->assertDatabaseHas('expenses', [
            'description' => 'Gasto de test',
            'amount' => 1234,
        ]);
        $expense = Expense::where('description', 'Gasto de test')->first();
        $this->assertNotNull($expense->incurred_on);
        $this->assertTrue($expense->incurred_on->isToday());
    }

    public function test_new_expense_list_orders_by_creation_date_desc(): void
    {
        $admin = $this->admin();
        $old = Expense::create([
            'description' => 'Viejo', 'type' => 'gasto', 'amount' => 10,
            'incurred_on' => now()->subMonth(),
        ]);
        $old->created_at = now()->subDays(5);
        $old->save();

        $new = Expense::create([
            'description' => 'Nuevo', 'type' => 'gasto', 'amount' => 20,
            'incurred_on' => now()->subMonths(2),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.expenses.index', ['tab' => 'nuevo-gasto']));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertTrue(strpos($content, 'Nuevo') < strpos($content, 'Viejo'));
    }

    public function test_category_select_includes_subcategories(): void
    {
        $admin = $this->admin();
        $parent = ExpenseCategory::create(['name' => 'Servicios', 'type' => 'gasto']);
        ExpenseCategory::create(['name' => 'Internet', 'type' => 'gasto', 'parent_id' => $parent->id]);

        $response = $this->actingAs($admin)->get(route('admin.expenses.index', ['tab' => 'nuevo-gasto']));

        $response->assertOk();
        $response->assertSee('Servicios');
        $response->assertSee('— Internet', false);
    }

    public function test_moving_category_to_be_its_own_parent_is_rejected(): void
    {
        $admin = $this->admin();
        $category = ExpenseCategory::create(['name' => 'Cat', 'type' => 'gasto']);

        $response = $this->actingAs($admin)->putJson(route('admin.expense-categories.update', $category), [
            'parent_id' => $category->id,
        ]);

        $response->assertStatus(422);
        $this->assertNull($category->fresh()->parent_id);
    }
}
