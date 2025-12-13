<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

/**
 * Pulse Service Provider for ICTServe v3.5.0
 *
 * Implements admin and superuser access control for Laravel Pulse
 * as per Requirement 36.6.
 *
 * @see D03 §8.2 - Laravel Pulse performance monitoring
 * @see Requirements 36.6 - Restrict /pulse route to admin and superuser roles
 */
class PulseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureAuthorization();
        $this->configureUserResolution();
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
        ]);
    }
}
