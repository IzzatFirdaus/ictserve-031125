<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Asset Lifecycle Report Page
 *
 * Provides comprehensive asset lifecycle analytics including:
 * - Asset acquisition timelines
 * - Maintenance history patterns
 * - Utilization metrics over time
 * - End-of-life predictions
 *
 * @trace D03-FR-003 (Asset tracking and reporting)
 * @trace D04-§4.3 (Reporting and analytics architecture)
 * @trace D12-§8 (Report UI/UX patterns)
 */
class AssetLifecycleReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.asset-lifecycle-report';

    /** @var array<string, mixed> */
    public array $filters = [];

    public bool $hasResults = false;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.asset_lifecycle_report.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_pages.asset_lifecycle_report.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user !== null && $user->hasAdminAccess();
    }

    public function mount(): void
    {
        $this->filters = [
            'start_date' => null,
            'end_date' => null,
            'category_id' => null,
            'status' => null,
            'lifecycle_stage' => null,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin_pages.asset_lifecycle_report.filters'))
                    ->description(__('admin_pages.asset_lifecycle_report.filters_description'))
                    ->schema([
                        DatePicker::make('filters.start_date')
                            ->label(__('admin_pages.asset_lifecycle_report.start_date'))
                            ->native(false)
                            ->maxDate(now()),
                        DatePicker::make('filters.end_date')
                            ->label(__('admin_pages.asset_lifecycle_report.end_date'))
                            ->native(false)
                            ->maxDate(now())
                            ->afterOrEqual('filters.start_date'),
                        Select::make('filters.category_id')
                            ->label(__('admin_pages.asset_lifecycle_report.category'))
                            ->options(fn () => AssetCategory::query()->pluck('name_ms', 'id'))
                            ->searchable()
                            ->preload(),
                        Select::make('filters.status')
                            ->label(__('admin_pages.asset_lifecycle_report.status'))
                            ->options(collect(AssetStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                            ->searchable(),
                        Select::make('filters.lifecycle_stage')
                            ->label(__('admin_pages.asset_lifecycle_report.lifecycle_stage'))
                            ->options([
                                'new' => __('admin_pages.asset_lifecycle_report.stage_new'),
                                'active' => __('admin_pages.asset_lifecycle_report.stage_active'),
                                'maintenance' => __('admin_pages.asset_lifecycle_report.stage_maintenance'),
                                'end_of_life' => __('admin_pages.asset_lifecycle_report.stage_end_of_life'),
                            ])
                            ->searchable(),
                    ])
                    ->columns(3),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('asset_tag')
                    ->label(__('admin_pages.asset_lifecycle_report.asset_tag'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('admin_pages.asset_lifecycle_report.asset_name'))
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name),
                TextColumn::make('category.name_ms')
                    ->label(__('admin_pages.asset_lifecycle_report.category'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin_pages.asset_lifecycle_report.status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'available' => 'success',
                        'on_loan' => 'warning',
                        'under_maintenance' => 'danger',
                        'disposed' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('acquisition_date')
                    ->label(__('admin_pages.asset_lifecycle_report.acquisition_date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('last_maintenance_date')
                    ->label(__('admin_pages.asset_lifecycle_report.last_maintenance'))
                    ->date('d M Y')
                    ->placeholder('-'),
                TextColumn::make('next_maintenance_date')
                    ->label(__('admin_pages.asset_lifecycle_report.next_maintenance'))
                    ->date('d M Y')
                    ->placeholder('-')
                    ->color(fn ($record) => $record->next_maintenance_date && now()->greaterThan($record->next_maintenance_date) ? 'danger' : 'success'),
                TextColumn::make('lifecycle_stage')
                    ->label(__('admin_pages.asset_lifecycle_report.lifecycle_stage'))
                    ->state(fn ($record) => $this->calculateLifecycleStage($record))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'new' => 'info',
                        'active' => 'success',
                        'maintenance' => 'warning',
                        'end_of_life' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('acquisition_date', 'desc')
            ->emptyStateHeading(__('admin_pages.asset_lifecycle_report.empty_state'))
            ->emptyStateDescription(__('admin_pages.asset_lifecycle_report.empty_state_description'))
            ->emptyStateIcon('heroicon-o-document-magnifying-glass');
    }

    protected function getTableQuery(): Builder
    {
        $query = Asset::query()->with('category');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('acquisition_date', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('acquisition_date', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (! empty($this->filters['lifecycle_stage'])) {
            $query = $this->applyLifecycleStageFilter($query, $this->filters['lifecycle_stage']);
        }

        return $query;
    }

    protected function applyLifecycleStageFilter(Builder $query, string $stage): Builder
    {
        $now = now();

        return match ($stage) {
            'new' => $query->where('acquisition_date', '>=', $now->copy()->subMonths(6)),
            'active' => $query->where('acquisition_date', '<', $now->copy()->subMonths(6))
                ->where(function ($q) use ($now) {
                    $q->whereNull('next_maintenance_date')
                        ->orWhere('next_maintenance_date', '>', $now);
                }),
            'maintenance' => $query->whereNotNull('next_maintenance_date')
                ->where('next_maintenance_date', '<=', $now),
            'end_of_life' => $query->where('acquisition_date', '<', $now->copy()->subYears(5)),
            default => $query,
        };
    }

    protected function calculateLifecycleStage(Asset $asset): string
    {
        $now = now();
        $acquisitionDate = $asset->acquisition_date;

        if ($acquisitionDate && $acquisitionDate->greaterThan($now->copy()->subMonths(6))) {
            return 'new';
        }

        if ($asset->next_maintenance_date && $now->greaterThan($asset->next_maintenance_date)) {
            return 'maintenance';
        }

        if ($acquisitionDate && $acquisitionDate->lessThan($now->copy()->subYears(5))) {
            return 'end_of_life';
        }

        return 'active';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateReport')
                ->label(__('admin_pages.asset_lifecycle_report.generate_report'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->hasResults = true),
            Action::make('exportCsv')
                ->label(__('filament.actions.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportReport('csv')),
            Action::make('exportExcel')
                ->label(__('filament.actions.export_excel'))
                ->icon('heroicon-o-table-cells')
                ->action(fn () => $this->exportReport('xlsx')),
        ];
    }

    public function exportReport(string $format): mixed
    {
        $assets = $this->getTableQuery()->get();

        if ($assets->isEmpty()) {
            return null;
        }

        $rows = $assets->map(fn (Asset $asset) => [
            __('admin_pages.asset_lifecycle_report.asset_tag') => $asset->asset_tag,
            __('admin_pages.asset_lifecycle_report.asset_name') => $asset->name,
            __('admin_pages.asset_lifecycle_report.category') => $asset->category?->name_ms ?? '-',
            __('admin_pages.asset_lifecycle_report.status') => $asset->status,
            __('admin_pages.asset_lifecycle_report.acquisition_date') => $asset->acquisition_date?->format('Y-m-d') ?? '-',
            __('admin_pages.asset_lifecycle_report.last_maintenance') => $asset->last_maintenance_date?->format('Y-m-d') ?? '-',
            __('admin_pages.asset_lifecycle_report.next_maintenance') => $asset->next_maintenance_date?->format('Y-m-d') ?? '-',
            __('admin_pages.asset_lifecycle_report.lifecycle_stage') => $this->calculateLifecycleStage($asset),
        ]);

        $export = new class($rows) implements FromCollection, WithHeadings
        {
            public function __construct(private readonly \Illuminate\Support\Collection $rows) {}

            public function collection(): \Illuminate\Support\Collection
            {
                return $this->rows;
            }

            public function headings(): array
            {
                return $this->rows->isNotEmpty() ? \array_keys($this->rows->first()) : [];
            }
        };

        $filename = 'asset-lifecycle-report-'.now()->format('Y-m-d-His').'.'.$format;
        $writerType = $format === 'xlsx' ? ExcelFormat::XLSX : ExcelFormat::CSV;

        return Excel::download($export, $filename, $writerType);
    }

    /**
     * @return array<string, mixed>
     */
    public function getSummaryKPIs(): array
    {
        $query = $this->getTableQuery();

        return [
            'total_assets' => $query->count(),
            'new_assets' => $query->clone()->where('acquisition_date', '>=', now()->subMonths(6))->count(),
            'maintenance_due' => $query->clone()->whereNotNull('next_maintenance_date')
                ->where('next_maintenance_date', '<=', now())->count(),
            'end_of_life' => $query->clone()->where('acquisition_date', '<', now()->subYears(5))->count(),
        ];
    }
}
