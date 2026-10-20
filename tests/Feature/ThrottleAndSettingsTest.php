<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ThrottleAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited_per_email(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])
            ->assertStatus(429);
    }

    public function test_requesting_reset_codes_is_rate_limited(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('password.send-code'), ['email' => $user->email])->assertRedirect();
        }

        $this->post(route('password.send-code'), ['email' => $user->email])->assertStatus(429);
    }

    public function test_formulario_de_configuracion_muestra_turnos_por_dia(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('horarios[lun][manana][abierto]', false)
            ->assertSee('horarios[dom][tarde][hasta]', false);
    }

    public function test_horario_rechaza_tarde_que_empieza_antes_de_cerrar_la_manana(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'horarios' => ['lun' => [
                'manana' => ['abierto' => '1', 'desde' => '09:00', 'hasta' => '13:00'],
                'tarde' => ['abierto' => '1', 'desde' => '12:00', 'hasta' => '20:00'],
            ]],
        ])->assertSessionHasErrors('horarios.lun.tarde.desde');
    }

    public function test_admin_can_save_every_site_setting_field(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'nombre_local' => 'Freddy Motos',
            'telefono' => '1155550000',
            'whatsapp' => '1155551111',
            'email' => 'info@example.com',
            'direccion' => 'Calle 123',
            'horarios' => [
                'lun' => ['manana' => ['abierto' => '1', 'desde' => '09:00', 'hasta' => '13:00'], 'tarde' => ['abierto' => '1', 'desde' => '16:00', 'hasta' => '20:00']],
                'mar' => ['manana' => ['abierto' => '1', 'desde' => '09:00', 'hasta' => '13:00'], 'tarde' => ['abierto' => '1', 'desde' => '16:00', 'hasta' => '20:00']],
                'sab' => ['manana' => ['abierto' => '1', 'desde' => '09:00', 'hasta' => '13:00']],
                'vie' => ['tarde' => ['abierto' => '1', 'desde' => '17:00', 'hasta' => '21:00']],
            ],
            'historia' => 'Una historia',
            'fecha_creacion' => '2010-05-01',
            'instagram_url' => 'https://instagram.com/freddy',
            'facebook_url' => 'https://facebook.com/freddy',
            'moneda' => 'ARS',
            'tax_rate' => '21',
            'payment_methods' => ['efectivo', 'transferencia'],
        ])->assertRedirect();

        SiteSetting::flush();
        $settings = SiteSetting::current();

        $this->assertSame(1, $settings->id);
        $this->assertSame('Freddy Motos', $settings->nombre_local);
        $this->assertSame('1155550000', $settings->telefono);
        $this->assertSame('1155551111', $settings->whatsapp);
        $this->assertSame('info@example.com', $settings->email);
        $this->assertSame('Calle 123', $settings->direccion);
        $this->assertSame(['Lun a Mar: 09:00 a 13:00 y 16:00 a 20:00', 'Vie: 17:00 a 21:00', 'Sáb: 09:00 a 13:00'], $settings->horario_lineas);
        $this->assertNull($settings->horarios['mie']['manana']);
        $this->assertNull($settings->horarios['mie']['tarde']);
        $this->assertSame('Una historia', $settings->historia);
        $this->assertSame('2010-05-01', $settings->fecha_creacion->toDateString());
        $this->assertSame('https://instagram.com/freddy', $settings->instagram_url);
        $this->assertSame('https://facebook.com/freddy', $settings->facebook_url);
        $this->assertSame('ARS', $settings->moneda);
        $this->assertSame('21.00', $settings->tax_rate);
        $this->assertSame(['efectivo', 'transferencia'], $settings->payment_methods);
    }

    public function test_site_setting_ignores_unknown_attributes_in_mass_assignment(): void
    {
        $settings = new SiteSetting(['nombre_local' => 'X', 'created_at' => '2000-01-01', 'otro' => 'y']);

        $this->assertSame('X', $settings->nombre_local);
        $this->assertNull($settings->getAttribute('otro'));
        $this->assertNull($settings->created_at);
    }
}
