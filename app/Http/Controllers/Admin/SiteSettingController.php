<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessPhoto;
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
            'photos' => BusinessPhoto::latest()->get(),
        ]);
    }

    public function storePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:4096'],
        ]);

        BusinessPhoto::create([
            'path' => $request->file('photo')->store('business', 'public'),
        ]);

        return redirect()->route('admin.settings.edit', ['tab' => 'negocio'])->with('status', 'Foto agregada.');
    }

    public function destroyPhoto(BusinessPhoto $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.settings.edit', ['tab' => 'negocio'])->with('status', 'Foto eliminada.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre_local' => ['nullable', 'string', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'whatsapp' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'email' => ['nullable', 'email', 'max:160'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'horario_atencion' => ['nullable', 'string', 'max:200'],
            'historia' => ['nullable', 'string', 'max:2000'],
            'fecha_creacion' => ['nullable', 'date', 'before_or_equal:today'],
            'instagram_url' => ['nullable', 'url', 'max:200'],
            'facebook_url' => ['nullable', 'url', 'max:200'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['sometimes', 'boolean'],
            'hero_photo' => ['nullable', 'image', 'max:4096'],
            'remove_hero_photo' => ['sometimes', 'boolean'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['string', 'in:efectivo,transferencia,tarjeta_debito,tarjeta_credito'],
            'tab' => ['nullable', 'string', 'in:general,negocio,pagos'],
        ]);

        $data['payment_methods'] = $data['payment_methods'] ?? [];
        $tab = $data['tab'] ?? 'general';
        unset($data['tab']);

        $settings = SiteSetting::query()->firstOrNew(['id' => 1]);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        } elseif ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $data['logo_path'] = null;
        }
        unset($data['logo'], $data['remove_logo']);

        if ($request->hasFile('hero_photo')) {
            if ($settings->hero_photo_path) {
                Storage::disk('public')->delete($settings->hero_photo_path);
            }
            $data['hero_photo_path'] = $request->file('hero_photo')->store('branding', 'public');
        } elseif ($request->boolean('remove_hero_photo') && $settings->hero_photo_path) {
            Storage::disk('public')->delete($settings->hero_photo_path);
            $data['hero_photo_path'] = null;
        }
        unset($data['hero_photo'], $data['remove_hero_photo']);

        $settings->fill($data)->save();

        SiteSetting::flush();

        return redirect()
            ->route('admin.settings.edit', ['tab' => $tab])
            ->with('status', 'La configuración del negocio se guardó correctamente.');
    }
}
