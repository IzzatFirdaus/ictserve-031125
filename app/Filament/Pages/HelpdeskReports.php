<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Exports\HelpdeskTicketExporter;
use App\Services\HelpdeskReportService;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Helpdesk Reports Page
 *
 * Unified dashboard for helpdesk reporting and analytics.
 * Provides ticket statistics, KPIs, and data export functionality.
 *
 * @see D03 Software Requirements Specification - Requirements 3.2, 3.5, 3.6, 8.1, 8.2, 8.5
 * @see D04 Software Design Document - Reporting Engine
 */
class HelpdeskReports extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public ?array $reportData = null;

    public ?\DateTime $startDate = null;

    public ?\DateTime $endDate = null;

    public static function getNavigationLabel(): string
    {
        return __('Reports & Analytics');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Helpdesk';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public function getTitle(): string|Htmlable
    {
        return __('Helpdesk Reports & Analytics');
    }

    public function getView(): string
    {
        return 'filament.pages.helpdesk-reports';
    }

    public function mount(): void
    {
        $this->data = [];
        $this->generateReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Filters')
                    ->description('Select date range for report generation')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->native(false)
                            ->maxDate(now()),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->native(false)
                            ->maxDate(now())
                            ->afterOrEqual('start_date'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateReport')
                ->label('Generate Report')
                ->icon('heroicon-o-arrow-path')
                ->action('generateReport'),
            ExportAction::make()
                ->label('Export Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->exporter(HelpdeskTicketExporter::class)
                ->modifyQueryUsing(function ($query) {
                    if ($this->startDate) {
                        $query->where('created_at', '>=', $this->startDate);
                    }
                    if ($this->endDate) {
                        $query->where('created_at', '<=', $this->endDate);
                    }

                    return $query;
                }),
        ];
    }

    public function generateReport(): void
    {
        // Convert string dates to DateTime objects if they exist
        $startDateValue = $this->data['start_date'] ?? null;
        $endDateValue = $this->data['end_date'] ?? null;

        $this->startDate = $startDateValue
            ? \Carbon\Carbon::parse($startDateValue)->startOfDay()
            : null;
        $this->endDate = $endDateValue
            ? \Carbon\Carbon::parse($endDateValue)->endOfDay()
            : null;

        $service = app(HelpdeskReportService::class);
        $this->reportData = $service->getComprehensiveReportData($this->startDate, $this->endDate);
    }

    public function getReportData(): ?array
    {
        return $this->reportData;
    }
}
