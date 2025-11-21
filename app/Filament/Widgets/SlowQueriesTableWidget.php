<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\PerformanceMonitoringService;
use Filament\Widgets\Widget;

class SlowQueriesTableWidget extends Widget
{
    protected string $view = 'filament.widgets.slow-queries-table';

    public function getQueries(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getSlowQueries(10);
    }
}
