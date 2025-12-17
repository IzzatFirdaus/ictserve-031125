<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * Telescope Service Provider for ICTServe v3.6.0
 *
 * Implements superuser-only access control for Laravel Telescope
 * with custom watchers for ICTServe-specific operations.
 *
 * @see D00 §4.1 - Laravel Telescope debugging (superuser only)
 * @see D03 SRS-ADM-002 - Superuser Telescope access
 * @see Requirements 4.2, 12.1, 14.1, 17.1, 17.2, 17.3, 17.4, 17.5
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * ICTServe-specific tags for monitoring.
     */
    private const ICTSERVE_TAGS = [
        'helpdesk',
        'loan-approval',
        'asset-management',
        'email-delivery',
        'approval-workflow',
        'sla-tracking',
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();
        $this->configureFiltering();
        $this->configureTagging();
    }

    /**
     * Configure entry filtering based on environment and ICTServe requirements.
     *
     * Per Requirements 17.2: Set up comprehensive request monitoring and query analysis
     */
    protected function configureFiltering(): void
    {
        Telescope::filter(function (IncomingEntry $entry): bool {
            // In local environment, capture everything for development
            if ($this->app->environment('local')) {
                return true;
            }

            // Always capture critical entries
            if (
                $entry->isReportableException() ||
                $entry->isFailedRequest() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask()
            ) {
                return true;
            }

            // Capture entries with ICTServe-specific tags
            if ($entry->hasMonitoredTag()) {
                return true;
            }

            // Capture email-related entries for delivery tracking
            if ($entry->type === 'mail') {
                return true;
            }

            // Capture slow queries (>100ms) for performance analysis
            if ($entry->type === 'query' && isset($entry->content['slow']) && $entry->content['slow']) {
                return true;
            }

            // Capture notification entries for approval workflow debugging
            if ($entry->type === 'notification') {
                return true;
            }

            // Capture job entries for queue monitoring
            if ($entry->type === 'job') {
                return true;
            }

            return false;
        });
    }

    /**
     * Configure automatic tagging for ICTServe-specific operations.
     *
     * Per Requirements 17.4: Create custom debugging tools for ICTServe-specific operations
     */
    protected function configureTagging(): void
    {
        Telescope::tag(function (IncomingEntry $entry): array {
            $tags = [];

            // Tag helpdesk-related entries
            if ($this->isHelpdeskRelated($entry)) {
                $tags[] = 'helpdesk';
            }

            // Tag loan approval-related entries
            if ($this->isLoanApprovalRelated($entry)) {
                $tags[] = 'loan-approval';
            }

            // Tag asset management-related entries
            if ($this->isAssetManagementRelated($entry)) {
                $tags[] = 'asset-management';
            }

            // Tag email delivery entries
            if ($entry->type === 'mail') {
                $tags[] = 'email-delivery';

                // Check if it's an approval-related email
                if ($this->isApprovalEmail($entry)) {
                    $tags[] = 'approval-workflow';
                }
            }

            // Tag SLA-related entries
            if ($this->isSLARelated($entry)) {
                $tags[] = 'sla-tracking';
            }

            return $tags;
        });
    }

    /**
     * Check if entry is helpdesk-related.
     */
    protected function isHelpdeskRelated(IncomingEntry $entry): bool
    {
        $content = json_encode($entry->content);

        return str_contains($content, 'helpdesk') ||
            str_contains($content, 'HelpdeskTicket') ||
            str_contains($content, 'ticket');
    }

    /**
     * Check if entry is loan approval-related.
     */
    protected function isLoanApprovalRelated(IncomingEntry $entry): bool
    {
        $content = json_encode($entry->content);

        return str_contains($content, 'loan') ||
            str_contains($content, 'LoanApplication') ||
            str_contains($content, 'approval');
    }

    /**
     * Check if entry is asset management-related.
     */
    protected function isAssetManagementRelated(IncomingEntry $entry): bool
    {
        $content = json_encode($entry->content);

        return str_contains($content, 'asset') ||
            str_contains($content, 'Asset') ||
            str_contains($content, 'checkout') ||
            str_contains($content, 'checkin');
    }

    /**
     * Check if entry is an approval-related email.
     */
    protected function isApprovalEmail(IncomingEntry $entry): bool
    {
        if ($entry->type !== 'mail') {
            return false;
        }

        $content = json_encode($entry->content);

        return str_contains($content, 'Approval') ||
            str_contains($content, 'kelulusan') ||
            str_contains($content, 'approval_token');
    }

    /**
     * Check if entry is SLA-related.
     */
    protected function isSLARelated(IncomingEntry $entry): bool
    {
        $content = json_encode($entry->content);

        return str_contains($content, 'sla') ||
            str_contains($content, 'SLA') ||
            str_contains($content, 'breach') ||
            str_contains($content, 'escalat');
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * Per Requirements 12.1: Implement secure authentication
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'approval_token',
            'api_token',
        ]);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'authorization',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     * Per Requirements 17.1: Configure Laravel Telescope v5.x with superuser-only access
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
            // Per Requirements 17.1: Superuser-only access enforced through middleware
            return $user->isSuperuser();
        });
    }
}
