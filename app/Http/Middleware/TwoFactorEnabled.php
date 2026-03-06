<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorEnabled
{
    /**
     * Rotas que devem ser acessíveis mesmo sem 2FA
     */
    protected $except = [
        'profile.*',
        'profile.index',
        'profile.edit',
        'profile.password',
        'two-factor.enable',
        'two-factor.disable',
        'two-factor.show',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se a rota atual está na lista de exceções
        $routeName = $request->route() ? $request->route()->getName() : '';

        if (in_array($routeName, $this->except)) {
            return $next($request);
        }

        // Verificar se o utilizador está autenticado e tem 2FA ativo
        if (!$request->user() || !$request->user()->two_factor_secret) {
            return redirect()->route('profile.index')
                ->with('mensagem', 'Precisas de ativar o 2FA para acederes a esta funcionalidade.');
        }

        return $next($request);
    }
}
