<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    private function adminWithTwoFactor(): User
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'password']);
        $admin->forceFill([
            'two_factor_secret' => Totp::generateSecret(),
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => [hash('sha256', 'codigo-de-rescate')],
        ])->save();

        return $admin;
    }

    private function loginUntilChallenge(User $admin): void
    {
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('two-factor.challenge'));
    }

    public function test_totp_matches_the_rfc_6238_vector(): void
    {
        // Secreto "12345678901234567890" y t=59 s: el vector de 8 dígitos termina en 287082.
        $this->assertSame('287082', Totp::code('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', 59));
    }

    public function test_admin_with_two_factor_is_not_logged_in_by_password_alone(): void
    {
        $this->loginUntilChallenge($this->adminWithTwoFactor());
        $this->get(route('two-factor.challenge'))->assertOk();

        $this->assertGuest();
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_correct_code_completes_the_login(): void
    {
        $admin = $this->adminWithTwoFactor();
        $this->loginUntilChallenge($admin);

        $this->post(route('two-factor.verify'), ['code' => Totp::code($admin->two_factor_secret)])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_code_is_rejected(): void
    {
        $admin = $this->adminWithTwoFactor();
        $this->loginUntilChallenge($admin);

        $this->post(route('two-factor.verify'), ['code' => '000000'])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a_code_cannot_be_used_twice(): void
    {
        $admin = $this->adminWithTwoFactor();
        $code = Totp::code($admin->two_factor_secret);

        $this->loginUntilChallenge($admin);
        $this->post(route('two-factor.verify'), ['code' => $code])->assertRedirect(route('admin.dashboard'));
        $this->post('/admin/logout');

        $this->loginUntilChallenge($admin);
        $this->post(route('two-factor.verify'), ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_recovery_code_works_once(): void
    {
        $admin = $this->adminWithTwoFactor();
        $this->loginUntilChallenge($admin);

        $this->post(route('two-factor.verify'), ['code' => 'codigo-de-rescate'])->assertRedirect(route('admin.dashboard'));
        $this->assertSame([], $admin->fresh()->two_factor_recovery_codes);
    }

    public function test_challenge_without_a_previous_password_step_goes_back_to_login(): void
    {
        $this->get(route('two-factor.challenge'))->assertRedirect(route('login'));
        $this->post(route('two-factor.verify'), ['code' => '123456'])->assertRedirect(route('login'));
    }

    public function test_challenge_is_rate_limited(): void
    {
        $admin = $this->adminWithTwoFactor();
        $this->loginUntilChallenge($admin);

        foreach (range(1, 5) as $i) {
            $this->post(route('two-factor.verify'), ['code' => '000000']);
        }

        $this->post(route('two-factor.verify'), ['code' => Totp::code($admin->two_factor_secret)])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_admin_without_two_factor_is_sent_to_setup_when_required(): void
    {
        config(['auth.admin_requires_two_factor' => true]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('admin.two-factor.show'));
        $this->actingAs($admin)->get(route('admin.two-factor.show'))->assertOk();
    }

    public function test_admin_can_activate_two_factor_and_gets_recovery_codes(): void
    {
        config(['auth.admin_requires_two_factor' => true]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.two-factor.show'));
        $secret = $admin->fresh()->two_factor_secret;

        $this->actingAs($admin)->post(route('admin.two-factor.confirm'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertFalse($admin->fresh()->hasTwoFactorEnabled());

        $this->actingAs($admin)->post(route('admin.two-factor.confirm'), ['code' => Totp::code($secret)])
            ->assertRedirect(route('admin.two-factor.show'))
            ->assertSessionHas('recovery_codes');

        $admin->refresh();
        $this->assertTrue($admin->hasTwoFactorEnabled());
        $this->assertCount(8, $admin->two_factor_recovery_codes);
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_regular_customers_are_not_affected(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'password' => 'password']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_failed_logins_are_logged_without_the_password(): void
    {
        Log::spy();

        $this->post('/login', ['email' => 'nadie@example.com', 'password' => 'secreta-123']);

        Log::shouldHaveReceived('warning')->withArgs(
            fn ($message, $context = []) => $message === 'Login fallido.'
                && ($context['email'] ?? null) === 'nadie@example.com'
                && ! str_contains(json_encode($context), 'secreta-123')
        )->once();
    }
}
