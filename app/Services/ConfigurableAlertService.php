<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Configurable Alert Service
 *
 * Manages automated alerts for overdue returns, approval delays, and critical asset shortages.
 * Provides customizable thresholds and multiple notification channels.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 */
class ConfigurableAlertService
{
    private const CACHE_PREFIX = 'alert_config_';

    private const DEFAULT_THRESHOLDS = [
        'overdue_tickets_threshold' => 5,
        'overdue_loans_threshold' => 3,
        'approval_delay_hours' => 48,
        'critical_asset_shortage_percentage' => 10,
        'system_health_threshold' => 70,
        'response_time_threshold' => 300, // 5 minutes in seconds
    ];

    public function __construct(
        private UnifiedAnalyticsService $analyticsService,
        private NotificationService $notificationService
    ) {}

    /**
     * Check all configured alerts and trigger notifications
     */
    public function checkAllAlerts(): array
    {
        $results = [];
        $config = $this->getAlertConfiguration();

        if ($config['overdue_tickets_enabled']) {
            $results['overdue_tickets'] = $this->checkOverdueTickets();
        }

        if ($config['overdue_loans_enabled']) {
            $results['overdue_loans'] = $this->checkOverdueLoans();
        }

        if ($config['approval_delays_enabled']) {
            $results['approval_delays'] = $this->checkApprovalDelays();
        }

        if ($config['asset_shortages_enabled']) {
            $results['asset_shortages'] = $this->checkAssetShortages();
        }

        if ($config['system_health_enabled']) {
            $results['system_health'] = $this->checkSystemHealth();
        }

        return $results;
    }

    /**
     * Check for overdue tickets and send alerts
     */
    public function checkOverdueTickets(): array
    {
        $config = $this->getAlertConfiguration();
        $threshold = $config['overdue_tickets_threshold'];

        $metrics = $this->analyticsService->getDashboardMetrics();
        $overdueCount = $metrics['helpdesk']['overdue_tickets'];

        if ($overdueCount >= $threshold) {
            $alertData = [
                'type' => 'overdue_tickets',
                'severity' => $this->calculateSeverity($overdueCount, $threshold),
                'count' => $overdueCount,
                'threshold' => $threshold,
                'message' => "Terdapat {$overdueCount} tiket tertunggak yang melebihi had {$threshold}",
                'details' => $this->analyticsService->getDrillDownData('overdue_tickets'),
            ];

            $this->sendAlert($alertData);

            return [
                'triggered' => true,
                'alert_data' => $alertData,
            ];
        }

        return ['triggered' => false];
    }

    /**
     * Check for overdue loans and send alerts
     */
    public function checkOverdueLoans(): array
    {
        $config = $this->getAlertConfiguration();
        $threshold = $config['overdue_loans_threshold'];

        $metrics = $this->analyticsService->getDashboardMetrics();
        $overdueCount = $metrics['loans']['overdue_loans'];

        if ($overdueCount >= $threshold) {
            $alertData = [
                'type' => 'overdue_loans',
                'severity' => $this->calculateSeverity($overdueCount, $threshold),
                'count' => $overdueCount,
                'threshold' => $threshold,
                'message' => "Terdapat {$overdueCount} pinjaman tertunggak yang melebihi had {$threshold}",
                'details' => $this->analyticsService->getDrillDownData('overdue_loans'),
            ];

            $this->sendAlert($alertData);

            return [
                'triggered' => true,
                'alert_data' => $alertData,
            ];
        }

        return ['triggered' => false];
    }

