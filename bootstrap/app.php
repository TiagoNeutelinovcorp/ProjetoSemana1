<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',  // ← ADICIONA ISTO
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'verified.email' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'two-factor' => \App\Http\Middleware\TwoFactorEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
