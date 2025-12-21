<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\HealthCheckTableWidget;
use App\Filament\Widgets\SlowQueriesTableWidget;
use App\Filament\Widgets\SystemMetricsWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PerformanceMonitoring extends Dashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    protected static string $routePath = 'performance-monitoring';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasRole('superuser') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.performance_monitoring.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system');
    }

    public function getWidgets(): array
    {
        return [
            SystemMetricsWidget::class,
            HealthCheckTableWidget::class,
            SlowQueriesTableWidget::class,
        ];
    }
}
