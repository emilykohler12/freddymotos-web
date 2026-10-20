<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Activación del doble factor (TOTP) para la cuenta de admin que está logueada. */
class TwoFactorController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        if (! $user->hasTwoFactorEnabled() && ! $user->two_factor_secret) {
            $user->forceFill(['two_factor_secret' => Totp::generateSecret()])->save();
        }

        $enabled = $user->hasTwoFactorEnabled();

        return view('admin.two-factor.show', [
            'enabled' => $enabled,
            'secret' => $enabled ? null : $user->two_factor_secret,
            'otpUri' => $enabled ? null : Totp::uri($user->two_factor_secret, $user->email, config('app.name')),
            'recoveryCodes' => $request->session()->get('recovery_codes'),
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled() || ! $user->two_factor_secret) {
            return redirect()->route('admin.two-factor.show');
        }

        $request->validate(['code' => ['required', 'digits:6']]);

        if (! Totp::verify($user->two_factor_secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'El código no es correcto. Revisá que la hora del celular sea automática.']);
        }

        $codes = collect(range(1, 8))->map(fn () => Str::lower(Str::random(16)))->all();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => array_map(fn ($code) => hash('sha256', $code), $codes),
        ])->save();

        return redirect()->route('admin.two-factor.show')->with('recovery_codes', $codes);
    }
}
