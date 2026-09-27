<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'password']);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_regular_user_login_redirects_home_not_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'password' => 'password']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
    }

    public function test_old_admin_login_route_no_longer_exists(): void
    {
        $response = $this->get('/admin/login');

        $response->assertNotFound();
    }

    public function test_guest_hitting_admin_is_redirected_to_unified_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_reach_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('Dashboard');
    }

    public function test_logout_redirects_admin_to_login(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
