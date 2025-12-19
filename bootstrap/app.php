<?php

// Override system environment variables for local development
// IMPORTANT: Do not override when running PHPUnit tests (APP_ENV=testing from phpunit.xml)
if (! isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'testing') {
    // Check if running in Docker (DB_HOST=db) or Codespaces (CODESPACES=true)
    $isDocker = isset($_ENV['DB_HOST']) && $_ENV['DB_HOST'] === 'db';
    $isCodespaces = isset($_ENV['CODESPACES']) && $_ENV['CODESPACES'] === 'true';

    if (! $isDocker) {
        $_ENV['APP_ENV'] = 'local';
        $_SERVER['APP_ENV'] = 'local';
        putenv('APP_ENV=local');
    }

    if ($isCodespaces) {
        $_ENV['COMPOSER_VENDOR_DIR'] = '/tmp/vendor';
        $_SERVER['COMPOSER_VENDOR_DIR'] = '/tmp/vendor';
        putenv('COMPOSER_VENDOR_DIR=/tmp/vendor');
    }
}

if (! isset($_ENV['APP_ENV']) || $_ENV['APP_ENV'] !== 'testing') {
    $_ENV['APP_DEBUG'] = 'true';
    $_SERVER['APP_DEBUG'] = 'true';
    putenv('APP_DEBUG=true');
}

// Load the Composer autoloader BEFORE using Laravel classes
// Priority: Codespaces vendor -> Local vendor -> Fallback
if (isset($_ENV['CODESPACES']) && file_exists('/tmp/vendor/autoload.php')) {
    require '/tmp/vendor/autoload.php';
} elseif (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    require __DIR__.'/../../../vendor/autoload.php';
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // Explicitly configure API routes with /api prefix to avoid conflicts with admin panel
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/ai.php'));
        },
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
            'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
