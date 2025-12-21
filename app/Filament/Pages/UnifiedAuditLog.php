<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Activity;
use App\Models\Audit;
use App\Models\User;
use Filament\Actions\Action as PageAction;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

/**
 * Unified Audit Log Page
 *
 * Combined view of owen-it audits (compliance) and spatie activity_log (operations).
 * Provides filtering by date, user, action type, entity with export to CSV/PDF.
 *
 * @version 1.0.0
 *
 * @since 2025-12-03
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: 7.2, 7.3
 *
 * @see D09 §4.6, §4.7 Dual Audit System
 * @see D03 SRS-ADM-005 Audit export
 * Traceability: Phase 7 - Task 28.1
 * WCAG 2.2 AA: Full keyboard navigation, ARIA labels, 4.5:1 contrast
 */
class UnifiedAuditLog extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected string $view = 'filament.pages.unified-audit-log';

    protected static string|UnitEnum|null $navigationGroup = 'Konfigurasi Sistem';

    protected static ?int $navigationSort = 10;

    /**
     * Active tab: 'all', 'compliance', 'activity'
     */
    public string $activeTab = 'all';

    /**
     * Filter values
     */
    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?int $userId = null;

    public ?string $actionType = null;

    public ?string $entityType = null;

    public ?string $source = null;

    /**
     * Statistics cache
     *
     * @var array<string, mixed>
     */
    public array $stats = [];

    /**
     * Mount the page
     */
    public function mount(): void
    {
        $this->loadStats();
    }

    /**
     * Get the navigation label
     */
    public static function getNavigationLabel(): string
    {
        return __('admin_pages.unified_audit_log.label');
    }

    /**
     * Get the page title
     */
    public function getTitle(): string
    {
        return __('Unified Audit Log');
    }

    /**
     * Get the page heading
     */
    public function getHeading(): string
    {
        return __('Unified Audit Log');
    }

    /**
     * Get the page subheading
     */
    public function getSubheading(): ?string
    {
        return __('Combined view of compliance audits and activity logs for governance review');
    }

    /**
     * Determine if the page should be registered in navigation
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperuser();
    }

    /**
     * Determine if the page can be accessed
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperuser();
    }

    /**
     * Load statistics
     */
    public function loadStats(): void
    {
        $this->stats = [
            'total_audits' => Audit::count(),
            'total_activities' => Activity::count(),
            'audits_today' => Audit::whereDate('created_at', today())->count(),
            'activities_today' => Activity::whereDate('created_at', today())->count(),
            'audits_last_7_days' => Audit::where('created_at', '>=', now()->subDays(7))->count(),
            'activities_last_7_days' => Activity::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Set active tab
     */
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    /**
     * Get the table query
     *
     * @return Builder<Audit>|Builder<Activity>
     */
    public function getTableQuery(): Builder
    {
        if ($this->activeTab === 'compliance') {
            return Audit::query()->with('user');
        }

        if ($this->activeTab === 'activity') {
            return Activity::query()->with('causer');
        }

        // For 'all' tab, we use Audit as base and will handle union in the view
        return Audit::query()->with('user');
    }

    /**
     * Configure the table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->recordActions([
                TableAction::make('view')
                    ->label(__('View Details'))
                    ->icon('heroicon-o-eye')
                    ->modalHeading(__('Audit Record Details'))
                    ->modalContent(fn ($record) => view('filament.pages.partials.audit-detail', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportCsv')
                        ->label(__('Export to CSV'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action($this->exportToCsv(...))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->poll('60s')
            ->striped();
    }

    /**
     * Get table columns based on active tab
     *
     * @return array<Tables\Columns\Column>
     */
    protected function getTableColumns(): array
    {
        $baseColumns = [
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Timestamp'))
                ->dateTime('d/m/Y H:i:s')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('source')
                ->label(__('Source'))
                ->badge()
                ->getStateUsing(fn ($record) => $record instanceof Audit ? 'Compliance' : 'Activity')
                ->color(fn (string $state): string => match ($state) {
                    'Compliance' => 'info',
                    'Activity' => 'success',
                    default => 'gray',
                })
                ->visible($this->activeTab === 'all'),
        ];

        if ($this->activeTab === 'compliance' || $this->activeTab === 'all') {
            $baseColumns = array_merge($baseColumns, [
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable()
                    ->sortable()
                    ->default('System')
                    ->visible($this->activeTab !== 'activity'),

                Tables\Columns\TextColumn::make('event')
                    ->label(__('Action'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'retrieved' => 'info',
                        default => 'gray',
                    })
                    ->searchable()
                    ->visible($this->activeTab !== 'activity'),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label(__('Entity Type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable()
                    ->sortable()
                    ->visible($this->activeTab !== 'activity'),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label(__('Entity ID'))
                    ->searchable()
                    ->sortable()
                    ->visible($this->activeTab !== 'activity'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('IP Address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible($this->activeTab !== 'activity'),
            ]);
        }

        if ($this->activeTab === 'activity') {
            $baseColumns = array_merge($baseColumns, [
                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('User'))
                    ->searchable()
                    ->sortable()
                    ->default('System'),

                Tables\Columns\TextColumn::make('log_name')
                    ->label(__('Log Category'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('event')
                    ->label(__('Event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'login' => 'info',
                        'logout' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('Subject Type'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'N/A')
                    ->searchable()
                    ->sortable(),
            ]);
        }

        return $baseColumns;
    }

    /**
     * Get table filters
     *
     * @return array<Tables\Filters\BaseFilter>
     */
    protected function getTableFilters(): array
    {
        $filters = [
            Tables\Filters\Filter::make('date_range')
                ->label(__('Date Range'))
                ->schema([
                    Forms\Components\DatePicker::make('date_from')
                        ->label(__('From Date')),
                    Forms\Components\DatePicker::make('date_to')
                        ->label(__('To Date')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['date_to'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['date_from'] ?? null) {
                        $indicators['date_from'] = __('From: :date', ['date' => $data['date_from']]);
                    }
                    if ($data['date_to'] ?? null) {
                        $indicators['date_to'] = __('To: :date', ['date' => $data['date_to']]);
                    }

                    return $indicators;
                }),

            Tables\Filters\SelectFilter::make('user_id')
                ->label(__('User'))
                ->options(fn () => User::pluck('name', 'id')->toArray())
                ->searchable()
                ->preload()
                ->visible($this->activeTab !== 'activity'),

            Tables\Filters\SelectFilter::make('causer_id')
                ->label(__('User'))
                ->options(fn () => User::pluck('name', 'id')->toArray())
                ->searchable()
                ->preload()
                ->visible($this->activeTab === 'activity'),
        ];

        if ($this->activeTab === 'compliance' || $this->activeTab === 'all') {
            $filters[] = Tables\Filters\SelectFilter::make('event')
                ->label(__('Action Type'))
                ->options([
                    'created' => __('Created'),
                    'updated' => __('Updated'),
                    'deleted' => __('Deleted'),
                    'retrieved' => __('Retrieved'),
                ])
                ->multiple()
                ->visible($this->activeTab !== 'activity');

            $filters[] = Tables\Filters\SelectFilter::make('auditable_type')
                ->label(__('Entity Type'))
                ->options([
                    User::class => __('User'),
                    \App\Models\HelpdeskTicket::class => __('Helpdesk Ticket'),
                    \App\Models\LoanApplication::class => __('Loan Application'),
                    \App\Models\Asset::class => __('Asset'),
                    \App\Models\Division::class => __('Division'),
                    \App\Models\Grade::class => __('Grade'),
                    \App\Models\LoanApproval::class => __('Loan Approval'),
                    \App\Models\LoanTransaction::class => __('Loan Transaction'),
                ])
                ->multiple()
                ->visible($this->activeTab !== 'activity');
        }

        if ($this->activeTab === 'activity') {
            $filters[] = Tables\Filters\SelectFilter::make('log_name')
                ->label(__('Log Category'))
                ->options([
                    'default' => __('Default'),
                    'auth' => __('Authentication'),
                    'helpdesk' => __('Helpdesk'),
                    'loan' => __('Loan'),
                    'admin' => __('Admin'),
                ])
                ->multiple();

            $filters[] = Tables\Filters\SelectFilter::make('event')
                ->label(__('Event Type'))
                ->options([
                    'created' => __('Created'),
                    'updated' => __('Updated'),
                    'deleted' => __('Deleted'),
                    'login' => __('Login'),
                    'logout' => __('Logout'),
                    'viewed' => __('Viewed'),
                ])
                ->multiple();
        }

        return $filters;
    }

    /**
     * Get header actions
     *
     * @return array<PageAction>
     */
    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('refresh')
                ->label(__('Refresh'))
                ->icon('heroicon-o-arrow-path')
                ->color(Color::Gray)
                ->action(function (): void {
                    $this->loadStats();
                    $this->resetTable();
                }),

            PageAction::make('exportAllCsv')
                ->label(__('Export All to CSV'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color(Color::Gray)
                ->action($this->exportAllToCsv(...)),

            PageAction::make('exportAllPdf')
                ->label(__('Export All to PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color(Color::Gray)
                ->action($this->exportAllToPdf(...))
                ->requiresConfirmation()
                ->modalHeading(__('Export to PDF'))
                ->modalDescription(__('This will generate a PDF report of all audit records matching current filters. This may take a while for large datasets.'))
                ->modalSubmitActionLabel(__('Generate PDF')),
        ];
    }

    /**
     * Export selected records to CSV
     */
    public function exportToCsv(Collection $records): StreamedResponse
    {
        return Response::streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');

            // Write BOM for Excel UTF-8 compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Write headers
            fputcsv($handle, [
                'ID',
                'Source',
                'Timestamp',
                'User',
                'Action/Event',
                'Entity Type',
                'Entity ID',
                'Description',
                'IP Address',
                'Changes/Properties',
            ]);

            foreach ($records as $record) {
                if ($record instanceof Audit) {
                    fputcsv($handle, [
                        $record->id,
                        'Compliance Audit',
                        $record->created_at->format('Y-m-d H:i:s'),
                        $record->user?->name ?? 'System',
                        $record->event,
                        class_basename($record->auditable_type),
                        $record->auditable_id,
                        '',
                        $record->ip_address ?? '',
                        json_encode(['old' => $record->old_values, 'new' => $record->new_values]),
                    ]);
                } else {
                    fputcsv($handle, [
                        $record->id,
                        'Activity Log',
                        $record->created_at->format('Y-m-d H:i:s'),
                        $record->causer?->name ?? 'System',
                        $record->event ?? $record->log_name,
                        $record->subject_type ? class_basename($record->subject_type) : '',
                        $record->subject_id ?? '',
                        $record->description,
                        '',
                        json_encode($record->properties ?? []),
                    ]);
                }
            }

            fclose($handle);
        }, 'audit-log-export-'.now()->format('Y-m-d-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export all records to CSV
     */
    public function exportAllToCsv(): StreamedResponse
    {
        $audits = Audit::with('user')->latest()->limit(10000)->get();
        $activities = Activity::with('causer')->latest()->limit(10000)->get();

        $records = $audits->concat($activities)->sortByDesc('created_at');

        return $this->exportToCsv($records);
    }

    /**
     * Export all records to PDF
     */
    public function exportAllToPdf(): StreamedResponse
    {
        $audits = Audit::with('user')->latest()->limit(1000)->get();
        $activities = Activity::with('causer')->latest()->limit(1000)->get();

        $records = $audits->concat($activities)->sortByDesc('created_at')->take(500);

        $html = view('filament.pages.exports.audit-log-pdf', [
            'records' => $records,
            'generatedAt' => now(),
            'filters' => [
                'tab' => $this->activeTab,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
            ],
        ])->render();

        // Use DomPDF or similar for PDF generation
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');

        return Response::streamDownload(
            fn () => print ($pdf->output()),
            'audit-log-report-'.now()->format('Y-m-d-His').'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Get unified audit records for 'all' tab
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getUnifiedRecords(): Collection
    {
        $audits = Audit::with('user')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn ($audit) => [
                'id' => $audit->id,
                'source' => 'compliance',
                'created_at' => $audit->created_at,
                'user_name' => $audit->user?->name ?? 'System',
                'action' => $audit->event,
                'entity_type' => class_basename($audit->auditable_type),
                'entity_id' => $audit->auditable_id,
                'description' => $audit->changes_summary ?? '',
                'ip_address' => $audit->ip_address,
                'record' => $audit,
            ]);

        $activities = Activity::with('causer')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn ($activity) => [
                'id' => $activity->id,
                'source' => 'activity',
                'created_at' => $activity->created_at,
                'user_name' => $activity->causer?->name ?? 'System',
                'action' => $activity->event ?? $activity->log_name,
                'entity_type' => $activity->subject_type ? class_basename($activity->subject_type) : '',
                'entity_id' => $activity->subject_id,
                'description' => $activity->description,
                'ip_address' => '',
                'record' => $activity,
            ]);

        return $audits->concat($activities)->sortByDesc('created_at')->take(100);
    }
}
