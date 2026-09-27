<?php

namespace Tests\Feature\Admin;

use App\Models\Mechanic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MechanicJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_can_be_created_without_moto_or_problema(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $mechanic = Mechanic::create(['name' => 'Mecánico Test']);

        $response = $this->actingAs($admin)->post(route('admin.mechanic-jobs.store'), [
            'mechanic_id' => $mechanic->id,
            'monto_a_pagar' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mechanic_jobs', [
            'mechanic_id' => $mechanic->id,
            'moto' => null,
            'problema' => null,
            'monto_a_pagar' => 100,
        ]);
    }

    public function test_deleting_mechanic_keeps_debt_visible_on_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $mechanic = Mechanic::create(['name' => 'Mecánico con deuda']);
        $mechanic->jobs()->create(['monto_a_pagar' => 550, 'pagado' => false]);

        $mechanic->delete();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Mecánico con deuda');
        $response->assertSee('$ 550');
    }

    public function test_marking_job_as_paid_records_paid_at_for_dashboard_chart(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $mechanic = Mechanic::create(['name' => 'Mecánico Test']);
        $job = $mechanic->jobs()->create(['monto_a_pagar' => 200, 'pagado' => false]);

        $this->actingAs($admin)->post(route('admin.mechanic-jobs.toggle-paid', $job));

        $job->refresh();
        $this->assertTrue($job->pagado);
        $this->assertNotNull($job->paid_at);
    }
}
