<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Segundo paso del login de admin: la contraseña ya fue correcta, falta el código del autenticador.
 * Hasta acá el usuario NO está logueado; solo su id queda en la sesión, por 5 minutos.
 */
class TwoFactorChallengeController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $this->pendingUser($request)) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login')->withErrors(['email' => 'La verificación venció. Ingresá de nuevo.']);
        }

        $request->validate(['code' => ['required', 'string', 'max:32']]);
        $input = preg_replace('/\s+/', '', (string) $request->input('code'));

        if (preg_match('/^\d{6}$/', $input)) {
            // Cache::add es atómico: un mismo código no se puede usar dos veces.
            $valid = Totp::verify($user->two_factor_secret, $input)
                && Cache::add("totp-used:{$user->id}:{$input}", true, 120);
        } else {
            $valid = $user->consumeRecoveryCode($input);
        }

        if (! $valid) {
            Log::warning('Código de doble factor incorrecto en cuenta admin.', ['user_id' => $user->id, 'ip' => $request->ip()]);

            return back()->withErrors(['code' => 'El código no es correcto.']);
        }

        $request->session()->forget(['two_factor_user_id', 'two_factor_started_at']);
        Auth::login($user);
        $request->session()->regenerate();

        Log::info('Ingreso al panel de administración.', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return redirect()->route('admin.dashboard');
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('two_factor_user_id');
        $startedAt = (int) $request->session()->get('two_factor_started_at', 0);

        if (! $id || now()->timestamp - $startedAt > 300) {
            return null;
        }

        $user = User::find($id);

        return $user && $user->isAdmin() && $user->hasTwoFactorEnabled() ? $user : null;
    }
}
