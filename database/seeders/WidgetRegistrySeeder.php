<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\WidgetRegistry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Widget Registry Seeder
 *
 * Seeds the widget registry with all existing ICTServe dashboard widgets.
 * Automatically categorizes widgets and assigns appropriate metadata.
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetRegistrySeeder extends Seeder
{
    public function run(): void
    {
        $registry = app(WidgetRegistry::class);

        Log::info('Starting widget registry seeding...');

        // Header Widgets (Stats Overview)
        $this->registerHeaderWidgets($registry);

        // Content Widgets (Tables, Actions, etc.)
        $this->registerContentWidgets($registry);

        // Chart Widgets (Analytics, Graphs)
        $this->registerChartWidgets($registry);

        Log::info('Widget registry seeding completed successfully');
    }

    private function registerHeaderWidgets(WidgetRegistry $registry): void
    {
        $headerWidgets = [
            [
                'class' => \App\Filament\Widgets\UnifiedDashboardOverview::class,
                'config' => [
                    'category' => 'header',
                    'sort_order' => 1,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 300,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\HelpdeskStatsOverview::class,
                'config' => [
                    'category' => 'header',
                    'sort_order' => 2,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 180, // 3 minutes
                    'cache_ttl' => 300,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\AssetLoanStatsOverview::class,
                'config' => [
                    'category' => 'header',
                    'sort_order' => 3,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 300,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\SystemHealthWidget::class,
                'config' => [
                    'category' => 'header',
                    'sort_order' => 4,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 120, // 2 minutes
                    'cache_ttl' => 180,
                ],
            ],
        ];

        foreach ($headerWidgets as $widget) {
            $registry->register($widget['class'], $widget['config']);
        }
    }

    private function registerContentWidgets(WidgetRegistry $registry): void
    {
        $contentWidgets = [
            [
                'class' => \App\Filament\Widgets\CriticalAlertsWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 1,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 60, // 1 minute for critical alerts
                    'cache_ttl' => 120,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\RecentActivityFeedWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 2,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 30, // 30 seconds for activity feed
                    'cache_ttl' => 60,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\QuickActionsWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 3,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\LoanApprovalQueueWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 4,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 120, // 2 minutes
                    'cache_ttl' => 180,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\RecentTicketsTable::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 5,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 180, // 3 minutes
                    'cache_ttl' => 300,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\AssetAvailabilityCalendarWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 6,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\ThemeToggleWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 7,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 3600, // 1 hour (rarely changes)
                    'cache_ttl' => 7200,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\UserActivityWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 8,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\UserActivityStatsWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 9,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\DataRetentionAlertWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 10,
                    'roles' => ['superuser'],
                    'refresh_rate' => 3600, // 1 hour
                    'cache_ttl' => 7200,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\SensitiveAccessLogWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 11,
                    'roles' => ['superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
        ];

        foreach ($contentWidgets as $widget) {
            $registry->register($widget['class'], $widget['config']);
        }
    }

    private function registerChartWidgets(WidgetRegistry $registry): void
    {
        $chartWidgets = [
            [
                'class' => \App\Filament\Widgets\UnifiedAnalyticsChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 1,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\TicketsByStatusChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 2,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\TicketVolumeChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 3,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\ResolutionTimeChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 4,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\LoanAnalyticsWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 5,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\CrossModuleIntegrationChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 6,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 900, // 15 minutes
                    'cache_ttl' => 1800,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\EnhancedUnifiedAnalyticsChart::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 7,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\AssetUtilizationWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 8,
                    'roles' => ['staff', 'admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\PerformanceMetricsWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 9,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\SystemMetricsWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 10,
                    'roles' => ['superuser'],
                    'refresh_rate' => 180, // 3 minutes
                    'cache_ttl' => 300,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\EmailQueueStatsWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 11,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\EmailQueueTrendsWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 12,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 600, // 10 minutes
                    'cache_ttl' => 900,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\HorizonHealthWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 13,
                    'roles' => ['superuser'],
                    'refresh_rate' => 120, // 2 minutes
                    'cache_ttl' => 180,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\SlowQueriesTableWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 14,
                    'roles' => ['superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\HealthCheckTableWidget::class,
                'config' => [
                    'category' => 'charts',
                    'sort_order' => 15,
                    'roles' => ['superuser'],
                    'refresh_rate' => 180, // 3 minutes
                    'cache_ttl' => 300,
                ],
            ],
        ];

        foreach ($chartWidgets as $widget) {
            $registry->register($widget['class'], $widget['config']);
        }

        // Register remaining widgets that weren't categorized above
        $this->registerRemainingWidgets($registry);
    }

    private function registerRemainingWidgets(WidgetRegistry $registry): void
    {
        $remainingWidgets = [
            [
                'class' => \App\Filament\Widgets\EnhancedRealTimeDashboardWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 12,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 60, // 1 minute for real-time
                    'cache_ttl' => 120,
                ],
            ],
            [
                'class' => \App\Filament\Widgets\CrossModuleIntegrationWidget::class,
                'config' => [
                    'category' => 'content',
                    'sort_order' => 13,
                    'roles' => ['admin', 'superuser'],
                    'refresh_rate' => 300, // 5 minutes
                    'cache_ttl' => 600,
                ],
            ],
        ];

        foreach ($remainingWidgets as $widget) {
            $registry->register($widget['class'], $widget['config']);
        }
    }
}
