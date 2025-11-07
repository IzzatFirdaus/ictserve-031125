<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\PerformanceMonitoringService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use UnitEnum;

class PerformanceMonitoring extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Performance Monitoring';

    protected static UnitEnum|string|null $navigationGroup = 'System Configuration';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.performance-monitoring';

    public string $selectedPeriod = '24h';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->action('refreshData')
                ->color('primary'),

            Action::make('runHealthCheck')
                ->label('Run Health Check')
                ->action('runHealthCheck')
                ->color('warning'),
        ];
    }

    #[Computed]
    public function systemMetrics(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getSystemMetrics();
    }

    #[Computed]
    public function performanceTrends(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getPerformanceTrends($this->selectedPeriod);
    }

    #[Computed]
    public function performanceAlerts(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->checkPerformanceThresholds();
    }

    #[Computed]
    public function integrationHealth(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getIntegrationHealth();
    }

    #[Computed]
    public function slowQueries(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getSlowQueries(10);
    }

    public function refreshData(): void
    {
        // Clear cached data
        cache()->forget('system_metrics');
        cache()->forget("performance_trends_{$this->selectedPeriod}");

        $this->dispatch('$refresh');
    }

    public function runHealthCheck(): void
    {
        $service = app(PerformanceMonitoringService::class);
        $health = $service->getIntegrationHealth();

        $unhealthyServices = collect($health)
            ->filter(fn ($status) => $status['status'] !== 'healthy')
            ->keys()
            ->toArray();

        if (empty($unhealthyServices)) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'All services are healthy',
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Issues detected with: '.implode(', ', $unhealthyServices),
            ]);
        }
    }

    public function setPeriod(string $period): void
    {
        $this->selectedPeriod = $period;
    }

    public function getMetricColor(string $metric, $value): string
    {
        return match ($metric) {
            'response_time' => $value > 2000 ? 'danger' : ($value > 1000 ? 'warning' : 'success'),
            'database_query_time' => $value > 500 ? 'danger' : ($value > 200 ? 'warning' : 'success'),
            'cache_hit_rate' => $value < 80 ? 'danger' : ($value < 90 ? 'warning' : 'success'),
            'memory_usage' => $value > 85 ? 'danger' : ($value > 70 ? 'warning' : 'success'),
            'error_rate' => $value > 5 ? 'danger' : ($value > 1 ? 'warning' : 'success'),
            default => 'primary',
        };
    }

    public function formatMetricValue(string $metric, $value): string
    {
        return match ($metric) {
            'response_time', 'database_query_time', 'queue_processing_time' => number_format($value, 0).'ms',
            'cache_hit_rate', 'memory_usage', 'disk_usage' => number_format($value, 1).'%',
            'error_rate' => number_format($value * 100, 2).'%',
            'active_connections' => number_format($value),
            default => (string) $value,
        };
    }
}
