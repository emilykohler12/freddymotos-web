<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre_local' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'horario_atencion' => ['nullable', 'string', 'max:200'],
            'historia' => ['nullable', 'string', 'max:2000'],
            'fecha_creacion' => ['nullable', 'date', 'before_or_equal:today'],
            'instagram_url' => ['nullable', 'url', 'max:200'],
            'facebook_url' => ['nullable', 'url', 'max:200'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['string', 'in:mercadopago,efectivo,transferencia,whatsapp'],
            'banco' => ['nullable', 'string', 'max:120'],
            'cbu_alias' => ['nullable', 'string', 'max:80'],
            'titular_cuenta' => ['nullable', 'string', 'max:120'],
            'mp_public_key' => ['nullable', 'string', 'max:200'],
            'mp_access_token' => ['nullable', 'string', 'max:200'],
        ]);

        $data['payment_methods'] = $data['payment_methods'] ?? [];

        $settings = SiteSetting::query()->firstOrNew(['id' => 1]);

        // Los secretos de Mercado Pago no se pisan si se deja el campo vacío (ya están guardados).
        if (empty($data['mp_access_token'])) {
            unset($data['mp_access_token']);
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }
        unset($data['logo']);

        $settings->fill($data)->save();

        SiteSetting::flush();

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'La configuración del negocio se guardó correctamente.');
    }
}
