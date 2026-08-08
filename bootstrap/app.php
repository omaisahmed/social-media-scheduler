<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Modules\Core\Http\Middleware\EnsureBusinessSetup;
use Modules\Core\Http\Middleware\SetBusinessContext;
use Modules\Core\Http\Middleware\SetUserPreferences;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetBusinessContext::class,
            SetUserPreferences::class,
        ]);

        $middleware->alias([
            'business.setup' => EnsureBusinessSetup::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