    /**
     * Check for approval delays and send alerts
     */
    public function checkApprovalDelays(): array
    {
        $config = $this->getAlertConfiguration();
        $thresholdHours = $config['approval_delay_hours'];

        // Check for loan applications pending approval beyond threshold
        $delayedApprovals = \App\Models\LoanApplication::where('status', 'under_review')
            ->where('created_at', '<', now()->subHours($thresholdHours))
            ->count();

        if ($delayedApprovals > 0) {
            $alertData = [
                'type' => 'approval_delays',
                'severity' => $this->calculateSeverity($delayedApprovals, 1),
                'count' => $delayedApprovals,
                'threshold_hours' => $thresholdHours,
                'message' => "Terdapat {$delayedApprovals} permohonan yang menunggu kelulusan melebihi {$thresholdHours} jam",
                'details' => $this->getDelayedApprovalsDetails($thresholdHours),
            ];

            $this->sendAlert($alertData);

            return [
                'triggered' => true,
                'alert_data' => $alertData,
            ];
        }

        return ['triggered' => false];
    }

    /**
     * Check for critical asset shortages and send alerts
     */
    public function checkAssetShortages(): array
    {
        $config = $this->getAlertConfiguration();
        $thresholdPercentage = $config['critical_asset_shortage_percentage'];

        $metrics = $this->analyticsService->getDashboardMetrics();
        $availabilityRate = $metrics['assets']['availability_rate'];
        $totalAssets = $metrics['assets']['total_assets'];

        // Only trigger if there are assets to check
        if ($totalAssets > 0 && $availabilityRate <= $thresholdPercentage) {
            $alertData = [
                'type' => 'asset_shortages',
                'severity' => 'critical',
                'availability_rate' => $availabilityRate,
                'threshold' => $thresholdPercentage,
                'message' => "Kadar ketersediaan aset kritikal: {$availabilityRate}% (had: {$thresholdPercentage}%)",
                'details' => $this->getAssetShortageDetails(),
            ];

            $this->sendAlert($alertData);

            return [
                'triggered' => true,
                'alert_data' => $alertData,
            ];
        }

        return ['triggered' => false];
    }

    /**
     * Check system health and send alerts
     */
    public function checkSystemHealth(): array
    {
        $config = $this->getAlertConfiguration();
        $threshold = $config['system_health_threshold'];

        $metrics = $this->analyticsService->getDashboardMetrics();
        $healthScore = $metrics['summary']['overall_system_health'];

        // Check if there's meaningful data (at least some tickets or loans exist)
        $hasData = $metrics['helpdesk']['total_tickets'] > 0 || $metrics['loans']['total_applications'] > 0;

        if ($hasData && $healthScore <= $threshold) {
            $alertData = [
                'type' => 'system_health',
                'severity' => $this->calculateHealthSeverity($healthScore),
                'health_score' => $healthScore,
                'threshold' => $threshold,
                'message' => "Skor kesihatan sistem rendah: {$healthScore}% (had: {$threshold}%)",
                'details' => $this->getSystemHealthDetails($metrics),
            ];

            $this->sendAlert($alertData);

            return [
                'triggered' => true,
                'alert_data' => $alertData,
            ];
        }

        return ['triggered' => false];
    }

