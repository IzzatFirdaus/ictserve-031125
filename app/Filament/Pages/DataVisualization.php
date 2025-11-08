<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\DataVisualizationService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DataVisualization extends Page
{
    protected string $view = 'filament.pages.data-visualization';

    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationLabel(): string
    {
        return 'Visualisasi Data';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports & Analytics';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public function getTitle(): string
    {
        return 'Visualisasi Data';
    }

    public function getHeading(): string
    {
        return 'Dashboard Visualisasi Data';
    }

    public function getSubheading(): ?string
    {
        return 'Analisis interaktif dengan carta dan trend untuk insight mendalam';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Muat Semula')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect(static::getUrl())),

            Action::make('export_dashboard')
                ->label('Eksport Dashboard')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('format')
                        ->label('Format')
                        ->options([
                            'png' => 'PNG Image',
                            'pdf' => 'PDF Document',
                            'svg' => 'SVG Vector',
                        ])
                        ->default('png')
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        $visualizationService = app(DataVisualizationService::class);
                        $dashboardData = $visualizationService->getPerformanceDashboardData();

                        $result = $visualizationService->exportChart($dashboardData, $data['format']);

                        Notification::make()
                            ->title('Dashboard berjaya dieksport')
                            ->body("Format: {$result['format']}, Saiz: {$result['size']}")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal mengeksport dashboard')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getDashboardData(): array
    {
        return app(DataVisualizationService::class)->getPerformanceDashboardData();
    }

    public function getTicketTrendsData(): array
    {
        $startDate = now()->subDays(30);
        $endDate = now();

        return app(DataVisualizationService::class)->getTicketTrendsChartData($startDate, $endDate);
    }

    public function getAssetUtilizationData(): array
    {
        return app(DataVisualizationService::class)->getAssetUtilizationChartData();
    }

    public function getSlaComplianceData(): array
    {
        $startDate = now()->subDays(30);
        $endDate = now();

        return app(DataVisualizationService::class)->getSlaComplianceChartData($startDate, $endDate);
    }

    public function getPriorityDistributionData(): array
    {
        return app(DataVisualizationService::class)->getPriorityDistributionData();
    }

    public function getResolutionTimeTrendsData(): array
    {
        $startDate = now()->subDays(30);
        $endDate = now();

        return app(DataVisualizationService::class)->getResolutionTimeTrendsData($startDate, $endDate);
    }

    public function exportChart(string $chartType): void
    {
        $this->mountAction('exportChart', ['chart_type' => $chartType]);
    }

    protected function getActions(): array
    {
        return [
            Action::make('exportChart')
                ->label('Eksport Carta')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\Hidden::make('chart_type'),

                    Forms\Components\Select::make('format')
                        ->label('Format')
                        ->options([
                            'png' => 'PNG Image (Recommended)',
                            'pdf' => 'PDF Document',
                            'svg' => 'SVG Vector',
                            'jpg' => 'JPEG Image',
                        ])
                        ->default('png')
                        ->required(),

                    Forms\Components\Select::make('size')
                        ->label('Saiz')
                        ->options([
                            '800x600' => 'Standard (800x600)',
                            '1024x768' => 'Large (1024x768)',
                            '1920x1080' => 'HD (1920x1080)',
                            '2560x1440' => '2K (2560x1440)',
                        ])
                        ->default('1024x768')
                        ->required(),

                    Forms\Components\Toggle::make('include_data')
                        ->label('Sertakan Data Mentah')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    try {
                        $visualizationService = app(DataVisualizationService::class);

                        $chartData = match ($data['chart_type']) {
                            'ticket_trends' => $this->getTicketTrendsData(),
                            'asset_utilization' => $this->getAssetUtilizationData(),
                            'sla_compliance' => $this->getSlaComplianceData(),
                            'priority_distribution' => $this->getPriorityDistributionData(),
                            'resolution_time_trends' => $this->getResolutionTimeTrendsData(),
                            default => [],
                        };

                        $result = $visualizationService->exportChart($chartData, $data['format']);

                        Notification::make()
                            ->title('Carta berjaya dieksport')
                            ->body("Format: {$result['format']}, Saiz: {$result['size']}")
                            ->success()
                            ->send();

                        $this->dispatch('chart-exported', [
                            'chart_type' => $data['chart_type'],
                            'format' => $data['format'],
                            'filename' => $result['filename'],
                        ]);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal mengeksport carta')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
