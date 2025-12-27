<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\AIMetricsCollector;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * AI Cost Widget (Widget Kos Penggunaan AI)
 *
 * Displays daily cost tracking and optimization recommendations for AWS Bedrock
 * usage with cost trend analysis and budget monitoring. Provides real-time insights
 * into token consumption and cost efficiency metrics.
 *
 * Features:
 * - Daily cost tracking (AWS Bedrock API usage)
 * - Token consumption metrics
 * - Cost trend analysis and forecasting
 * - Budget threshold alerts
 * - Optimization recommendations
 *
 * @trace D18-§4.1 (AI Cost Monitoring and Analytics)
 * @trace D03-SRS-AI-017 (Cost Optimization Requirements)
 * @trace D04-§6.4 (Cloud Hybrid AI Architecture)
 * @trace D12-§7 (AI Dashboard Widgets)
 *
 * @see \App\Services\AIMetricsCollector
 * @see docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md
 */
class AICostWidget extends BaseWidget
{
    protected ?string $pollingInterval = '5m';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Widget metadata for registry system
     */
    public static function getWidgetMetadata(): array
    {
        return [
            'category' => 'header',
            'sort_order' => 16,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 300, // 5 minutes
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
            $costMetrics = $metricsCollector->getCostMetrics();

            return [
                $this->createDailyCostStat($costMetrics),
                $this->createMonthlyCostStat($costMetrics),
                $this->createCostTrendStat($costMetrics),
                $this->createOptimizationStat($costMetrics),
            ];
        } catch (\Exception $e) {
            return $this->getErrorStats();
        }
    }

    /**
     * Create daily cost stat
     *
     * @param  array<string, mixed>  $costMetrics
     */

