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

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

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
            /** @see \Laravel\Sanctum\Http\Middleware\CheckAbilities */
            'abilities' => CheckAbilities::class,
            /** @see \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility */
            'ability' => CheckForAnyAbility::class,
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

        // Configure rate limiting for broadcasting auth endpoint
        // Requirement 7.4: Limit to 60 requests per minute per IP
        RateLimiter::for('broadcasting', function (Request $request) {
            /** @var \Illuminate\Cache\RateLimiting\Limit $limit */
            return Limit::perMinute(60)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak percubaan pengesahan. Sila tunggu sebentar.',
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
