<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * API Rate Limiting Service Provider
 *
 * Configures rate limiters for API endpoints per Requirement 37.4:
 * - Authenticated tokens: 60 requests/minute (per user)
 * - Unauthenticated requests: 10 requests/minute (per IP)
 *
 * @see D03 SRS-API-001
 * @see Requirement 37.4
 */
class ApiRateLimitingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * Configure API rate limiters in bootstrap/app.php as per Laravel 12 conventions.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * Per Requirement 37.4:
     * - Authenticated tokens: 60 requests/minute
     * - Unauthenticated requests: 10 requests/minute
     */
    protected function configureRateLimiting(): void
    {
        // Main API rate limiter
        // Authenticated users: 60 requests/minute (by user ID)
        // Unauthenticated: 10 requests/minute (by IP)
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        // Separate rate limiter for API tokens (Sanctum authenticated)
        // Authenticated API tokens: 60 requests/minute
        // Unauthenticated: 10 requests/minute by IP
        RateLimiter::for('api-token', function (Request $request) {
            if ($request->user()) {
                return Limit::perMinute(60)->by('token:'.$request->user()->id);
            }

            return Limit::perMinute(10)->by('ip:'.$request->ip());
        });

        // Strict rate limiter for unauthenticated API access
        // Always 10 requests/minute by IP
        RateLimiter::for('api-guest', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
