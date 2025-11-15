<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\SecurityMonitoringService;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

/**
 * Security Monitoring Page
 *
 * Real-time security monitoring dashboard for superusers.
 * Displays security events, failed logins, suspicious activities,
 * and critical alerts with 60-second auto-refresh.
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-010 (Security Monitoring), D11 §8 (Security)
 * Traceability: Phase 9.2 - Security Monitoring Page
 * WCAG 2.2 AA: Full keyboard navigation, ARIA labels, 4.5:1 contrast
 * Bilingual: MS (primary), EN (secondary)
 */
class SecurityMonitoring extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected string $view = 'filament.pages.security-monitoring';

    protected static string|\UnitEnum|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 11;

    /**
     * Polling interval in seconds
     */
    protected static string $pollingInterval = '60s';

    /**
     * Security monitoring service
     */
    protected SecurityMonitoringService $securityService;

    /**
     * Dashboard statistics
     */
    public array $stats = [];

    /**
     * Recent security events
     */
    public array $recentEvents = [];

    /**
     * Failed login attempts
     */
    public array $failedLogins = [];

    /**
     * Security alerts
     */
    public array $alerts = [];

    /**
     * Blocked IPs
     */
    public array $blockedIPs = [];

    /**
     * Boot the page
     */
    public function boot(): void
    {
        $this->securityService = app(SecurityMonitoringService::class);
    }

    /**
     * Mount the page
     */
    public function mount(): void
    {
        $this->loadData();
    }

    /**
     * Get the navigation label
     */
    public static function getNavigationLabel(): string
    {
        return __('Security Monitoring');
    }

    /**
     * Get the page title
     */
    public function getTitle(): string
    {
        return __('Security Monitoring');
    }

    /**
     * Get the page heading
     */
    public function getHeading(): string
    {
        return __('Security Monitoring Dashboard');
    }

    /**
     * Get the page subheading
     */
    public function getSubheading(): ?string
    {
        return __('Real-time security event monitoring and incident management');
    }

    /**
     * Determine if the page should be registered in navigation
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    /**
     * Determine if the page can be accessed
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    /**
     * Load dashboard data
     */
    public function loadData(): void
    {
        $this->stats = $this->securityService->getDashboardStats();
        $this->recentEvents = $this->securityService->getRecentSecurityEvents(20)->toArray();
        $this->failedLogins = $this->securityService->getFailedLoginAttempts(20)->toArray();
        $this->alerts = $this->securityService->getAlerts(unacknowledgedOnly: true);
        $this->blockedIPs = $this->securityService->getBlockedIPs();
    }

    /**
     * Refresh dashboard data
     */
    public function refresh(): void
    {
        $this->loadData();

        $this->dispatch('$refresh');
    }

    /**
     * Acknowledge alert
     */
    public function acknowledgeAlert(string $alertId): void
    {
        $this->securityService->acknowledgeAlert($alertId);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title(__('Alert Acknowledged'))
            ->success()
            ->send();
    }

    /**
     * Unblock IP address
     */
    public function unblockIP(string $ipAddress): void
    {
        $this->securityService->unblockIP($ipAddress);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title(__('IP Unblocked'))
            ->body(__('IP address :ip has been unblocked.', ['ip' => $ipAddress]))
            ->success()
            ->send();
    }

    /**
     * Clear old alerts
     */
    public function clearOldAlerts(): void
    {
        $count = $this->securityService->clearOldAlerts(24);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title(__('Alerts Cleared'))
            ->body(__(':count old alerts have been cleared.', ['count' => $count]))
            ->success()
            ->send();
    }

    /**
     * Get header actions
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('Refresh'))
                ->icon(Heroicon::OutlinedArrowPath->value)
                ->color(Color::Gray)
                ->action('refresh'),

            Action::make('clearOldAlerts')
                ->label(__('Clear Old Alerts'))
                ->icon(Heroicon::OutlinedTrash->value)
                ->color(Color::Gray)
                ->action('clearOldAlerts')
                ->requiresConfirmation()
                ->modalHeading(__('Clear Old Alerts'))
                ->modalDescription(__('This will clear all acknowledged alerts older than 24 hours.'))
                ->modalSubmitActionLabel(__('Clear')),
        ];
    }

    /**
     * Get polling interval
     */
    public function getPollingInterval(): ?string
    {
        return self::$pollingInterval;
    }
}
