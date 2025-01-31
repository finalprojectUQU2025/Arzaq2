<?php

use App\Http\Middleware\ActiveMiddleware;
use App\Http\Middleware\UpdatePasswordMiddleware;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('auth.login', 'admin'));
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            return '/cms/admin';
        });
        $middleware->alias([
            'status' => ActiveMiddleware::class,
            'updatePassword' => UpdatePasswordMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
