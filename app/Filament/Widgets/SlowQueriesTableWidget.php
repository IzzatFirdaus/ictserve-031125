<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\PerformanceMonitoringService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

/**
 * Slow Queries Table Widget (Widget Jadual Pertanyaan Perlahan)
 *
 * Displays database queries exceeding performance thresholds for optimization
 * analysis. Integrates with Laravel Pulse and performance monitoring to identify
 * N+1 queries, missing indexes, and inefficient query patterns.
 *
 * Features:
 * - Query execution time tracking
 * - N+1 query detection
 * - Missing index identification
 * - Query frequency analysis
 * - Performance improvement recommendations
 * - Integration with Laravel Pulse metrics
 *
 * @trace D11-§12.1 (Performance Standards and Monitoring)
 * @trace D04-§3.2 (Dashboard Widgets Architecture)
 * @trace D10-§8 (Performance Optimization Guidelines)
 * @trace D12-§7 (Performance Monitoring UI)
 *
 * @see \App\Services\PerformanceMonitoringService
 * @see \App\Filament\Traits\WidgetMetadata
 */
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
