<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PerformanceOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Performance Monitoring Command
 *
 * Monitors and reports on system performance metrics:
 * - Database query performance
 * - Cache effectiveness
 * - Memory usage
 * - Response times
 *
 * @trace D07 System Integration Plan - Performance Monitoring
 * @trace D11 Technical Design - Performance Standards
 *
 * @requirements 7.1, 7.2, 14.1
 */
class PerformanceMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:monitor
                            {--clear-cache : Clear all performance caches}
                            {--warm-cache : Warm up critical caches}
                            {--report : Generate detailed performance report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor and optimize system performance';

    /**
     * Performance optimization service
     */
    private PerformanceOptimizationService $performanceService;

    /**
     * Create a new command instance.
     */
    public function __construct(PerformanceOptimizationService $performanceService)
    {
        parent::__construct();
        $this->performanceService = $performanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('ICTServe Performance Monitor');
        $this->info('============================');
        $this->newLine();

        // Handle cache operations
        if ($this->option('clear-cache')) {
            return $this->clearCaches();
        }

        if ($this->option('warm-cache')) {
            return $this->warmCaches();
        }

        if ($this->option('report')) {
            return $this->generateReport();
        }

        // Default: Show current metrics
        return $this->showMetrics();
    }

    /**
     * Show current performance metrics
     */
    private function showMetrics(): int
    {
        $this->info('Current Performance Metrics:');
        $this->newLine();

        // Get metrics
        $metrics = $this->performanceService->getPerformanceMetrics();

        // Display metrics
        $this->table(
            ['Metric', 'Value', 'Status'],
            [
                ['Cache Hit Rate', number_format($metrics['cache_hit_rate'], 1).'%', $this->getStatus($metrics['cache_hit_rate'], 80, 90)],
                ['Avg Query Time', number_format($metrics['average_query_time'], 2).' ms', $this->getStatus(100 - $metrics['average_query_time'], 50, 80)],
                ['Slow Queries', $metrics['slow_queries_count'], $this->getStatus(10 - $metrics['slow_queries_count'], 5, 8)],
                ['Memory Usage', $this->formatBytes($metrics['memory_usage']), '✓'],
                ['Peak Memory', $this->formatBytes($metrics['peak_memory_usage']), '✓'],
            ]
        );

        $this->newLine();
        $this->info('Run with --report for detailed analysis');

        return Command::SUCCESS;
    }

    /**
     * Clear all performance caches
     */
    private function clearCaches(): int
    {
        $this->info('Clearing performance caches...');

        $this->performanceService->clearAllCaches();

        $this->info('✓ All performance caches cleared');

        return Command::SUCCESS;
    }

    /**
     * Warm up critical caches
     */
    private function warmCaches(): int
    {
        $this->info('Warming up critical caches...');

        $this->performanceService->warmUpCaches();

        $this->info('✓ Critical caches warmed up');

        return Command::SUCCESS;
    }

    /**
     * Generate detailed performance report
     */
    private function generateReport(): int
    {
        $this->info('Generating Performance Report...');
        $this->newLine();

        // Database Performance
        $this->info('📊 Database Performance:');
        DB::enableQueryLog();

        // Run sample queries
        DB::table('users')->count();
        DB::table('helpdesk_tickets')->count();
        DB::table('loan_applications')->count();

        $queries = DB::getQueryLog();
        $avgTime = count($queries) > 0 ? array_sum(array_column($queries, 'time')) / count($queries) : 0;

        $this->line('  Total Queries: '.count($queries));
        $this->line('  Average Time: '.number_format($avgTime, 2).' ms');
        $this->line('  Slow Queries: '.count(array_filter($queries, fn ($q) => $q['time'] > 1000)));
        $this->newLine();

        // Cache Performance
        $this->info('💾 Cache Performance:');
        $cacheDriver = config('cache.default');
        $this->line("  Driver: {$cacheDriver}");
        $this->line('  Status: '.(Cache::has('test_key') ? 'Connected' : 'Not Connected'));
        $this->newLine();

        // Memory Usage
        $this->info('🧠 Memory Usage:');
        $this->line('  Current: '.$this->formatBytes(memory_get_usage(true)));
        $this->line('  Peak: '.$this->formatBytes(memory_get_peak_usage(true)));
        $this->line('  Limit: '.ini_get('memory_limit'));
        $this->newLine();

        // Core Web Vitals Targets
        $this->info('🎯 Core Web Vitals Targets:');
        $this->table(
            ['Metric', 'Target', 'Status'],
            [
                ['LCP (Largest Contentful Paint)', '< 2.5s', '⏱️  Run E2E tests'],
                ['FID (First Input Delay)', '< 100ms', '⏱️  Run E2E tests'],
                ['CLS (Cumulative Layout Shift)', '< 0.1', '⏱️  Run E2E tests'],
                ['TTFB (Time to First Byte)', '< 600ms', '⏱️  Run E2E tests'],
            ]
        );
        $this->newLine();

        // Recommendations
        $this->info('💡 Recommendations:');
        $recommendations = $this->getRecommendations($avgTime, count($queries));

        foreach ($recommendations as $recommendation) {
            $this->line("  • {$recommendation}");
        }
        $this->newLine();

        // Save report
        $reportPath = storage_path('logs/performance-report-'.date('Y-m-d-His').'.txt');
        $reportContent = $this->generateReportContent($queries, $avgTime);
        file_put_contents($reportPath, $reportContent);

        $this->info("📄 Report saved to: {$reportPath}");

        return Command::SUCCESS;
    }

    /**
     * Get performance recommendations
     */
    private function getRecommendations(float $avgQueryTime, int $queryCount): array
    {
        $recommendations = [];

        if ($avgQueryTime > 100) {
            $recommendations[] = 'Consider adding database indexes for frequently queried columns';
            $recommendations[] = 'Review and optimize slow queries using EXPLAIN';
        }

        if ($queryCount > 50) {
            $recommendations[] = 'Implement eager loading to reduce N+1 query problems';
            $recommendations[] = 'Consider caching frequently accessed data';
        }

        $cacheDriver = config('cache.default');
        if ($cacheDriver === 'file') {
            $recommendations[] = 'Consider using Redis for better cache performance';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Performance looks good! Continue monitoring.';
        }

        return $recommendations;
    }

    /**
     * Generate report content
     */
    private function generateReportContent(array $queries, float $avgTime): string
    {
        $content = "ICTServe Performance Report\n";
        $content .= 'Generated: '.date('Y-m-d H:i:s')."\n";
        $content .= str_repeat('=', 50)."\n\n";

        $content .= "Database Performance:\n";
        $content .= '  Total Queries: '.count($queries)."\n";
        $content .= '  Average Time: '.number_format($avgTime, 2)." ms\n";
        $content .= '  Slow Queries: '.count(array_filter($queries, fn ($q) => $q['time'] > 1000))."\n\n";

        $content .= "Memory Usage:\n";
        $content .= '  Current: '.$this->formatBytes(memory_get_usage(true))."\n";
        $content .= '  Peak: '.$this->formatBytes(memory_get_peak_usage(true))."\n\n";

        $content .= "Recommendations:\n";
        foreach ($this->getRecommendations($avgTime, count($queries)) as $rec) {
            $content .= "  • {$rec}\n";
        }

        return $content;
    }

    /**
     * Get status indicator
     */
    private function getStatus(float $value, float $warning, float $good): string
    {
        if ($value >= $good) {
            return '✓ Good';
        } elseif ($value >= $warning) {
            return '⚠ Warning';
        } else {
            return '✗ Poor';
        }
    }

    /**
     * Format bytes to human-readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
