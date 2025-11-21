<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\PerformanceMonitoringService;
use Filament\Widgets\Widget;

class HealthCheckTableWidget extends Widget
{
    protected string $view = 'filament.widgets.health-check-table';

    public function getHealth(): array
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getIntegrationHealth();
    }
}
