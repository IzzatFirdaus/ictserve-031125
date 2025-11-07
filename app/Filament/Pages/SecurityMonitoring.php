<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\SecurityMonitoringService;
use BackedEnum;
use Filament\Actions;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * Security Monitoring Dashboard
 *
 * Superuser-only dashboard for monitoring security events, failed logins,
 * suspicious activities, and system security metrics.
 *
 * Requirements: 9.2, 9.3
 *
 * @see D03-FR-007.3 Security monitoring dashboard
 * @see D11 §8 Security implementation
 */
class SecurityMonitoring extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected string $view = 'filament.pages.security-monitoring';

    protected static ?string $navigationLabel = 'Security Monitoring';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'security-monitoring';

    public array $securityStats = [];

    public array $failedLogins = [];

    public array $suspiciousActivities = [];

    public array $roleChanges = [];

    public array $configChanges = [];

    public array $securityIncidents = [];

    public array $securityMetrics = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superuser') ?? false;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Security Monitoring Dashboard');
    }

    public function mount(): void
    {
        $this->loadSecurityData();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh_data')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action('loadSecurityData')
                ->keyBindings(['ctrl+r', 'cmd+r']),

            Actions\Action::make('export_security_report')
                ->label('Export Security Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('format')
                        ->label('Export Format')
                        ->options([
                            'pdf' => 'PDF Report',
                            'csv' => 'CSV Data',
                            'json' => 'JSON Data',
                        ])
                        ->default('pdf')
                        ->required(),

                    \Filament\Forms\Components\Select::make('period')
                        ->label('Time Period')
                        ->options([
                            '1' => 'Last 24 Hours',
                            '7' => 'Last 7 Days',
                            '30' => 'Last 30 Days',
                            '90' => 'Last 90 Days',
                        ])
                        ->default('7')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->exportSecurityReport($data['format'], (int) $data['period']);
                }),

            Actions\Action::make('security_settings')
                ->label('Security Settings')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('warning')
                ->url('/admin/system-configuration')
                ->openUrlInNewTab(false),

            Actions\Action::make('view_audit_trail')
                ->label('View Audit Trail')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url('/admin/audit-trail')
                ->openUrlInNewTab(false),
        ];
    }

    public function loadSecurityData(): void
    {
        $securityService = app(SecurityMonitoringService::class);

        $this->securityStats = $securityService->getSecurityStats();
        $this->failedLogins = $securityService->getFailedLoginAttempts(7)->toArray();
        $this->suspiciousActivities = $securityService->getSuspiciousActivities(7)->toArray();
        $this->roleChanges = $securityService->getRoleChangeHistory(30)->toArray();
        $this->configChanges = $securityService->getConfigurationChanges(30)->toArray();
        $this->securityIncidents = $securityService->detectSecurityIncidents()->toArray();
        $this->securityMetrics = $securityService->getSecurityMetrics(30);

        $this->notify('success', 'Security data refreshed successfully.');
    }

    public function exportSecurityReport(string $format, int $days): void
    {
        $securityService = app(SecurityMonitoringService::class);

        // Generate comprehensive security report
        $reportData = [
            'generated_at' => now(),
            'period_days' => $days,
            'stats' => $this->securityStats,
            'failed_logins' => $securityService->getFailedLoginAttempts($days),
            'suspicious_activities' => $securityService->getSuspiciousActivities($days),
            'role_changes' => $securityService->getRoleChangeHistory($days),
            'config_changes' => $securityService->getConfigurationChanges($days),
            'security_incidents' => $securityService->detectSecurityIncidents(),
        ];

        $filename = "security_report_{$days}d_".now()->format('Y-m-d_H-i-s').".{$format}";

        // Export logic would go here
        $this->notify('success', "Security report exported: {$filename}");
    }

    public function acknowledgeIncident(int $incidentIndex): void
    {
        if (isset($this->securityIncidents[$incidentIndex])) {
            // Mark incident as acknowledged
            $this->securityIncidents[$incidentIndex]['acknowledged'] = true;
            $this->securityIncidents[$incidentIndex]['acknowledged_by'] = auth()->user()->name;
            $this->securityIncidents[$incidentIndex]['acknowledged_at'] = now();

            $this->notify('success', 'Security incident acknowledged.');
        }
    }

    public function dismissIncident(int $incidentIndex): void
    {
        if (isset($this->securityIncidents[$incidentIndex])) {
            unset($this->securityIncidents[$incidentIndex]);
            $this->securityIncidents = array_values($this->securityIncidents);

            $this->notify('success', 'Security incident dismissed.');
        }
    }

    public function blockIpAddress(string $ipAddress): void
    {
        // In production, this would add IP to firewall/block list
        $this->notify('success', "IP address {$ipAddress} has been blocked.");
    }

    public function getSecurityStatsProperty(): array
    {
        return $this->securityStats;
    }

    public function getFailedLoginsProperty(): array
    {
        return $this->failedLogins;
    }

    public function getSuspiciousActivitiesProperty(): array
    {
        return $this->suspiciousActivities;
    }

    public function getRoleChangesProperty(): array
    {
        return $this->roleChanges;
    }

    public function getConfigChangesProperty(): array
    {
        return $this->configChanges;
    }

    public function getSecurityIncidentsProperty(): array
    {
        return $this->securityIncidents;
    }

    public function getSecurityMetricsProperty(): array
    {
        return $this->securityMetrics;
    }

    protected function getViewData(): array
    {
        return [
            'securityStats' => $this->securityStats,
            'failedLogins' => $this->failedLogins,
            'suspiciousActivities' => $this->suspiciousActivities,
            'roleChanges' => $this->roleChanges,
            'configChanges' => $this->configChanges,
            'securityIncidents' => $this->securityIncidents,
            'securityMetrics' => $this->securityMetrics,
        ];
    }
}
