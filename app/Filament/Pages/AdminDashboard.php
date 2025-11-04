<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Filament\Widgets\LoanApprovalQueueWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketVolumeChart;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            HelpdeskStatsOverview::class,
            AssetUtilizationWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UnifiedAnalyticsChart::class,
            TicketVolumeChart::class,
            ResolutionTimeChart::class,
            LoanApprovalQueueWidget::class,
        ];
    }
}
