<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MinorFindingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_cap_per_account_holds_even_if_the_ip_header_is_spoofed(): void
    {
        $user = User::factory()->create();

        // Cada intento con una IP falsa distinta: los límites por IP no se activan,
        // pero el tope por cuenta (30 por hora) sí tiene que frenar el intento 31.
        for ($i = 1; $i <= 30; $i++) {
            $this->withHeader('X-Forwarded-For', "203.0.113.{$i}")
                ->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])
                ->assertSessionHasErrors('email');
        }

        $this->withHeader('X-Forwarded-For', '203.0.113.200')
            ->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])
            ->assertStatus(429);
    }

    public function test_reset_code_form_takes_the_email_from_the_session_not_from_the_url(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post(route('password.send-code'), ['email' => $user->email])
            ->assertRedirect(route('password.code-form'));

        $this->get(route('password.code-form'))->assertOk()->assertSee($user->email);

        // Un ?email= en la URL ya no rellena nada.
        $this->flushSession();
        $this->get(route('password.code-form', ['email' => 'otro@example.com']))
            ->assertOk()
            ->assertDontSee('otro@example.com');
    }

    public function test_reset_rejects_a_password_that_does_not_meet_the_defaults(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.reset'), [
            'email' => $user->email,
            'code' => '123456',
            'password' => 'corta',
            'password_confirmation' => 'corta',
        ])->assertSessionHasErrors('password');
    }

    public function test_order_form_does_not_inject_product_names_as_html(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $html = $this->actingAs($admin)->get(route('admin.orders.create'))->assertOk()->getContent();

        $this->assertStringNotContainsString('${name}</span>', $html);
        $this->assertStringContainsString('chip.textContent = name', $html);
    }
}
