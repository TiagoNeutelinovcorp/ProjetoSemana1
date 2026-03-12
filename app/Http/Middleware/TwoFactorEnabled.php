<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->two_factor_secret) {
            return redirect()->route('profile.index')
                ->with('mensagem', 'Precisas de ativar o 2FA para acederes a esta funcionalidade.');
        }

        return $next($request);
    }
}
