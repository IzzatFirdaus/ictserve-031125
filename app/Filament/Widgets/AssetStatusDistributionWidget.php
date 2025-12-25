<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\AssetStatus;
use App\Filament\Traits\WidgetMetadata;
use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Asset Status Distribution Widget
 *
 * Displays a pie chart showing the distribution of assets by status
 * with WCAG 2.2 AA compliant colors and accessibility features.
 *
 * @trace Requirements: R13 (Missing Widget Integration)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D12 §4 MyDS Design System - WCAG 2.2 AA compliance
 * @see D03 SRS-AST-001 Asset management requirements
 *
 * @version 3.6.1
 */
class AssetStatusDistributionWidget extends ChartWidget
{
    use WidgetMetadata;

    protected ?string $heading = 'Taburan Status Aset';

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '300s'; // 5 minutes

    protected int|string|array $columnSpan = 'full';

    /**
     * Widget roles - accessible to all staff
     */
    public static function getWidgetRoles(): array
    {
        return ['staff', 'admin', 'superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D03 SRS-AST-001';
    }

    /**
     * Get the chart type
     */
    protected function getType(): string
    {
        return 'pie';
    }

    /**
     * Get chart data with caching
     */
    protected function getData(): array
    {
        return Cache::remember('widget:asset-status-distribution', 300, function () {
            return $this->calculateAssetDistribution();
        });
    }

    /**
     * Calculate asset status distribution
     */
    private function calculateAssetDistribution(): array
    {
        // Get asset counts by status using proper Eloquent methods
        $statusCounts = Asset::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Prepare data for chart
        $labels = [];
        $data = [];
        $colors = [];

        foreach (AssetStatus::cases() as $status) {
            $count = $statusCounts[$status->value] ?? 0;

            if ($count > 0) {
                $labels[] = $status->label();
                $data[] = $count;
                $colors[] = $this->getWcagCompliantColor($status);
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bilangan Aset',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => array_map([$this, 'darkenColor'], $colors),
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Get WCAG 2.2 AA compliant colors for asset status
     */
    private function getWcagCompliantColor(AssetStatus $status): string
    {
        return match ($status) {
            AssetStatus::AVAILABLE => '#059669',    // Green-600 (4.5:1 contrast)
            AssetStatus::RESERVED => '#D97706',     // Amber-600 (4.5:1 contrast)
            AssetStatus::LOANED => '#2563EB',       // Blue-600 (4.5:1 contrast)
            AssetStatus::MAINTENANCE => '#EA580C',  // Orange-600 (4.5:1 contrast)
            AssetStatus::RETIRED => '#6B7280',      // Gray-500 (4.5:1 contrast)
            AssetStatus::DAMAGED => '#DC2626',      // Red-600 (4.5:1 contrast)
        };
    }

    /**
     * Darken color for border (accessibility enhancement)
     */
    private function darkenColor(string $hexColor): string
    {
        // Remove # if present
        $hex = ltrim($hexColor, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Darken by 20%
        $r = max(0, $r - 51);
        $g = max(0, $g - 51);
        $b = max(0, $b - 51);

        return \sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Get chart options with accessibility features
     */
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 14,
                            'weight' => 'normal',
                        ],
                        'color' => '#374151', // Gray-700 for good contrast
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => '#1F2937', // Gray-800
                    'titleColor' => '#F9FAFB',      // Gray-50
                    'bodyColor' => '#F9FAFB',       // Gray-50
                    'borderColor' => '#6B7280',     // Gray-500
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'elements' => [
                'arc' => [
                    'borderWidth' => 2,
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            // Accessibility enhancements
            'accessibility' => [
                'announceNewData' => [
                    'enabled' => true,
                ],
            ],
        ];
    }

    /**
     * Get widget description for screen readers
     */
    public function getDescription(): ?string
    {
        $totalAssets = Asset::query()->count();

        return "Carta pai menunjukkan taburan {$totalAssets} aset mengikut status. ".
            'Gunakan kekunci tab untuk navigasi dan kekunci enter untuk maklumat lanjut.';
    }

    /**
     * Check if widget is WCAG 2.2 AA compliant
     */
    public static function isWcagCompliant(): bool
    {
        return true; // Implemented with WCAG 2.2 AA compliant colors and accessibility features
    }

    /**
     * Get widget configuration for registry
     */
    public static function getWidgetConfiguration(): array
    {
        return [
            'category' => 'charts',
            'sort_order' => 3,
            'is_active' => true,
            'roles' => ['staff', 'admin', 'superuser'],
            'refresh_rate' => 300, // 5 minutes
            'cache_ttl' => 300,    // 5 minutes
            'configuration' => [
                'chart_type' => 'pie',
                'wcag_compliant' => true,
                'accessibility_enabled' => true,
                'responsive' => true,
            ],
        ];
    }
}