    /**
     * Send alert through configured channels
     */
    private function sendAlert(array $alertData): void
    {
        $config = $this->getAlertConfiguration();
        $recipients = $this->getAlertRecipients($alertData['type']);

        // Prevent spam - check if similar alert was sent recently
        $cacheKey = "alert_sent_{$alertData['type']}_".md5(json_encode($alertData));
        if (Cache::has($cacheKey)) {
            return;
        }

        // Email notifications
        if ($config['email_notifications_enabled']) {
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(
                        new \App\Mail\SystemAlertMail($alertData)
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send alert email to {$recipient->email}", [
                        'error' => $e->getMessage(),
                        'alert_data' => $alertData,
                    ]);
                }
            }
        }

        // Admin panel notifications
        if ($config['admin_panel_notifications_enabled']) {
            foreach ($recipients as $recipient) {
                \Filament\Notifications\Notification::make()
                    ->title($this->getAlertTitle($alertData))
                    ->body($alertData['message'])
                    ->icon($this->getAlertIcon($alertData['type']))
                    ->color($this->getAlertColor($alertData['severity']))
                    ->sendToDatabase($recipient);
            }
        }

        // Cache to prevent spam (cache for 1 hour)
        Cache::put($cacheKey, true, now()->addHour());

        // Log alert
        Log::info('System alert triggered', [
            'type' => $alertData['type'],
            'severity' => $alertData['severity'],
            'recipients_count' => $recipients->count(),
        ]);
    }

    /**
     * Get alert configuration with defaults
     */
    public function getAlertConfiguration(): array
    {
        return Cache::remember(self::CACHE_PREFIX.'config', 3600, function () {
            // In a real implementation, this would come from a database table
            return array_merge(self::DEFAULT_THRESHOLDS, [
                'overdue_tickets_enabled' => true,
                'overdue_loans_enabled' => true,
                'approval_delays_enabled' => true,
                'asset_shortages_enabled' => true,
                'system_health_enabled' => true,
                'email_notifications_enabled' => true,
                'admin_panel_notifications_enabled' => true,
                'alert_frequency' => 'hourly', // hourly, daily, immediate
            ]);
        });
    }

    /**
     * Update alert configuration
     */
    public function updateAlertConfiguration(array $config): void
    {
        // Merge with existing configuration to preserve unmodified values
        $currentConfig = $this->getAlertConfiguration();
        $mergedConfig = array_merge($currentConfig, $config);

        // Clear cache first to ensure fresh data
        Cache::forget(self::CACHE_PREFIX.'config');

        // Store updated configuration
        Cache::put(self::CACHE_PREFIX.'config', $mergedConfig, now()->addDay());

        // In a real implementation, save to database
        Log::info('Alert configuration updated', ['config' => $mergedConfig]);
    }

    /**
     * Send test alert to verify system functionality
     */
    public function sendTestAlert(): void
    {
        $testAlert = [
            'type' => 'system_test',
            'severity' => 'medium',
            'message' => 'Ini adalah ujian sistem amaran ICTServe. Sistem berfungsi dengan normal.',
            'count' => 1,
            'threshold' => 1,
            'details' => [
                [
                    'test_item' => 'Ujian Sistem Amaran',
                    'status' => 'Berjaya',
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                    'test_id' => 'TEST-'.now()->format('YmdHis'),
                ],
            ],
        ];

        $this->sendAlert($testAlert);
    }

    /**
     * Get recipients for specific alert type
     */
    private function getAlertRecipients(string $alertType): Collection
    {
        $roleMap = [
            'overdue_tickets' => ['admin', 'superuser'],
            'overdue_loans' => ['admin', 'superuser', 'approver'],
            'approval_delays' => ['admin', 'superuser', 'approver'],
            'asset_shortages' => ['admin', 'superuser'],
            'system_health' => ['superuser'],
            'system_test' => ['admin', 'superuser'], // Test alerts go to admins
        ];

        $roles = $roleMap[$alertType] ?? ['admin', 'superuser'];

        return User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })
            ->where('is_active', true)
            ->whereNotNull('email')
            ->get();
    }

    /**
     * Calculate alert severity based on threshold breach
     */
    private function calculateSeverity(int $actual, int $threshold): string
    {
        $ratio = $actual / $threshold;

        return match (true) {
            $ratio >= 3 => 'critical',
            $ratio >= 2 => 'high',
            $ratio >= 1.5 => 'medium',
            default => 'low',
        };
    }

    /**
     * Calculate health-based severity
     */
    private function calculateHealthSeverity(float $healthScore): string
    {
        return match (true) {
            $healthScore <= 50 => 'critical',
            $healthScore <= 60 => 'high',
            $healthScore <= 70 => 'medium',
            default => 'low',
        };
    }

    /**
     * Get alert title based on type
     */
    private function getAlertTitle(array $alertData): string
    {
        return match ($alertData['type']) {
            'overdue_tickets' => 'Amaran: Tiket Tertunggak',
            'overdue_loans' => 'Amaran: Pinjaman Tertunggak',
            'approval_delays' => 'Amaran: Kelewatan Kelulusan',
            'asset_shortages' => 'Amaran Kritikal: Kekurangan Aset',
            'system_health' => 'Amaran: Kesihatan Sistem Rendah',
            'system_test' => 'Ujian: Sistem Amaran',
            default => 'Amaran Sistem ICTServe',
        };
    }

    /**
     * Get alert icon based on type
     */
    private function getAlertIcon(string $type): string
    {
        return match ($type) {
            'overdue_tickets' => 'heroicon-o-exclamation-triangle',
            'overdue_loans' => 'heroicon-o-clock',
            'approval_delays' => 'heroicon-o-pause-circle',
            'asset_shortages' => 'heroicon-o-x-circle',
            'system_health' => 'heroicon-o-heart',
            'system_test' => 'heroicon-o-beaker',
            default => 'heroicon-o-bell',
        };
    }

    /**
     * Get alert color based on severity
     */
    private function getAlertColor(string $severity): string
    {
        return match ($severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'gray',
        };
    }

    /**
     * Get URL for alert details
     */
    private function getAlertUrl(string $type): string
    {
        return match ($type) {
            'overdue_tickets' => route('filament.admin.resources.helpdesk-tickets.index', ['tableFilters[status][value]' => 'overdue']),
            'overdue_loans' => route('filament.admin.resources.loan-applications.index', ['tableFilters[status][value]' => 'overdue']),
            'approval_delays' => route('filament.admin.resources.loan-applications.index', ['tableFilters[status][value]' => 'under_review']),
            'asset_shortages' => route('filament.admin.resources.assets.index', ['tableFilters[status][value]' => 'available']),
            'system_health' => route('filament.admin.pages.unified-analytics-dashboard'),
            'system_test' => route('filament.admin.pages.alert-configuration'),
            default => route('filament.admin.pages.unified-analytics-dashboard'),
        };
    }

    /**
     * Get detailed information for delayed approvals
     */
    private function getDelayedApprovalsDetails(int $thresholdHours): array
    {
        return \App\Models\LoanApplication::where('status', 'under_review')
            ->where('created_at', '<', now()->subHours($thresholdHours))
            ->with(['loanItems.asset'])
            ->get()
            ->map(function ($loan) {
                return [
                    'application_number' => $loan->application_number,
                    'applicant_name' => $loan->applicant_name,
                    'created_at' => $loan->created_at->format('Y-m-d H:i'),
                    'hours_pending' => $loan->created_at->diffInHours(now()),
                    'total_value' => $loan->total_value,
                    'asset_count' => $loan->loanItems->count(),
                ];
            })
            ->toArray();
    }

    /**
     * Get asset shortage details
     */
    private function getAssetShortageDetails(): array
    {
        return \App\Models\Asset::selectRaw('
                category_id,
                COUNT(*) as total,
                SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available,
                ROUND((SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as availability_rate
            ')
            ->with('category')
            ->groupBy('category_id')
            ->havingRaw('availability_rate <= ?', [20]) // Categories with less than 20% availability
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category?->name ?? 'Uncategorized',
                    'total_assets' => $item->total,
                    'available_assets' => $item->available,
                    'availability_rate' => $item->availability_rate,
                ];
            })
            ->toArray();
    }

    /**
     * Get system health details
     */
    private function getSystemHealthDetails(array $metrics): array
    {
        return [
            'helpdesk_issues' => [
                'overdue_tickets' => $metrics['helpdesk']['overdue_tickets'],
                'resolution_rate' => $metrics['helpdesk']['resolution_rate'],
            ],
            'loan_issues' => [
                'overdue_loans' => $metrics['loans']['overdue_loans'],
                'approval_rate' => $metrics['loans']['approval_rate'],
            ],
            'asset_issues' => [
                'maintenance_assets' => $metrics['assets']['maintenance_assets'],
                'utilization_rate' => $metrics['assets']['utilization_rate'],
            ],
        ];
    }
}
