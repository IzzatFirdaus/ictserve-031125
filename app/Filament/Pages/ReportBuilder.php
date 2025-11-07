<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ReportBuilderService;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

/**
 * Report Builder Interface
 *
 * Provides interface for building custom reports with module selection,
 * date range filtering, status filtering, and format selection.
 *
 * @trace Requirements 8.1
 */
class ReportBuilder extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.pages.report-builder';

    protected static ?string $title = 'Pembina Laporan';

    protected static ?string $navigationLabel = 'Pembina Laporan';

    protected static UnitEnum|string|null $navigationGroup = 'Reports & Analytics';

    protected static ?int $navigationSort = 1;

    public ?string $module = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public array $statuses = [];

    public ?string $format = 'pdf';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('module')
                    ->label('Modul')
                    ->options([
                        'helpdesk' => 'Helpdesk Tickets',
                        'loans' => 'Asset Loans',
                        'assets' => 'Asset Inventory',
                        'users' => 'User Management',
                        'unified' => 'Unified Analytics',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->statuses = []),

                DatePicker::make('startDate')
                    ->label('Tarikh Mula')
                    ->required()
                    ->default(now()->subMonth())
                    ->maxDate(now()),

                DatePicker::make('endDate')
                    ->label('Tarikh Akhir')
                    ->required()
                    ->default(now())
                    ->maxDate(now())
                    ->afterOrEqual('startDate'),

                Select::make('statuses')
                    ->label('Status')
                    ->options(function () {
                        return match ($this->module) {
                            'helpdesk' => [
                                'open' => 'Open',
                                'assigned' => 'Assigned',
                                'in_progress' => 'In Progress',
                                'pending_user' => 'Pending User',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ],
                            'loans' => [
                                'pending_approval' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'in_use' => 'In Use',
                                'return_due' => 'Return Due',
                                'overdue' => 'Overdue',
                                'completed' => 'Completed',
                            ],
                            'assets' => [
                                'available' => 'Available',
                                'on_loan' => 'On Loan',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired',
                            ],
                            'users' => [
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ],
                            default => [],
                        };
                    })
                    ->multiple()
                    ->visible(fn () => ! empty($this->module) && $this->module !== 'unified'),

                Select::make('format')
                    ->label('Format')
                    ->options([
                        'pdf' => 'PDF',
                        'csv' => 'CSV',
                        'excel' => 'Excel',
                    ])
                    ->required()
                    ->default('pdf'),
            ]);
    }

    public function generateReport(): void
    {
        $this->validate();

        $reportService = app(ReportBuilderService::class);

        try {
            $report = $reportService->generateReport([
                'module' => $this->module,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'statuses' => $this->statuses,
                'format' => $this->format,
                'user_id' => auth()->id(),
            ]);

            Notification::make()
                ->success()
                ->title('Laporan Berjaya Dijana')
                ->body("Laporan {$this->module} telah dijana dalam format {$this->format}")
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('Muat Turun')
                        ->url($report['download_url'])
                        ->openUrlInNewTab(),
                ])
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Ralat Menjana Laporan')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getPreviewData(): array
    {
        if (empty($this->module) || empty($this->startDate) || empty($this->endDate)) {
            return [];
        }

        $reportService = app(ReportBuilderService::class);

        return $reportService->getPreviewData([
            'module' => $this->module,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'statuses' => $this->statuses,
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }
}
