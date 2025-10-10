<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ========================
        // 🔐 Middleware Global & Alias
        // ========================

        // Jika kamu punya middleware custom lain, bisa tambahkan di sini juga.
        // Contoh:
        // $middleware->web(YourCustomMiddleware::class);

        // Alias untuk admin session middleware
        $middleware->alias([
            'admin.session' => \App\Http\Middleware\AdminSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
