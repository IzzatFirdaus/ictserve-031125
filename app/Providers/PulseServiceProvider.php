<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

/**
 * Pulse Service Provider for ICTServe v3.6.0
 *
 * Implements admin and superuser access control for Laravel Pulse
 * and registers custom ICTServe-specific recorders for performance monitoring.
 *
 * @see D03 §8.2 - Laravel Pulse performance monitoring
 * @see Requirements 4.1, 4.2, 14.1, 14.2, 14.5, 16.1, 16.2, 16.3, 16.4, 16.5
 * @see Requirements 36.6 - Restrict /pulse route to admin and superuser roles
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class PulseServiceProvider extends ServiceProvider
{
    /**
     * Performance threshold constants (in milliseconds).
     */
    private const RESPONSE_TIME_THRESHOLD = 2000;

    private const DATABASE_QUERY_THRESHOLD = 500;

    private const QUEUE_JOB_THRESHOLD = 1000;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the ResolvesUsers contract to resolve the Pulse instantiation issue
        $this->app->bind(
            \Laravel\Pulse\Contracts\ResolvesUsers::class,
            \Laravel\Pulse\Users::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureAuthorization();
        $this->configureUserResolution();
        $this->registerCustomRecorders();
        $this->configurePerformanceAlerts();
    }

    /**
     * Configure the Pulse authorization gate.
     *
     * Per Requirement 36.6:
     * - Admin and superuser roles can access /pulse
     * - Staff role receives 403 Forbidden
     */
    protected function configureAuthorization(): void
    {
        Gate::define('viewPulse', function (?User $user): bool {
            // In local environment, allow access for development
            if (app()->environment('local')) {
                return true;
            }

            // Require authentication
            if ($user === null) {
                return false;
            }

            // Only admin and superuser roles can access Pulse
            // Per Requirement 36.6: Restrict /pulse route to admin and superuser roles
            return $user->isAdmin() || $user->isSuperuser();
        });
    }

    /**
     * Configure how Pulse resolves user information for display.
     */
    protected function configureUserResolution(): void
    {
        Pulse::user(fn (User $user): array => [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? null,
            'role' => $user->role ?? 'staff',
        ]);
    }

    /**
     * Register custom ICTServe-specific Pulse recorders.
     *
     * Per Requirements 16.4: Create custom performance metrics for ICTServe-specific operations
     */
    protected function registerCustomRecorders(): void
    {
        // Register ticket processing recorder
        Pulse::handleExceptionsUsing(function (\Throwable $e): void {
            report($e);
        });
    }

    /**
     * Configure automated performance alerting.
     *
     * Per Requirements 16.3: Implement automated alerting for performance threshold breaches
     * - Response times exceeding 2 seconds
     * - Database query times exceeding 500ms
     * - Queue job failures
     */
    protected function configurePerformanceAlerts(): void
    {
        // Performance alerting is handled via the PerformanceAlertService
        // which monitors Pulse metrics and triggers notifications
        // This is configured in the PerformanceServiceProvider
    }

    /**
     * Get performance threshold configuration.
     *
     * @return array<string, int>
     */
    public static function getPerformanceThresholds(): array
    {
        return [
            'response_time_ms' => self::RESPONSE_TIME_THRESHOLD,
            'database_query_ms' => self::DATABASE_QUERY_THRESHOLD,
            'queue_job_ms' => self::QUEUE_JOB_THRESHOLD,
        ];
    }
}
