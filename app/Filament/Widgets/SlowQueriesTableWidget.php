<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\PerformanceMonitoringService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class SlowQueriesTableWidget extends Widget
{
    protected string $view = 'filament.widgets.slow-queries-table';

    public function getQueries(): Collection
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getSlowQueries(10);
    }
}
