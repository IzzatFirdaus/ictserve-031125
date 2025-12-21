<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\PerformanceMonitoringService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class SlowQueriesTableWidget extends Widget
{
    use WidgetMetadata;

    /**
     * Widget roles - restricted access
     */
    public static function getWidgetRoles(): array
    {
        return ['admin', 'superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D11 §12.1 Performance standards, D04 §3.2 Dashboard widgets';
    }

    protected string $view = 'filament.widgets.slow-queries-table';

    public function getQueries(): Collection
    {
        $service = app(PerformanceMonitoringService::class);

        return $service->getSlowQueries(10);
    }
}
