<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'is_admin'   => \App\Http\Middleware\IsAdmin::class,
            'is_peserta' => \App\Http\Middleware\IsPeserta::class,
            'seb'        => \App\Http\Middleware\EnsureSafeExamBrowser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function () {
        // Rate limiter untuk AJAX endpoint ujian
        RateLimiter::for('exam-ajax', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter untuk login (mencegah brute force)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    })
    ->create();