    /**
     * @param  array<string, mixed>  $costMetrics
     */
    private function createDailyCostStat(array $costMetrics): Stat
    {
        $dailyCost = $costMetrics['daily_cost_usd'] ?? 0;
        $yesterdayCost = $costMetrics['yesterday_cost_usd'] ?? 0;

        $color = match (true) {
            $dailyCost === 0 => 'gray',
            $dailyCost < 1 => 'success',
            $dailyCost < 5 => 'info',
            $dailyCost < 10 => 'warning',
            default => 'danger',
        };

        $comparison = $yesterdayCost > 0
            ? ($dailyCost > $yesterdayCost ? 'naik' : ($dailyCost < $yesterdayCost ? 'turun' : 'sama'))
            : 'tiada data semalam';

        return Stat::make('Kos Harian (USD)', '$'.number_format($dailyCost, 4))
            ->description('Semalam: $'.number_format($yesterdayCost, 4)." ({$comparison})")
            ->descriptionIcon($this->getTrendIcon($dailyCost, $yesterdayCost))
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-cost-stat daily-cost',
                'data-cost' => $dailyCost,
                'aria-label' => "Kos harian AI: {$dailyCost} USD, semalam {$yesterdayCost} USD",
            ]);
    }

    /**
     * Create monthly cost stat
     *
     * @param  array<string, mixed>  $costMetrics
     */

    /**
     * @param  array<string, mixed>  $costMetrics
     */
    private function createMonthlyCostStat(array $costMetrics): Stat
    {
        $monthlyCost = $costMetrics['monthly_cost_usd'] ?? 0;
        $estimatedMonthlyCost = $costMetrics['estimated_monthly_cost'] ?? 0;

        $color = match (true) {
            $estimatedMonthlyCost === 0 => 'gray',
            $estimatedMonthlyCost < 30 => 'success',
            $estimatedMonthlyCost < 100 => 'info',
            $estimatedMonthlyCost < 200 => 'warning',
            default => 'danger',
        };

        $budgetStatus = $this->getBudgetStatus($estimatedMonthlyCost);

        return Stat::make('Kos Bulanan (USD)', '$'.number_format($monthlyCost, 2))
            ->description('Anggaran bulan ini: $'.number_format($estimatedMonthlyCost, 2)." | {$budgetStatus}")
            ->descriptionIcon('heroicon-o-calendar')
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-cost-stat monthly-cost',
                'data-cost' => $monthlyCost,
                'data-estimated' => $estimatedMonthlyCost,
                'aria-label' => "Kos bulanan AI: {$monthlyCost} USD, anggaran {$estimatedMonthlyCost} USD",
            ]);
    }

    /**
     * Create cost trend stat
     *
     * @param  array<string, mixed>  $costMetrics
     */

    /**
     * @param  array<string, mixed>  $costMetrics
     */
    private function createCostTrendStat(array $costMetrics): Stat
    {
        $trendPercent = $costMetrics['cost_trend_percent'] ?? 0;
        $dailyCost = $costMetrics['daily_cost_usd'] ?? 0;

        $color = match (true) {
            $trendPercent === 0 => 'gray',
            $trendPercent < -10 => 'success',
            $trendPercent < 10 => 'info',
            $trendPercent < 25 => 'warning',
            default => 'danger',
        };

        $trendText = match (true) {
            $trendPercent > 0 => "↗ +{$trendPercent}%",
            $trendPercent < 0 => "↘ {$trendPercent}%",
            default => '→ 0%',
        };

        $trendDescription = match (true) {
            $trendPercent > 20 => 'Peningkatan ketara',
            $trendPercent > 10 => 'Peningkatan sederhana',
            $trendPercent > 0 => 'Peningkatan kecil',
            $trendPercent < -20 => 'Penurunan ketara',
            $trendPercent < -10 => 'Penurunan sederhana',
            $trendPercent < 0 => 'Penurunan kecil',
            default => 'Tiada perubahan',
        };

        return Stat::make('Trend Kos', $trendText)
            ->description($trendDescription.' berbanding semalam')
            ->descriptionIcon($this->getTrendIcon($dailyCost, $dailyCost - ($dailyCost * $trendPercent / 100)))
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-cost-stat cost-trend',
                'data-trend' => $trendPercent,
                'aria-label' => "Trend kos AI: {$trendPercent}% perubahan, {$trendDescription}",
            ]);
    }

    /**
     * Create optimization recommendations stat
     *
     * @param  array<string, mixed>  $costMetrics
     */

    /**
     * @param  array<string, mixed>  $costMetrics
     */
    private function createOptimizationStat(array $costMetrics): Stat
    {
        $recommendations = $costMetrics['recommendations'] ?? [];
        $recommendationCount = count($recommendations);
        $primaryRecommendation = $recommendations[0] ?? 'Tiada cadangan tersedia';

        $color = match ($recommendationCount) {
            0 => 'gray',
            1 => 'success',
            2 => 'info',
            default => 'warning',
        };

        $icon = match ($recommendationCount) {
            0 => 'heroicon-o-check-circle',
            1 => 'heroicon-o-light-bulb',
            default => 'heroicon-o-exclamation-triangle',
        };

        return Stat::make('Pengoptimuman Kos', (string) $recommendationCount.' Cadangan')
            ->description($this->truncateText($primaryRecommendation, 60))
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-cost-stat optimization-recommendations',
                'data-recommendations' => $recommendationCount,
                'title' => implode(' | ', $recommendations),
                'aria-label' => "Cadangan pengoptimuman kos: {$recommendationCount} cadangan tersedia",
            ]);
    }

    /**
     * Get error stats when cost collection fails
     *
     * @return array<Stat>
     */
    private function getErrorStats(): array
    {
        return [
            Stat::make('Kos AI', 'Tidak Tersedia')
                ->description('Gagal mengumpul data kos')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'ai-cost-stat ai-error-stat',
                    'aria-label' => 'Data kos AI tidak tersedia kerana ralat',
                ]),
        ];
    }

    /**
     * Get trend icon based on cost comparison
     */
    private function getTrendIcon(float $current, float $previous): string
    {
        if ($current > $previous) {
            return 'heroicon-o-arrow-trending-up';
        } elseif ($current < $previous) {
            return 'heroicon-o-arrow-trending-down';
        }

        return 'heroicon-o-minus';
    }

    /**
     * Get budget status description
     */
    private function getBudgetStatus(float $estimatedCost): string
    {
        // Assuming a monthly budget of $100 USD
        $monthlyBudget = 100.0;
        $percentage = $monthlyBudget > 0 ? ($estimatedCost / $monthlyBudget) * 100 : 0;

        return match (true) {
            $percentage < 50 => 'Dalam bajet',
            $percentage < 80 => 'Hampir had',
            $percentage < 100 => 'Mendekati had',
            default => 'Melebihi bajet',
        };
    }

    /**
     * Truncate text to specified length
     */
    private function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length - 3).'...';
    }
}
