<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\AssetUtilizationWidget;
use App\Filament\Widgets\HelpdeskStatsOverview;
use App\Filament\Widgets\LoanApprovalQueueWidget;
use App\Filament\Widgets\ResolutionTimeChart;
use App\Filament\Widgets\TicketsByStatusChart;
use App\Filament\Widgets\TicketVolumeChart;
use App\Filament\Widgets\UnifiedAnalyticsChart;
use Filament\Pages\Dashboard as BaseDashboard;

class AdminDashboard extends BaseDashboard
{
    public function getColumns(): int
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HelpdeskStatsOverview::class,
            LoanApprovalQueueWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UnifiedAnalyticsChart::class,
            // Put the overall trend up top, then a 2-column row of supporting charts.
            TicketVolumeChart::class,
            ResolutionTimeChart::class,
            // Ticket breakdown and asset util chart should sit side-by-side on large screens
            TicketsByStatusChart::class,
            AssetUtilizationWidget::class,
        ];
    }
}
