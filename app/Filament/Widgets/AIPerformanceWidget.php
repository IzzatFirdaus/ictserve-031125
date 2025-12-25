<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\AIMetricsCollector;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * AI Performance Widget
 *
 * Displays real-time performance metrics for Ollama and AWS Bedrock AI services
 * including response times, success rates, and request volumes.
 *
 * trace: D18-§4.1 (AI Performance Monitoring), R21 (Cloud Hybrid AI Dashboard Integration)
 * trace: D04-§6.4 (AI Architecture), D11-§8.1 (Performance Monitoring)
 *
 * @see docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md
 */
class AIPerformanceWidget extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Widget metadata for registry system
     */
    public static function getWidgetMetadata(): array
    {
        return [
            'category' => 'header',
            'sort_order' => 15,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 30,
            'cache_ttl' => 120,
        ];
    }

    /**
     * Check if user can access this widget
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Get widget stats
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        if (! self::canView()) {
            return [];
        }

        try {
            $metricsCollector = app(AIMetricsCollector::class);
            $metrics = $metricsCollector->getPerformanceMetrics();

            return [
                $this->createOllamaPerformanceStat($metrics['ollama'] ?? []),
                $this->createBedrockPerformanceStat($metrics['bedrock'] ?? []),
                $this->createCombinedPerformanceStat($metrics['combined'] ?? []),
                $this->createRequestVolumeStat($metrics['combined'] ?? []),
            ];
        } catch (\Exception $e) {
            return $this->getErrorStats();
        }
    }

    /**
     * Create Ollama performance stat
     *
     * @param  array<string, mixed>  $ollamaMetrics
     */

    /**
     * @param  array<string, mixed>  $ollamaMetrics
     */
    private function createOllamaPerformanceStat(array $ollamaMetrics): Stat
    {
        $responseTime = $ollamaMetrics['avg_response_time_ms'] ?? 0;
        $successRate = $ollamaMetrics['success_rate'] ?? 0;
        $status = $ollamaMetrics['status'] ?? 'unknown';

        $color = match ($status) {
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'inactive' => 'gray',
            default => 'gray',
        };

        $icon = match ($status) {
            'excellent' => 'heroicon-o-check-circle',
            'good' => 'heroicon-o-check-circle',
            'fair' => 'heroicon-o-exclamation-triangle',
            'poor' => 'heroicon-o-x-circle',
            'inactive' => 'heroicon-o-pause-circle',
            default => 'heroicon-o-question-mark-circle',
        };

        return Stat::make('Prestasi Ollama (Tempatan)', $this->formatResponseTime($responseTime))
            ->description("Kadar kejayaan: {$successRate}% | Status: ".$this->translateStatus($status))
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-performance-stat',
                'data-service' => 'ollama',
                'aria-label' => "Prestasi Ollama: masa respons {$responseTime}ms, kadar kejayaan {$successRate}%",
            ]);
    }

    /**
     * Create Bedrock performance stat
     *
     * @param  array<string, mixed>  $bedrockMetrics
     */

    /**
     * @param  array<string, mixed>  $bedrockMetrics
     */
    private function createBedrockPerformanceStat(array $bedrockMetrics): Stat
    {
        $responseTime = $bedrockMetrics['avg_response_time_ms'] ?? 0;
        $successRate = $bedrockMetrics['success_rate'] ?? 0;
        $status = $bedrockMetrics['status'] ?? 'unknown';
        $tokens = $bedrockMetrics['total_tokens_24h'] ?? 0;

        $color = match ($status) {
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'inactive' => 'gray',
            default => 'gray',
        };

        $icon = match ($status) {
            'excellent' => 'heroicon-o-cloud',
            'good' => 'heroicon-o-cloud',
            'fair' => 'heroicon-o-exclamation-triangle',
            'poor' => 'heroicon-o-x-circle',
            'inactive' => 'heroicon-o-pause-circle',
            default => 'heroicon-o-question-mark-circle',
        };

        return Stat::make('Prestasi Bedrock (Awan)', $this->formatResponseTime($responseTime))
            ->description("Kadar kejayaan: {$successRate}% | Token: ".number_format($tokens))
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-performance-stat',
                'data-service' => 'bedrock',
                'aria-label' => "Prestasi Bedrock: masa respons {$responseTime}ms, kadar kejayaan {$successRate}%, {$tokens} token",
            ]);
    }

    /**
     * Create combined performance stat
     *
     * @param  array<string, mixed>  $combinedMetrics
     */

    /**
     * @param  array<string, mixed>  $combinedMetrics
     */
    private function createCombinedPerformanceStat(array $combinedMetrics): Stat
    {
        $responseTime = $combinedMetrics['avg_response_time_ms'] ?? 0;
        $successRate = $combinedMetrics['success_rate'] ?? 0;
        $ollamaPercentage = $combinedMetrics['ollama_percentage'] ?? 0;
        $bedrockPercentage = $combinedMetrics['bedrock_percentage'] ?? 0;

        $color = match (true) {
            $successRate >= 95 => 'success',
            $successRate >= 90 => 'info',
            $successRate >= 80 => 'warning',
            default => 'danger',
        };

        return Stat::make('Prestasi Keseluruhan AI', $this->formatResponseTime($responseTime))
            ->description("Kadar kejayaan: {$successRate}% | Ollama: {$ollamaPercentage}% | Bedrock: {$bedrockPercentage}%")
            ->descriptionIcon('heroicon-o-cpu-chip')
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-performance-stat ai-combined-stat',
                'data-service' => 'combined',
                'aria-label' => "Prestasi keseluruhan AI: masa respons {$responseTime}ms, kadar kejayaan {$successRate}%",
            ]);
    }

    /**
     * Create request volume stat
     *
     * @param  array<string, mixed>  $combinedMetrics
     */

    /**
     * @param  array<string, mixed>  $combinedMetrics
     */
    private function createRequestVolumeStat(array $combinedMetrics): Stat
    {
        $totalRequests = $combinedMetrics['total_requests_24h'] ?? 0;
        $requestsPerHour = round($totalRequests / 24, 1);

        $color = match (true) {
            $totalRequests > 1000 => 'success',
            $totalRequests > 500 => 'info',
            $totalRequests > 100 => 'warning',
            default => 'gray',
        };

        return Stat::make('Volum Permintaan (24 Jam)', number_format($totalRequests))
            ->description("Purata sejam: {$requestsPerHour} permintaan")
            ->descriptionIcon('heroicon-o-chart-bar')
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-performance-stat ai-volume-stat',
                'data-metric' => 'volume',
                'aria-label' => "Volum permintaan AI: {$totalRequests} dalam 24 jam, purata {$requestsPerHour} sejam",
            ]);
    }

    /**
     * Get error stats when metrics collection fails
     *
     * @return array<Stat>
     */
    private function getErrorStats(): array
    {
        return [
            Stat::make('Prestasi AI', 'Tidak Tersedia')
                ->description('Gagal mengumpul metrik prestasi')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'ai-performance-stat ai-error-stat',
                    'aria-label' => 'Prestasi AI tidak tersedia kerana ralat',
                ]),
        ];
    }

    /**
     * Format response time for display
     */
    private function formatResponseTime(float $responseTime): string
    {
        if ($responseTime === 0.0) {
            return 'Tidak Aktif';
        }

        if ($responseTime < 1000) {
            return round($responseTime).'ms';
        }

        return round($responseTime / 1000, 1).'s';
    }

    /**
     * Translate status to Bahasa Melayu
     */
    private function translateStatus(string $status): string
    {
        return match ($status) {
            'excellent' => 'Cemerlang',
            'good' => 'Baik',
            'fair' => 'Sederhana',
            'poor' => 'Lemah',
            'inactive' => 'Tidak Aktif',
            default => 'Tidak Diketahui',
        };
    }
}
