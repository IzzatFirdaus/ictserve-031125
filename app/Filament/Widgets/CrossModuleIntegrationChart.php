<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

/**
 * Cross-Module Integration Chart Widget
 *
 * Displays asset-ticket linkage statistics, maintenance workflow metrics, and
 * cross-module integration trends. Uses WCAG 2.2 AA compliant colors for all
 * visualizations with 5-minute caching and real-time updates.
 *
 * @trace Requirements: Requirement 3.2, 13.3
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D04 §5.1 Cross-module integration
 * @see D12 UI/UX Design Guide - Compliant color palette
 */
class CrossModuleIntegrationChart extends ChartWidget
{
    protected ?string $heading = 'Integrasi Silang Modul';

    protected static ?int $sort = 3;

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
        // Note: category is a relationship, query via category relationship
        $maintenanceTickets = HelpdeskTicket::whereHas('category', function ($query) {
            $query->where('name', 'like', '%maintenance%');
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

        // WCAG 2.2 AA compliant colors
        $colors = [
            'rgba(181, 12, 12, 0.7)',  // danger - asset damage (#b50c0c)
            'rgba(255, 140, 0, 0.7)',  // warning - maintenance (#ff8c00)
            'rgba(0, 86, 179, 0.7)',   // primary - asset link (#0056b3)
            'rgba(25, 135, 84, 0.7)',  // success - general integration (#198754)
        ];

        $borderColors = [
            'rgba(181, 12, 12, 1)',
            'rgba(255, 140, 0, 1)',
            'rgba(0, 86, 179, 1)',
            'rgba(25, 135, 84, 1)',
        ];

        // Ensure we have enough colors for all data points
        while (count($colors) < count($data)) {
            $colors[] = 'rgba(107, 114, 128, 0.7)'; // gray for additional items
            $borderColors[] = 'rgba(107, 114, 128, 1)';
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
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#ffffff',
                    'bodyColor' => '#ffffff',
                    'borderColor' => 'rgba(255, 255, 255, 0.2)',
                    'borderWidth' => 1,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'color' => '#6b7280', // gray-500 for accessibility
                    ],
                    'grid' => [
                        'color' => 'rgba(229, 231, 235, 0.5)', // gray-200 with transparency
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'color' => '#6b7280', // gray-500 for accessibility
                    ],
                    'grid' => [
                        'display' => false,
                    ],
                ],
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
