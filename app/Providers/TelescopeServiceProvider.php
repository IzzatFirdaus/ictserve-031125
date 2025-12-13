<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * Telescope Service Provider for ICTServe v3.5.0
 *
 * Implements superuser-only access control for Laravel Telescope
 * as per Requirements 20.2 and 20.3.
 *
 * @see D00 §4.1 - Laravel Telescope debugging (superuser only)
 * @see D03 SRS-ADM-002 - Superuser Telescope access
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        // Filter entries based on environment
        Telescope::filter(function (IncomingEntry $entry): bool {
            if ($this->app->environment('local')) {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     * Per Requirements 20.2 and 20.3, only superuser role can access Telescope.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (?User $user): bool {
            // In local environment, allow access for development
            if (app()->environment('local')) {
                return true;
            }

            // Require authentication
            if ($user === null) {
                return false;
            }

            // Only superuser role can access Telescope
            // Per Requirements 20.2: Superuser accesses /telescope
            // Per Requirements 20.3: Return 403 for non-superuser access
            return $user->isSuperuser();
        });
    }
}
