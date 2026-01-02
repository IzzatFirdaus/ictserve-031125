<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\ReportBuilderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Report Builder Page
 *
 * Custom report generation with filters and export options.
 *
 * @see D03-FR-006.1 Reporting requirements
 * @see D04 §7.1 Reporting architecture
 */
class ReportBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.pages.report-builder';

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    /** @var array<string, mixed> */
    public array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $reportData = null;

    public mixed $form = null;

    public bool $showPreview = false;

    /**
     * Control navigation visibility
     *
     * @phpstan-ignore method.notFound
     */
    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return Auth::check() && $user?->hasAnyRole(['admin', 'superuser']);
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.report_builder.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.reports');
    }

    public function getTitle(): string
    {
        return __('admin_pages.report_builder.title');
    }

    /**
     * Mount the page
     */
    public function mount(): void
    {
        $this->fillForm();
    }

    /**
     * Get header actions - removed to avoid duplicate CTA
     * Single "Jana Pratonton" button is in the Blade view
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Define the form
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('report_builder.config.heading'))
                    ->collapsible(false)
                    ->collapsed(false)
                    ->components([
                        Select::make('module')
                            ->label(__('report_builder.config.module'))
                            ->options([
                                'helpdesk' => __('report_builder.modules.helpdesk'),
                                'loans' => __('report_builder.modules.loans'),
                                'assets' => __('report_builder.modules.assets'),
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Grid::make(2)
                            ->components([
                                DatePicker::make('date_from')
                                    ->label(__('report_builder.config.date_from'))
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now()),

                                DatePicker::make('date_to')
                                    ->label(__('report_builder.config.date_to'))
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->afterOrEqual('date_from'),
                            ]),

                        Select::make('status')
                            ->label(__('report_builder.config.status'))
                            ->options(fn ($get) => match ($get('module')) {
                                'helpdesk' => __('report_builder.statuses.helpdesk'),
                                'loans' => __('report_builder.statuses.loans'),
                                'assets' => __('report_builder.statuses.assets'),
                                default => [],
                            })
                            ->multiple()
                            ->native(false)
                            ->visible(fn ($get) => ! empty($get('module'))),

                        Select::make('format')
                            ->label(__('report_builder.config.format'))
                            ->options([
                                'csv' => __('report_builder.formats.csv'),
                                'excel' => __('report_builder.formats.excel'),
                                'pdf' => __('report_builder.formats.pdf'),
                            ])
                            ->default('csv')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Pratonton')
                    ->components([
                        Placeholder::make('preview_area')
                            ->label('')
                            ->content(fn () => $this->showPreview && $this->reportData
                                ? new HtmlString('<div class="text-sm"><strong>Modul:</strong> '.ucfirst($this->reportData['module']).'<br><strong>Jumlah Rekod:</strong> '.$this->reportData['total_records'].'</div>')
                                : new HtmlString('<p class="text-gray-500">Tiada pratonton. Sila jana laporan.</p>')),
                    ])
                    ->visible(fn () => $this->showPreview),
            ])
            ->statePath('data');
    }

    /**
     * Generate report preview
     */
    public function generatePreview(): void
    {
        $data = $this->getFormState();

        if (empty($data['module'])) {
            Notification::make()
                ->warning()
                ->title(__('report_builder.messages.module_required'))
                ->body(__('report_builder.messages.module_required_body'))
                ->send();

            return;
        }

        $service = app(ReportBuilderService::class);

        $filters = [
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'status' => $data['status'] ?? [],
        ];

        $module = \is_string($data['module'] ?? null) ? $data['module'] : '';
        $this->reportData = $service->generateReport($module, $filters);
        $this->showPreview = true;

        Notification::make()
            ->success()
            ->title(__('report_builder.messages.report_generated'))
            ->body(__('report_builder.messages.records_found', ['count' => $this->reportData['total_records']]))
            ->send();
    }

    /**
     * Export report
     */
    public function exportReport(): void
    {
        if (! $this->reportData) {
            $this->generatePreview();
        }

        if (! $this->reportData) {
            return;
        }

        $data = $this->getFormState();
        $service = app(ReportBuilderService::class);

        $format = \is_string($data['format'] ?? null) ? $data['format'] : 'csv';
        $exportData = $service->formatForExport($this->reportData ?? [], $format);

        Notification::make()
            ->success()
            ->title(__('report_builder.messages.export_success'))
            ->body(__('report_builder.messages.export_success_body', ['filename' => $exportData['filename']]))
            ->send();

        // Note: Actual file download would be implemented here
        // For now, we just show a success notification
    }

    /**
     * @return array<string, mixed>
     */
    private function getFormState(): array
    {
        if (\is_object($this->form) && method_exists($this->form, 'getState')) {
            $state = $this->form->getState();

            return \is_array($state) ? $state : $this->data;
        }

        return $this->data;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function fillForm(?array $state = null): void
    {
        if (\is_object($this->form) && method_exists($this->form, 'fill')) {
            $this->form->fill($state ?? []);
        }
    }

    /**
     * Clear preview
     */
    public function clearPreview(): void
    {
        $this->reportData = null;
        $this->showPreview = false;
    }
}
