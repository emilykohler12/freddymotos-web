<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.password.request');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $code = PasswordResetCode::generateFor($user->email);
            Mail::to($user->email)->send(new PasswordResetCodeMail($code));
        }

        // Mismo mensaje exista o no la cuenta, para no revelar qué emails están registrados.
        return redirect()
            ->route('password.code-form', ['email' => $data['email']])
            ->with('status', 'Si el correo está registrado, te enviamos un código de 6 dígitos.');
    }

    public function showCodeForm(Request $request): View
    {
        return view('auth.password.code', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $reset = PasswordResetCode::where('email', $data['email'])->first();

        if (! $reset || $reset->isExpired()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'El código venció. Pedí uno nuevo.']);
        }

        if (! $reset->hasAttemptsLeft()) {
            $reset->delete();

            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'Superaste los intentos permitidos. Pedí un código nuevo.']);
        }

        if (! Hash::check($data['code'], $reset->code_hash)) {
            $reset->increment('attempts');

            return back()->withInput($request->only('email'))
                ->withErrors(['code' => 'El código no es correcto.']);
        }

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            $reset->delete();

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        $user->update(['password' => $data['password']]);
        $reset->delete();

        $loginRoute = $user->isAdmin() ? 'admin.login' : 'login';

        return redirect()->route($loginRoute)->with('status', 'Contraseña actualizada. Ya podés ingresar.');
    }
}
