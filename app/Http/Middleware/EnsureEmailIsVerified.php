<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Se o email já está verificado, deixa passar
        if (!is_null($user->email_verified_at)) {
            return $next($request);
        }

        // Se não está verificado, redireciona para a página de verificação
        return redirect()->route('verification.notice');
    }
}
