<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class CustomLoginController extends Controller
{
    /**
     * Handle login for cliente
     */
    public function loginCliente(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verifica se é cliente
            if ($user->role !== 'cliente') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Esta área é apenas para clientes.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/livros');
        }

        return back()->withErrors([
            'email' => 'As credenciais não correspondem.',
        ]);
    }

    /**
     * Handle login for bibliotecario
     */
    public function loginBibliotecario(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verifica se é bibliotecário
            if ($user->role !== 'bibliotecario') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Esta área é apenas para bibliotecários.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/livros');
        }

        return back()->withErrors([
            'email' => 'As credenciais não correspondem.',
        ]);
    }
}
