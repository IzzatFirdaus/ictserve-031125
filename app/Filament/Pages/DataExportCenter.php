<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Exports\UnifiedAnalyticsExporter;
use App\Services\ReportExportService;
use App\Services\UnifiedAnalyticsService;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Data Export Center Page
 *
 * Centralized interface for exporting ICTServe data in multiple formats.
 * Provides CSV, PDF, and Excel export with proper formatting and metadata.
 *
 * Requirements: 13.5, 4.5, 6.1, 7.2
 */
class DataExportCenter extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-arrow-down-tray';
    }

    public static function getNavigationLabel(): string
    {
        return 'Pusat Eksport Data';
    }

    public function getTitle(): string
    {
        return 'Pusat Eksport Data ICTServe';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public function getView(): string
    {
        return 'filament.pages.data-export-center';
    }

    public function mount(): void
    {
        $this->data = [
            'start_date' => now()->subMonth()->startOfMonth()->toDateString(),
            'end_date' => now()->subMonth()->endOfMonth()->toDateString(),
            'export_format' => 'csv',
            'include_metadata' => true,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Parameter Eksport')
                    ->description('Pilih tarikh dan format untuk eksport data')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Tarikh Mula')
                            ->required()
                            ->native(false)
                            ->maxDate(now()),

                        DatePicker::make('end_date')
                            ->label('Tarikh Tamat')
                            ->required()
                            ->native(false)
                            ->maxDate(now())
                            ->afterOrEqual('start_date'),

                        Select::make('export_format')
                            ->label('Format Eksport')
                            ->options([
                                'csv' => 'CSV - Comma Separated Values',
                                'excel' => 'Excel - Microsoft Excel Format',
                                'pdf' => 'PDF - Portable Document Format',
                            ])
                            ->required()
                            ->default('csv'),

                        Select::make('data_type')
                            ->label('Jenis Data')
                            ->options([
                                'unified_analytics' => 'Analitik Terpadu (Helpdesk + Pinjaman)',
                                'helpdesk_only' => 'Data Helpdesk Sahaja',
                                'loans_only' => 'Data Pinjaman Sahaja',
                                'assets_only' => 'Data Aset Sahaja',
                                'cross_module' => 'Integrasi Silang Modul',
                            ])
                            ->required()
                            ->default('unified_analytics'),

                        Toggle::make('include_metadata')
                            ->label('Sertakan Metadata')
                            ->helperText('Termasuk maklumat laporan, timestamp, dan header')
                            ->default(true),

                        Toggle::make('compress_large_files')
                            ->label('Mampat Fail Besar')
                            ->helperText('Mampat fail yang melebihi 10MB')
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportData')
                ->label('Eksport Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action('exportData')
                ->requiresConfirmation()
                ->modalHeading('Eksport Data ICTServe')
                ->modalDescription('Adakah anda pasti untuk mengeksport data dengan parameter yang dipilih?')
                ->modalSubmitActionLabel('Ya, Eksport'),

            ExportAction::make('quickExport')
                ->label('Eksport Pantas')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->exporter(UnifiedAnalyticsExporter::class)
                ->modifyQueryUsing(function ($query) {
                    // This will be handled by the exporter's getRecords method
                    return $query;
                }),

            Action::make('downloadSample')
                ->label('Muat Turun Contoh')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action('downloadSample'),
        ];
    }

    public function exportData(): void
    {
        try {
            $exportService = app(ReportExportService::class);
            $analyticsService = app(UnifiedAnalyticsService::class);

            $startDate = new \DateTime($this->data['start_date']);
            $endDate = new \DateTime($this->data['end_date']);

            // Generate report data based on selected type
            $reportData = $this->generateReportData($analyticsService, $startDate, $endDate);

            // Generate export files
            $files = $exportService->generateReportFiles($reportData, [
                'formats' => [$this->data['export_format']],
                'include_metadata' => $this->data['include_metadata'],
                'compress' => $this->data['compress_large_files'],
            ]);

            if (empty($files)) {
                throw new \Exception('Gagal menjana fail eksport');
            }

            $format = $this->data['export_format'];
            $filepath = $files[$format];

            // Provide download link
            $downloadUrl = Storage::url($filepath);

            Notification::make()
                ->title('Eksport Berjaya')
                ->body("Data telah dieksport dalam format {$format}. Fail akan dimuat turun secara automatik.")
                ->success()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Muat Turun')
                        ->url($downloadUrl)
                        ->openUrlInNewTab(),
                ])
                ->send();

            // Clean up file after 1 hour
            \Illuminate\Support\Facades\Cache::put(
                "cleanup_export_{$filepath}",
                $filepath,
                now()->addHour()
            );

        } catch (\Exception $e) {
            Notification::make()
                ->title('Eksport Gagal')
                ->body("Ralat semasa mengeksport data: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    public function downloadSample(): void
    {
        try {
            $sampleData = $this->generateSampleData();
            $exportService = app(ReportExportService::class);

            $files = $exportService->generateReportFiles($sampleData, [
                'formats' => ['csv'],
                'include_metadata' => true,
            ]);

            if (! empty($files)) {
                $downloadUrl = Storage::url($files['csv']);

                Notification::make()
                    ->title('Contoh Data Sedia')
                    ->body('Fail contoh telah dijana dan sedia untuk dimuat turun.')
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('download')
                            ->label('Muat Turun Contoh')
                            ->url($downloadUrl)
                            ->openUrlInNewTab(),
                    ])
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menjana Contoh')
                ->body("Ralat: {$e->getMessage()}")
                ->danger()
                ->send();
        }
    }

    private function generateReportData(UnifiedAnalyticsService $service, \DateTime $start, \DateTime $end): array
    {
        return match ($this->data['data_type']) {
            'unified_analytics' => [
                'report_info' => [
                    'title' => 'Eksport Analitik Terpadu ICTServe',
                    'frequency' => 'custom',
                    'period' => [
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                        'days' => $start->diff($end)->days + 1,
                    ],
                    'generated_at' => now()->toDateTimeString(),
                    'generated_by' => auth()->user()->name ?? 'Sistem',
                ],
                'unified_metrics' => $service->getDashboardMetrics($start, $end),
            ],
            'helpdesk_only' => $this->generateHelpdeskOnlyData($start, $end),
            'loans_only' => $this->generateLoansOnlyData($start, $end),
            'assets_only' => $this->generateAssetsOnlyData($start, $end),
            'cross_module' => $this->generateCrossModuleData($start, $end),
            default => throw new \InvalidArgumentException('Jenis data tidak sah'),
        };
    }

    private function generateHelpdeskOnlyData(\DateTime $start, \DateTime $end): array
    {
        // Implementation for helpdesk-only data
        return [
            'report_info' => [
                'title' => 'Eksport Data Helpdesk ICTServe',
                'frequency' => 'custom',
                'period' => ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')],
                'generated_at' => now()->toDateTimeString(),
            ],
            // Add helpdesk-specific data structure
        ];
    }

    private function generateLoansOnlyData(\DateTime $start, \DateTime $end): array
    {
        // Implementation for loans-only data
        return [
            'report_info' => [
                'title' => 'Eksport Data Pinjaman Aset ICTServe',
                'frequency' => 'custom',
                'period' => ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')],
                'generated_at' => now()->toDateTimeString(),
            ],
            // Add loans-specific data structure
        ];
    }

    private function generateAssetsOnlyData(\DateTime $start, \DateTime $end): array
    {
        // Implementation for assets-only data
        return [
            'report_info' => [
                'title' => 'Eksport Data Aset ICTServe',
                'frequency' => 'custom',
                'period' => ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')],
                'generated_at' => now()->toDateTimeString(),
            ],
            // Add assets-specific data structure
        ];
    }

    private function generateCrossModuleData(\DateTime $start, \DateTime $end): array
    {
        // Implementation for cross-module integration data
        return [
            'report_info' => [
                'title' => 'Eksport Data Integrasi Silang Modul ICTServe',
                'frequency' => 'custom',
                'period' => ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')],
                'generated_at' => now()->toDateTimeString(),
            ],
            // Add cross-module integration data structure
        ];
    }

    private function generateSampleData(): array
    {
        return [
            'report_info' => [
                'title' => 'Contoh Data ICTServe',
                'frequency' => 'sample',
                'period' => [
                    'start' => now()->subWeek()->format('Y-m-d'),
                    'end' => now()->format('Y-m-d'),
                ],
                'generated_at' => now()->toDateTimeString(),
            ],
            'executive_summary' => [
                'system_health' => [
                    'score' => 85.5,
                    'status' => 'good',
                    'description' => 'Sistem beroperasi dengan baik',
                ],
                'key_metrics' => [
                    'total_tickets' => 150,
                    'ticket_resolution_rate' => 87.5,
                    'total_loan_applications' => 45,
                    'loan_approval_rate' => 92.0,
                    'asset_utilization_rate' => 68.3,
                ],
            ],
            'unified_metrics' => [
                'helpdesk' => [
                    'total_tickets' => 150,
                    'resolved_tickets' => 131,
                    'pending_tickets' => 19,
                    'overdue_tickets' => 3,
                    'resolution_rate' => 87.5,
                    'avg_resolution_hours' => 24.5,
                ],
                'loans' => [
                    'total_applications' => 45,
                    'approved_applications' => 41,
                    'active_loans' => 28,
                    'overdue_loans' => 2,
                    'pending_approval' => 4,
                    'approval_rate' => 92.0,
                    'total_loan_value' => 125000.00,
                ],
                'assets' => [
                    'total_assets' => 320,
                    'available_assets' => 185,
                    'loaned_assets' => 108,
                    'maintenance_assets' => 15,
                    'retired_assets' => 12,
                    'utilization_rate' => 68.3,
                    'availability_rate' => 57.8,
                ],
            ],
        ];
    }
}
