<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->isAdmin()) {
            abort(403, 'No tenés acceso al panel de administración.');
        }

        // Un admin sin doble factor solo puede activarlo (o salir): el resto del panel queda bloqueado.
        if (config('auth.admin_requires_two_factor')
            && ! $user->hasTwoFactorEnabled()
            && ! $request->routeIs('admin.two-factor.*', 'admin.logout')) {
            return redirect()->route('admin.two-factor.show');
        }

        return $next($request);
    }
}
