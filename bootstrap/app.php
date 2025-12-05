<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // Explicitly configure API routes with /api prefix to avoid conflicts with admin panel
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register session middleware before SetLocaleMiddleware
        // CRITICAL: Session must be available before locale detection
        $middleware->use([
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        // Register global middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\SetLocaleMiddleware::class);
        $middleware->append(\App\Http\Middleware\SessionTimeoutMiddleware::class);
        $middleware->append(\App\Http\Middleware\ImpersonationMiddleware::class);

        // Register custom middleware aliases
        $middleware->alias([
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'staff' => \App\Http\Middleware\EnsureStaffRole::class,
            'approver' => \App\Http\Middleware\EnsureApproverRole::class,
            'track.portal' => \App\Http\Middleware\TrackPortalActivity::class,
            'guest.ratelimit' => \App\Http\Middleware\GuestFormRateLimiter::class,
            'check.impersonation' => \App\Http\Middleware\CheckImpersonation::class,
            'url.locale' => \App\Http\Middleware\UrlBasedLocale::class,
            'ip.blocking' => \App\Http\Middleware\IpBlockingMiddleware::class,
            'two-factor' => \App\Http\Middleware\TwoFactorVerify::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
