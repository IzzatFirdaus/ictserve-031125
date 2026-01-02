<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\ThemeAwareChartColors;
use App\Filament\Traits\WidgetMetadata;
use App\Filament\Widgets\Concerns\HandlesEmptyChartData;
use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Cross-Module Integration Chart Widget
 *
 * Displays asset-ticket linkage statistics, maintenance workflow metrics, and
 * cross-module integration trends. Uses theme-aware WCAG 2.2 AA compliant colors
 * for all visualizations with 5-minute caching and real-time updates.
 *
 * @trace Requirements: Requirement 3.2, 6.7, 13.3
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D04 §5.1 Cross-module integration
 * @see D12 UI/UX Design Guide - Compliant color palette
 */
class CrossModuleIntegrationChart extends ChartWidget
{
    use HandlesEmptyChartData;
    use ThemeAwareChartColors;
    use WidgetMetadata;

    protected ?string $heading = 'Integrasi Silang Modul';

    protected static ?int $sort = 3;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance';
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = '300s'; // 5-minute real-time updates

    protected function getData(): array
    {
        /** @var array<string, mixed> */
        $data = Cache::remember('cross-module-integration-chart', 300, function () {
            return $this->calculateIntegrationData();
        });

        return $data;
    }

    /**
     * Calculate cross-module integration statistics
     *
     * @return array<string, mixed>
     */
    protected function calculateIntegrationData(): array
    {
        // Asset-ticket linking statistics
        $ticketsWithAssets = HelpdeskTicket::whereNotNull('asset_id')->count();

        // Maintenance workflow metrics
        // Note: Use actual column name (name_en) instead of accessor to avoid query issues
        $maintenanceTickets = HelpdeskTicket::whereHas('category', function ($query) {
            $query->where('name_en', 'like', '%maintenance%');
        })->count();
        $assetsRequiringMaintenance = Asset::where('status', 'maintenance')->count();

        // Cross-module integration types
        $integrationTypes = [
            'Tiket dengan Aset' => $ticketsWithAssets,
            'Tiket Penyelenggaraan' => $maintenanceTickets,
            'Aset Perlu Penyelenggaraan' => $assetsRequiringMaintenance,
        ];

        // Check if CrossModuleIntegration model exists and has data
        if (class_exists(CrossModuleIntegration::class)) {
            $damageReports = CrossModuleIntegration::where('integration_type', CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT)->count();
            $maintenanceRequests = CrossModuleIntegration::where('integration_type', CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST)->count();
            $assetTicketLinks = CrossModuleIntegration::where('integration_type', CrossModuleIntegration::TYPE_ASSET_TICKET_LINK)->count();

            $integrationTypes = [
                'Laporan Kerosakan' => $damageReports,
                'Permintaan Penyelenggaraan' => $maintenanceRequests,
                'Pautan Aset-Tiket' => $assetTicketLinks,
                'Tiket dengan Aset' => $ticketsWithAssets,
            ];
        }

        $data = array_values($integrationTypes);
        $labels = array_keys($integrationTypes);

        // Theme-aware WCAG 2.2 AA compliant colors
        $colors = [
            $this->getChartDangerColor(0.7),   // danger - asset damage
            $this->getChartWarningColor(0.7),  // warning - maintenance
            $this->getChartPrimaryColor(0.7),  // primary - asset link
            $this->getChartSuccessColor(0.7),  // success - general integration
        ];

        $borderColors = [
            $this->getChartDangerColor(1.0),
            $this->getChartWarningColor(1.0),
            $this->getChartPrimaryColor(1.0),
            $this->getChartSuccessColor(1.0),
        ];

        // Ensure we have enough colors for all data points
        while (count($colors) < count($data)) {
            $colors[] = $this->getChartGrayColor(0.7);
            $borderColors[] = $this->getChartGrayColor(1.0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bilangan Integrasi',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => $this->getChartTooltipConfig(),
            ],
            'scales' => [
                'y' => $this->getChartYScaleOptions(),
                'x' => $this->getChartXScaleOptions(false),
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }

    /**
     * Get description for the widget
     */
    public function getDescription(): ?string
    {
        return 'Statistik integrasi antara modul helpdesk dan pinjaman aset';
    }
}
