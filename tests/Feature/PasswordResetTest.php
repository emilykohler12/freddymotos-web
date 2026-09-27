<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_requesting_a_code_sends_a_six_digit_code_by_mail(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post(route('password.send-code'), ['email' => $user->email])
            ->assertRedirect(route('password.code-form', ['email' => $user->email]));

        Mail::assertSent(PasswordResetCodeMail::class);
        $this->assertDatabaseHas('password_reset_codes', ['email' => $user->email]);
    }

    public function test_resetting_password_with_correct_code_logs_in_afterwards(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->post(route('password.send-code'), ['email' => $user->email]);

        $rawCode = null;
        Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use (&$rawCode) {
            $rawCode = $mail->code;

            return true;
        });

        $this->assertNotNull($rawCode, 'No se pudo recuperar el código generado en el test.');

        $response = $this->post(route('password.reset'), [
            'email' => $user->email,
            'code' => $rawCode,
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('nueva-clave-123', $user->fresh()->password));
    }

    public function test_wrong_code_is_rejected(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->post(route('password.send-code'), ['email' => $user->email]);

        $response = $this->post(route('password.reset'), [
            'email' => $user->email,
            'code' => '000000',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ]);

        $response->assertSessionHasErrors('code');
    }
}
