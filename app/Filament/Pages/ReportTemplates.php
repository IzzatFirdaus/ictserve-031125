<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ReportTemplateService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ReportTemplates extends Page
{
    protected string $view = 'filament.pages.report-templates';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-duplicate';
    }

    public static function getNavigationLabel(): string
    {
        return 'Template Laporan';
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
        return 'Template Laporan';
    }

    public function getHeading(): string
    {
        return 'Template Laporan Pra-konfigurasi';
    }

    public function getSubheading(): ?string
    {
        return 'Jana laporan menggunakan template yang telah disediakan dengan satu klik';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Muat Semula')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect(static::getUrl())),
        ];
    }

    public function generateMonthlyTicketSummary(): void
    {
        try {
            $templateService = app(ReportTemplateService::class);
            $result = $templateService->generateMonthlyTicketSummary('pdf');

            Notification::make()
                ->title('Laporan berjaya dijana')
                ->body('Ringkasan tiket bulanan telah dijana. Saiz fail: '.$result['formatted_size'])
                ->success()
                ->send();

            // In production, this would trigger a download or email
            $this->dispatch('report-generated', [
                'type' => 'monthly_ticket_summary',
                'size' => $result['formatted_size'],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjana laporan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateAssetUtilization(): void
    {
        try {
            $templateService = app(ReportTemplateService::class);
            $result = $templateService->generateAssetUtilizationReport('pdf');

            Notification::make()
                ->title('Laporan berjaya dijana')
                ->body('Laporan penggunaan aset telah dijana. Saiz fail: '.$result['formatted_size'])
                ->success()
                ->send();

            $this->dispatch('report-generated', [
                'type' => 'asset_utilization',
                'size' => $result['formatted_size'],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjana laporan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateSlaCompliance(): void
    {
        try {
            $templateService = app(ReportTemplateService::class);
            $result = $templateService->generateSlaComplianceReport('pdf');

            Notification::make()
                ->title('Laporan berjaya dijana')
                ->body('Laporan pematuhan SLA telah dijana. Saiz fail: '.$result['formatted_size'])
                ->success()
                ->send();

            $this->dispatch('report-generated', [
                'type' => 'sla_compliance',
                'size' => $result['formatted_size'],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjana laporan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateOverdueItems(): void
    {
        try {
            $templateService = app(ReportTemplateService::class);
            $result = $templateService->generateOverdueItemsReport('pdf');

            Notification::make()
                ->title('Laporan berjaya dijana')
                ->body('Laporan item tertunggak telah dijana. Saiz fail: '.$result['formatted_size'])
                ->success()
                ->send();

            $this->dispatch('report-generated', [
                'type' => 'overdue_items',
                'size' => $result['formatted_size'],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjana laporan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateWeeklyPerformance(): void
    {
        try {
            $templateService = app(ReportTemplateService::class);
            $result = $templateService->generateWeeklyPerformanceReport('pdf');

            Notification::make()
                ->title('Laporan berjaya dijana')
                ->body('Laporan prestasi mingguan telah dijana. Saiz fail: '.$result['formatted_size'])
                ->success()
                ->send();

            $this->dispatch('report-generated', [
                'type' => 'weekly_performance',
                'size' => $result['formatted_size'],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjana laporan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generateCustomTemplate(): void
    {
        $this->mountAction('customTemplate');
    }

    protected function getActions(): array
    {
        return [
            Action::make('customTemplate')
                ->label('Template Tersuai')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->form([
                    Forms\Components\Select::make('template')
                        ->label('Pilih Template')
                        ->options([
                            'monthly_ticket_summary' => 'Ringkasan Tiket Bulanan',
                            'asset_utilization' => 'Laporan Penggunaan Aset',
                            'sla_compliance' => 'Laporan Pematuhan SLA',
                            'overdue_items' => 'Laporan Item Tertunggak',
                            'weekly_performance' => 'Prestasi Mingguan',
                        ])
                        ->required(),

                    Forms\Components\Select::make('format')
                        ->label('Format')
                        ->options([
                            'pdf' => 'PDF',
                            'csv' => 'CSV',
                            'excel' => 'Excel',
                        ])
                        ->default('pdf')
                        ->required(),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tarikh Mula')
                        ->default(now()->subMonth()),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tarikh Akhir')
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    try {
                        $templateService = app(ReportTemplateService::class);

                        $result = match ($data['template']) {
                            'monthly_ticket_summary' => $templateService->generateMonthlyTicketSummary(
                                $data['format'],
                                Carbon::parse($data['start_date'])
                            ),
                            'asset_utilization' => $templateService->generateAssetUtilizationReport(
                                $data['format'],
                                Carbon::parse($data['start_date'])
                            ),
                            'sla_compliance' => $templateService->generateSlaComplianceReport(
                                $data['format'],
                                Carbon::parse($data['start_date']),
                                Carbon::parse($data['end_date'])
                            ),
                            'overdue_items' => $templateService->generateOverdueItemsReport($data['format']),
                            'weekly_performance' => $templateService->generateWeeklyPerformanceReport(
                                $data['format'],
                                Carbon::parse($data['start_date'])
                            ),
                            default => throw new \InvalidArgumentException("Unknown template: {$data['template']}"),
                        };

                        Notification::make()
                            ->title('Laporan tersuai berjaya dijana')
                            ->body('Saiz fail: '.$result['formatted_size'])
                            ->success()
                            ->send();

                        $this->dispatch('report-generated', [
                            'type' => $data['template'],
                            'format' => $data['format'],
                            'size' => $result['formatted_size'],
                        ]);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal menjana laporan tersuai')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function getTemplates(): array
    {
        return app(ReportTemplateService::class)->getAvailableTemplates();
    }
}
