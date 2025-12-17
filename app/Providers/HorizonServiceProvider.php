<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

/**
 * ICTServe Horizon Service Provider
 *
 * Configures Laravel Horizon for queue management and monitoring.
 * Access is restricted to admin and superuser roles per Requirement 23.1.
 *
 * @see docs/D17_QUEUE_MANAGEMENT_HORIZON.md
 */
class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Configure notification routing for queue issues
        // Requirement 23.5: Automated alerting for queue issues
        Horizon::routeMailNotificationsTo(config('horizon.notifications.email', 'admin@motac.gov.my'));

        // Configure Horizon night mode based on user preference
        Horizon::night();

        // Configure custom tags for ICTServe job filtering
        // Requirement 23.7: Job tagging for ICTServe operations
        $this->configureJobTags();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     * Access is restricted to admin and superuser roles per Requirement 23.1.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function (?User $user = null) {
            // Allow access in local environment for development
            if (app()->environment('local')) {
                return true;
            }

            // Require authentication
            if (! $user) {
                return false;
            }

            // Allow admin and superuser roles to access Horizon
            // Requirement 23.1: Dashboard accessible to admin and superuser roles
            return $user->hasRole(['admin', 'superuser']) ||
                in_array($user->role, ['admin', 'superuser']);
        });
    }

    /**
     * Configure custom job tags for ICTServe operations.
     *
     * Enables filtering by module (helpdesk, asset-loan, ai-chatbot)
     * and priority level in the Horizon dashboard.
     */
    protected function configureJobTags(): void
    {
        // Note: Job tagging will be implemented in individual job classes
        // using the tags() method as per Laravel Horizon best practices.
        // This ensures proper tagging without relying on global tag callbacks.

        // Individual job classes should implement:
        // public function tags(): array
        // {
        //     return ['module-name', 'priority-level', 'user:' . $this->userId];
        // }
    }
}
