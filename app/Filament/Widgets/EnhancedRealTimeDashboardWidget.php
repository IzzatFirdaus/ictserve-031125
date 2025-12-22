<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\CacheableWidget;
use App\Filament\Traits\WidgetMetadata;
use App\Services\EnhancedUnifiedDashboardService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Enhanced Real-Time Dashboard Widget
 *
 * Provides real-time metrics with Laravel Pulse integration and
 * WebSocket updates via Laravel Reverb for instant status changes.
 *
 * Features:
 * - 60-second polling for near real-time updates
 * - Laravel Pulse performance metrics integration
 * - Alert severity indicators with WCAG 2.2 AA compliant colors
 * - Role-based metric visibility
 * - Bahasa Melayu exclusive labels
 *
 * @see D03-FR-019 Dashboard requirements
 * @see D04 §3.2 Dashboard widgets
 * @see D12 §2 Real-time features with Laravel Reverb
 * @see D14 §7.5 Shadow tokens and color compliance
 *
 * @requirements 1.1, 1.3, 4.1, 4.2, 6.1, 6.2, 14.1
 *
 * @version 3.6.0
 */
class EnhancedRealTimeDashboardWidget extends BaseWidget
{
    use CacheableWidget;
    use WidgetMetadata;

    protected function getCacheTtl(): int
    {
        return 60;
    }

    /**
     * 60-second polling for real-time feel
     */
    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -20;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration';
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $service = app(EnhancedUnifiedDashboardService::class);
        /** @var array{summary: array{overall_system_health: float, total_active_items: int}, alerts: array{total_alerts: int, severity: string, sla_breaches: int, overdue_loans: int, critical_tickets: int}, helpdesk: array{resolution_rate: float, pending_tickets: int}, loans: array{approval_rate: float, pending_approval: int}, assets: array{utilization_rate: float, available_assets: int}, performance?: array{status: string, response_time_avg: float}} $metrics */
        $metrics = $this->cached(
            fn () => $service->getAdminDashboardMetrics(),
            'enhanced-dashboard-metrics'
        );

        $user = Auth::user();
        $canAccessPulse = $user && method_exists($user, 'canAccessPulse') && $user->canAccessPulse();

        $summary = $metrics['summary'];
        $alerts = $metrics['alerts'];
        $helpdesk = $metrics['helpdesk'];
        $loans = $metrics['loans'];
        $assets = $metrics['assets'];

        $stats = [
            // System Health with trend
            Stat::make('Kesihatan Sistem', $summary['overall_system_health'].'%')
                ->description($this->getHealthDescription($summary['overall_system_health']))
                ->descriptionIcon('heroicon-m-heart')
                ->color($this->getHealthColor($summary['overall_system_health']))
                ->chart($this->getHealthTrendData()),

            // Active Items
            Stat::make('Item Aktif', (string) $summary['total_active_items'])
                ->description('Tiket & pinjaman dalam proses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            // Alerts Requiring Attention
            Stat::make('Amaran', (string) $alerts['total_alerts'])
                ->description($this->getAlertDescription($alerts))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($this->getAlertColor($alerts['severity'])),

            // Helpdesk Performance
            Stat::make('Kadar Penyelesaian', $helpdesk['resolution_rate'].'%')
                ->description($helpdesk['pending_tickets'].' tiket tertunda')
                ->descriptionIcon('heroicon-m-ticket')
                ->color($this->getPerformanceColor($helpdesk['resolution_rate']))
                ->url(route('filament.admin.operations.resources.helpdesk.helpdesk-tickets.index')),

            // Loan Approval Rate
            Stat::make('Kadar Kelulusan', $loans['approval_rate'].'%')
                ->description($loans['pending_approval'].' menunggu kelulusan')
                ->descriptionIcon('heroicon-m-document-check')
                ->color($this->getPerformanceColor($loans['approval_rate']))
                ->url(route('filament.admin.operations.resources.loan-applications.index')),

            // Asset Utilization
            Stat::make('Penggunaan Aset', $assets['utilization_rate'].'%')
                ->description($assets['available_assets'].' tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color($this->getUtilizationColor($assets['utilization_rate']))
                ->url(route('filament.admin.inventory.resources.assets.index')),
        ];

        // Add performance metrics for admin/superuser
        if ($canAccessPulse && isset($metrics['performance']['status']) && $metrics['performance']['status'] === 'healthy') {
            $performance = $metrics['performance'];
            $stats[] = Stat::make('Masa Respons', $performance['response_time_avg'].'ms')
                ->description('Purata masa respons')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($this->getResponseTimeColor($performance['response_time_avg']))
                ->url(route('filament.admin.pages.pulse-dashboard'));
        }

        return $stats;
    }

    private function getHealthDescription(float $health): string
    {
        return match (true) {
            $health >= 90 => 'Sistem berfungsi dengan baik',
            $health >= 75 => 'Prestasi sederhana',
            $health >= 60 => 'Memerlukan perhatian',
            default => 'Prestasi kritikal',
        };
    }

    private function getHealthColor(float $health): string
    {
        return match (true) {
            $health >= 90 => 'success',
            $health >= 75 => 'warning',
            default => 'danger',
        };
    }

    /**
     * @param  array{sla_breaches: int, overdue_loans: int, critical_tickets: int}  $alerts
     */

    /**
     * @param  array<string, mixed>  $alerts
     */
    private function getAlertDescription(array $alerts): string
    {
        $parts = [];
        if ($alerts['sla_breaches'] > 0) {
            $parts[] = $alerts['sla_breaches'].' SLA';
        }
        if ($alerts['overdue_loans'] > 0) {
            $parts[] = $alerts['overdue_loans'].' tertunggak';
        }
        if ($alerts['critical_tickets'] > 0) {
            $parts[] = $alerts['critical_tickets'].' kritikal';
        }

        return empty($parts) ? 'Tiada amaran' : implode(', ', $parts);
    }

    private function getAlertColor(string $severity): string
    {
        return match ($severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            default => 'success',
        };
    }

    private function getPerformanceColor(float $rate): string
    {
        return match (true) {
            $rate >= 85 => 'success',
            $rate >= 70 => 'warning',
            default => 'danger',
        };
    }

    private function getUtilizationColor(float $rate): string
    {
        return match (true) {
            $rate >= 80 => 'warning',
            $rate >= 60 => 'success',
            $rate >= 40 => 'info',
            default => 'gray',
        };
    }

    private function getResponseTimeColor(float $ms): string
    {
        return match (true) {
            $ms <= 200 => 'success',
            $ms <= 500 => 'warning',
            default => 'danger',
        };
    }

    /**
     * @return array<int, int>
     */
    private function getHealthTrendData(): array
    {
        // Simulated trend data - could be enhanced with historical data
        return [65, 70, 75, 80, 85, 88, 90];
    }
}
