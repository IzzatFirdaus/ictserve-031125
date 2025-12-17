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
        return __('admin_pages.report_builder.group');
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
     * Get header actions
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Jana Pratonton')
                ->icon('heroicon-o-eye')
                ->action('generatePreview'),
        ];
    }

    /**
     * Define the form
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konfigurasi Laporan')
                    ->collapsible(false)
                    ->collapsed(false)
                    ->components([
                        Select::make('module')
                            ->label('Modul')
                            ->options([
                                'helpdesk' => 'Tiket Helpdesk',
                                'loans' => 'Permohonan Pinjaman',
                                'assets' => 'Aset',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Grid::make(2)
                            ->components([
                                DatePicker::make('date_from')
                                    ->label('Tarikh Dari')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now()),

                                DatePicker::make('date_to')
                                    ->label('Tarikh Hingga')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now())
                                    ->afterOrEqual('date_from'),
                            ]),

                        Select::make('status')
                            ->label('Status')
                            ->options(function ($get) {
                                return match ($get('module')) {
                                    'helpdesk' => [
                                        'open' => 'Terbuka',
                                        'assigned' => 'Ditugaskan',
                                        'in_progress' => 'Dalam Proses',
                                        'resolved' => 'Diselesaikan',
                                        'closed' => 'Ditutup',
                                    ],
                                    'loans' => [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Diluluskan',
                                        'in_use' => 'Sedang Digunakan',
                                        'completed' => 'Selesai',
                                    ],
                                    'assets' => [
                                        'available' => 'Tersedia',
                                        'on_loan' => 'Dipinjam',
                                        'maintenance' => 'Penyelenggaraan',
                                        'retired' => 'Bersara',
                                    ],
                                    default => [],
                                };
                            })
                            ->multiple()
                            ->native(false)
                            ->visible(fn ($get) => ! empty($get('module'))),

                        Select::make('format')
                            ->label('Format Export')
                            ->options([
                                'csv' => 'CSV',
                                'excel' => 'Excel (XLSX)',
                                'pdf' => 'PDF',
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
                ->title('Modul Diperlukan')
                ->body('Sila pilih modul untuk menjana laporan.')
                ->send();

            return;
        }

        $service = app(ReportBuilderService::class);

        $filters = [
            'date_from' => $data['date_from'] ?? null,
            'date_to' => $data['date_to'] ?? null,
            'status' => $data['status'] ?? [],
        ];

        $module = is_string($data['module'] ?? null) ? $data['module'] : '';
        $this->reportData = $service->generateReport($module, $filters);
        $this->showPreview = true;

        Notification::make()
            ->success()
            ->title('Laporan Dijana')
            ->body("Ditemui {$this->reportData['total_records']} rekod.")
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

        $format = is_string($data['format'] ?? null) ? $data['format'] : 'csv';
        $exportData = $service->formatForExport($this->reportData ?? [], $format);

        Notification::make()
            ->success()
            ->title('Export Berjaya')
            ->body("Laporan {$exportData['filename']} telah dijana.")
            ->send();

        // Note: Actual file download would be implemented here
        // For now, we just show a success notification
    }

    /**
     * @return array<string, mixed>
     */
    private function getFormState(): array
    {
        if (is_object($this->form) && method_exists($this->form, 'getState')) {
            $state = $this->form->getState();

            return is_array($state) ? $state : $this->data;
        }

        return $this->data;
    }

    /**
     * @param  array<string, mixed>|null  $state
     */
    private function fillForm(?array $state = null): void
    {
        if (is_object($this->form) && method_exists($this->form, 'fill')) {
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
