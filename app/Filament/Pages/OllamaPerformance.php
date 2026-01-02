<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Clusters\OllamaAI;
use App\Models\MessageLog;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Cache\RedisStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Halaman Papan Pemuka Prestasi Ollama AI
 *
 * Memaparkan metrik prestasi dan kesihatan sistem AI:
 * - Widget masa respons (P50, P95, P99)
 * - Widget kesihatan sistem (uptime, status pelayan)
 * - Widget prestasi cache (kadar hit, saiz)
 * - Widget prestasi pangkalan data (masa query, query perlahan)
 * - Widget penggunaan sumber (CPU/memori)
 * - Widget statistik operasi AI
 *
 * Selaras dengan D11 v3.6.0: Laravel Pulse integration
 * Selaras dengan D15 v3.6.0: Bahasa Melayu sahaja
 * Pematuhan WCAG 2.2 AA untuk carta dan jadual data
 *
 * @trace Requirements 8.7
 */
class OllamaPerformance extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.ollama-performance';

    protected static ?string $cluster = OllamaAI::class;

    protected static ?int $navigationSort = 21;

    public static function getNavigationLabel(): string
    {
        return __('ollama.performance.navigation_label');
    }

    public function getTitle(): string
    {
        return __('ollama.performance.page_title');
    }

    public function getSubheading(): ?string
    {
        return __('ollama.performance.page_description');
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Get performance statistics
     *
     * @return array<string, mixed>
     */

    /**
     * @return array<string, mixed>
     */
    public function getPerformanceStats(): array
    {
        return Cache::remember('ollama_performance_stats', 60, function () {
            $stats = [];

            // Response time metrics from message logs
            $responseTimes = MessageLog::query()
                ->whereNotNull('metadata->response_time_ms')
                ->orderBy('processed_at', 'desc')
                ->limit(1000)
                ->pluck('metadata')
                ->map(fn ($m) => $m['response_time_ms'] ?? 0)
                ->filter(fn ($t) => $t > 0)
                ->sort()
                ->values();

            if ($responseTimes->isNotEmpty()) {
                $count = $responseTimes->count();
                $stats['response_time_p50'] = $responseTimes->get((int) floor($count * 0.5), 0);
                $stats['response_time_p95'] = $responseTimes->get((int) floor($count * 0.95), 0);
                $stats['response_time_p99'] = $responseTimes->get((int) floor($count * 0.99), 0);
            } else {
                $stats['response_time_p50'] = 0;
                $stats['response_time_p95'] = 0;
                $stats['response_time_p99'] = 0;
            }

            // Operations by type
            $stats['operations_by_type'] = MessageLog::query()
                ->select('operation_type', DB::raw('count(*) as count'))
                ->groupBy('operation_type')
                ->pluck('count', 'operation_type')
                ->toArray();

            // Total operations today
            $stats['total_operations_today'] = MessageLog::query()
                ->whereDate('processed_at', today())
                ->count();

            // Total operations this week
            $stats['total_operations_week'] = MessageLog::query()
                ->whereBetween('processed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            // Cache stats (if Redis is available)
            $store = Cache::getStore();
            $stats['cache_hits'] = 0;
            $stats['cache_misses'] = 0;

            if ($store instanceof RedisStore) {
                $connection = $store->connection();

                if (is_object($connection) && method_exists($connection, 'info')) {
                    try {
                        $cacheInfo = $connection->info('stats') ?? [];
                        $stats['cache_hits'] = (int) ($cacheInfo['keyspace_hits'] ?? 0);
                        $stats['cache_misses'] = (int) ($cacheInfo['keyspace_misses'] ?? 0);
                    } catch (\Exception $e) {
                        // Ignore Redis INFO failures and keep zeros
                    }
                }
            }

            $totalCacheRequests = $stats['cache_hits'] + $stats['cache_misses'];
            $stats['cache_hit_rate'] = $totalCacheRequests > 0
                ? round(($stats['cache_hits'] / $totalCacheRequests) * 100, 2)
                : 0;

            return $stats;
        });
    }

    /**
     * Get system health status
     *
     * @return array<string, mixed>
     */

    /**
     * @return array<string, mixed>
     */
    public function getSystemHealth(): array
    {
        $health = [
            'ollama_status' => 'unknown',
            'database_status' => 'healthy',
            'cache_status' => 'healthy',
            'queue_status' => 'healthy',
        ];

        // Check Ollama server
        try {
            $client = app(\App\Contracts\OllamaClientContract::class);
            $health['ollama_status'] = $client->healthCheck() ? 'healthy' : 'unhealthy';
        } catch (\Exception $e) {
            $health['ollama_status'] = 'unhealthy';
        }

        // Check database
        try {
            DB::connection()->getPdo();
            $health['database_status'] = 'healthy';
        } catch (\Exception $e) {
            $health['database_status'] = 'unhealthy';
        }

        // Check cache
        try {
            Cache::put('health_check', true, 1);
            Cache::forget('health_check');
            $health['cache_status'] = 'healthy';
        } catch (\Exception $e) {
            $health['cache_status'] = 'unhealthy';
        }

        return $health;
    }
}
