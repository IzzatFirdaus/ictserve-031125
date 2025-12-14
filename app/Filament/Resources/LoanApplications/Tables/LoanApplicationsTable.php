<?php

declare(strict_types=1);

namespace App\Filament\Resources\LoanApplications\Tables;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Loan Applications Table Configuration v3.6.0
 *
 * Provides comprehensive table configuration for loan application management
 * with overdue indicators, bulk approve, export functionality, and approval workflow.
 *
 * @see D03 Requirements 8.3, 8.5, 11.1, 11.2
 * @see D12 UI/UX Design Guide - WCAG 2.2 AA Compliance
 */
class LoanApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->deferFilters()
            ->striped()
            ->columns(self::getColumns())
            ->filters(self::getFilters())
            ->recordActions(self::getRecordActions())
            ->headerActions(self::getHeaderActions())
            ->groupedBulkActions(self::getBulkActions());
    }

    /**
     * @return array<int, Tables\Columns\Column>
     */
    private static function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('application_number')
                ->label(__('filament.labels.application_number'))
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('form_reference_code')
                ->label(__('filament.labels.form_reference'))
                ->default('PK.(S).MOTAC.07.(L3)')
                ->badge()
                ->color('info')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('applicant_name')
                ->label(__('filament.labels.applicant'))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('division.name_ms')
                ->label(__('filament.labels.division'))
                ->toggleable()
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->label(__('filament.labels.status'))
                ->badge()
                ->color(fn (LoanStatus|string|null $state) => $state instanceof LoanStatus ? $state->color() : 'primary')
                ->formatStateUsing(fn (LoanStatus|string|null $state): string => $state instanceof LoanStatus
                    ? $state->label()
                    : (is_string($state) ? trans('loan.status.'.$state) : '-'))
                ->sortable(),

            Tables\Columns\TextColumn::make('priority')
                ->label(__('filament.labels.priority'))
                ->badge()
                ->color(fn (LoanPriority|string|null $state) => $state instanceof LoanPriority ? $state->color() : 'secondary')
                ->formatStateUsing(fn (LoanPriority|string|null $state): string => $state instanceof LoanPriority
                    ? $state->label()
                    : (is_string($state) ? trans('loan.priority.'.$state) : '-')),

            Tables\Columns\TextColumn::make('loan_start_date')
                ->label(__('filament.labels.start_date'))
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('loan_end_date')
                ->label(__('filament.labels.end_date'))
                ->date()
                ->sortable(),

            Tables\Columns\TextColumn::make('overdue_status')
                ->label(__('filament.labels.overdue_status'))
                ->badge()
                ->state(function (LoanApplication $record) {
                    if (! $record->loan_end_date) {
                        return null;
                    }

                    $status = $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status;
                    if (! in_array($status, [LoanStatus::IN_USE->value, LoanStatus::RETURN_DUE->value])) {
                        return null;
                    }

                    $daysOverdue = now()->diffInDays($record->loan_end_date, false);

                    if ($daysOverdue < 0) {
                        return __('filament.status.overdue_days', ['days' => abs($daysOverdue)]);
                    }

                    if ($daysOverdue <= 2) {
                        return __('filament.status.due_soon', ['days' => $daysOverdue]);
                    }

                    return null;
                })
                ->color(function (LoanApplication $record) {
                    if (! $record->loan_end_date) {
                        return null;
                    }

                    $daysOverdue = now()->diffInDays($record->loan_end_date, false);

                    if ($daysOverdue < 0) {
                        return 'danger';
                    }

                    if ($daysOverdue <= 2) {
                        return 'warning';
                    }

                    return null;
                })
                ->icon(function (LoanApplication $record) {
                    if (! $record->loan_end_date) {
                        return null;
                    }

                    $daysOverdue = now()->diffInDays($record->loan_end_date, false);

                    if ($daysOverdue < 0) {
                        return 'heroicon-o-exclamation-triangle';
                    }

                    if ($daysOverdue <= 2) {
                        return 'heroicon-o-clock';
                    }

                    return null;
                })
                ->sortable(query: function (Builder $query, string $direction) {
                    return $query->orderByRaw("CASE
                        WHEN loan_end_date < NOW() THEN 1
                        WHEN DATEDIFF(loan_end_date, NOW()) <= 2 THEN 2
                        ELSE 3
                    END {$direction}");
                })
                ->toggleable(),

            Tables\Columns\TextColumn::make('total_value')
                ->label(__('filament.labels.total_value'))
                ->money('MYR')
                ->sortable(),

            Tables\Columns\IconColumn::make('maintenance_required')
                ->label(__('filament.labels.maintenance_required'))
                ->boolean()
                ->toggleable(),

            Tables\Columns\TextColumn::make('approval_status')
                ->label(__('filament.labels.approval_status'))
                ->badge()
                ->state(function (LoanApplication $record) {
                    if ($record->approved_at) {
                        return __('filament.status.approved');
                    }
                    if ($record->rejected_reason) {
                        return __('filament.status.rejected');
                    }
                    if ($record->approval_token) {
                        return __('filament.status.pending');
                    }

                    return __('filament.status.not_submitted');
                })
                ->color(function (LoanApplication $record) {
                    if ($record->approved_at) {
                        return 'success';
                    }
                    if ($record->rejected_reason) {
                        return 'danger';
                    }
                    if ($record->approval_token) {
                        return 'warning';
                    }

                    return 'gray';
                })
                ->icon(function (LoanApplication $record) {
                    if ($record->approved_at) {
                        return 'heroicon-o-check-circle';
                    }
                    if ($record->rejected_reason) {
                        return 'heroicon-o-x-circle';
                    }
                    if ($record->approval_token) {
                        return 'heroicon-o-clock';
                    }

                    return 'heroicon-o-minus-circle';
                })
                ->tooltip(function (LoanApplication $record) {
                    if ($record->approved_at) {
                        $method = match ($record->approval_method) {
                            'email' => __('filament.filters.email_approval'),
                            'portal' => __('filament.filters.portal_approval'),
                            default => '-',
                        };

                        return __('filament.tooltips.approval_approved', [
                            'date' => $record->approved_at->format('d M Y h:i A'),
                            'approver' => $record->approved_by_name,
                            'method' => $method,
                        ]);
                    }
                    if ($record->rejected_reason) {
                        return __('filament.tooltips.approval_rejected', ['reason' => $record->rejected_reason]);
                    }
                    if ($record->approval_token) {
                        return __('filament.tooltips.approval_pending', [
                            'email' => $record->approver_email,
                            'expires' => $record->approval_token_expires_at?->format('d M Y h:i A') ?? '-',
                        ]);
                    }

                    return __('filament.tooltips.approval_not_submitted');
                })
                ->toggleable(),

            Tables\Columns\TextColumn::make('submission_type')
                ->label(__('filament.labels.submission_type'))
                ->badge()
                ->state(fn (LoanApplication $record) => $record->user_id
                    ? __('filament.filters.authenticated_submission')
                    : __('filament.filters.guest_submission'))
                ->color(fn (LoanApplication $record) => $record->user_id ? 'success' : 'warning')
                ->icon(fn (LoanApplication $record) => $record->user_id ? 'heroicon-o-user-circle' : 'heroicon-o-user')
                ->toggleable(),
        ];
    }

    /**
     * @return array<int, Tables\Filters\BaseFilter>
     */
    private static function getFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('status')
                ->label(__('filament.labels.status'))
                ->options(self::enumOptions(LoanStatus::cases()))
                ->multiple()
                ->searchable(),

            Tables\Filters\SelectFilter::make('priority')
                ->label(__('filament.labels.priority'))
                ->options(self::enumOptions(LoanPriority::cases()))
                ->multiple()
                ->searchable(),

            Tables\Filters\SelectFilter::make('division_id')
                ->relationship('division', 'name_ms')
                ->label(__('filament.labels.division'))
                ->searchable()
                ->preload()
                ->multiple(),

            Tables\Filters\Filter::make('created_at')
                ->schema([
                    DatePicker::make('created_from')
                        ->label(__('filament.labels.created_from'))
                        ->placeholder(__('filament.date_filters.select_start_date')),
                    DatePicker::make('created_until')
                        ->label(__('filament.labels.created_until'))
                        ->placeholder(__('filament.date_filters.select_end_date')),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['created_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['created_until'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['created_from'] ?? null) {
                        $indicators[] = __('filament.date_filters.from_date', ['date' => date('d M Y', strtotime((string) $data['created_from']))]);
                    }

                    if ($data['created_until'] ?? null) {
                        $indicators[] = __('filament.date_filters.until_date', ['date' => date('d M Y', strtotime((string) $data['created_until']))]);
                    }

                    return $indicators;
                }),

            Tables\Filters\Filter::make('pending_approval')
                ->label(__('filament.filters.pending_approval'))
                ->query(fn ($query) => $query->whereIn('status', [
                    LoanStatus::SUBMITTED->value,
                    LoanStatus::UNDER_REVIEW->value,
                ]))
                ->toggle()
                ->indicator(__('filament.filters.approval_indicator')),

            Tables\Filters\Filter::make('approved')
                ->label(__('filament.filters.approved'))
                ->query(fn ($query) => $query->where('status', LoanStatus::APPROVED->value))
                ->toggle(),

            Tables\Filters\Filter::make('overdue')
                ->label(__('filament.filters.overdue'))
                ->query(
                    fn ($query) => $query
                        ->whereIn('status', [
                            LoanStatus::IN_USE->value,
                            LoanStatus::RETURN_DUE->value,
                        ])
                        ->whereDate('loan_end_date', '<', now()->toDateString())
                )
                ->toggle()
                ->indicator(__('filament.filters.overdue_indicator')),

            Tables\Filters\SelectFilter::make('submission_type')
                ->label(__('filament.labels.submission_type_filter'))
                ->options([
                    'guest' => __('filament.filters.guest_submission'),
                    'authenticated' => __('filament.filters.authenticated_submission'),
                ])
                ->query(function (Builder $query, array $data) {
                    if (($data['value'] ?? null) === 'guest') {
                        return $query->whereNull('user_id');
                    }
                    if (($data['value'] ?? null) === 'authenticated') {
                        return $query->whereNotNull('user_id');
                    }

                    return $query;
                }),

            Tables\Filters\SelectFilter::make('approval_method')
                ->label(__('filament.labels.approval_method'))
                ->options([
                    'email' => __('filament.filters.email_approval'),
                    'portal' => __('filament.filters.portal_approval'),
                ])
                ->searchable(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    private static function getRecordActions(): array
    {
        return [
            ViewAction::make()->label(__('filament.actions.view')),
            EditAction::make()->label(__('filament.actions.edit')),
            Action::make('approve')
                ->label(__('filament.actions.approve'))
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(function (LoanApplication $record): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    return $user?->can('approve', $record) ?? false;
                })
                ->schema([
                    Textarea::make('remarks')
                        ->label(__('filament.actions.approval_remarks'))
                        ->maxLength(500),
                ])
                ->action(function (LoanApplication $record, array $data) {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    abort_unless($user?->can('approve', $record) ?? false, 403);

                    $record->update([
                        'status' => LoanStatus::APPROVED,
                        'approved_at' => now(),
                        'rejected_reason' => null,
                        'special_instructions' => $data['remarks'] ?? $record->special_instructions,
                    ]);
                }),
            Action::make('decline')
                ->label(__('filament.actions.decline'))
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(function (LoanApplication $record): bool {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    return $user?->can('approve', $record) ?? false;
                })
                ->schema([
                    Textarea::make('reason')
                        ->label(__('filament.actions.rejection_reason'))
                        ->required()
                        ->maxLength(500),
                ])
                ->action(function (LoanApplication $record, array $data): void {
                    /** @var \App\Models\User|null $user */
                    $user = Auth::user();

                    abort_unless($user?->can('approve', $record) ?? false, 403);

                    $record->update([
                        'status' => LoanStatus::REJECTED,
                        'rejected_reason' => (string) $data['reason'],
                        'approval_token' => null,
                        'approval_token_expires_at' => null,
                    ]);
                }),
            Action::make('extend')
                ->label(__('filament.actions.extend'))
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->visible(fn (LoanApplication $record) => in_array(
                    $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                    [
                        LoanStatus::IN_USE->value,
                        LoanStatus::RETURN_DUE->value,
                    ]
                ))
                ->schema([
                    DatePicker::make('loan_end_date')
                        ->label(__('filament.actions.new_date'))
                        ->required()
                        ->minDate(fn () => now()),
                    Textarea::make('special_instructions')
                        ->label(__('filament.actions.instructions'))
                        ->maxLength(500),
                ])
                ->action(fn (LoanApplication $record, array $data) => $record->update([
                    'loan_end_date' => $data['loan_end_date'],
                    'special_instructions' => $data['special_instructions'],
                    'status' => LoanStatus::RETURN_DUE,
                ])),
            Action::make('exportPdf')
                ->label(__('filament.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function (LoanApplication $record) {
                    return app(\App\Services\LoanApplicationPdfExporter::class)->exportSingle($record);
                }),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    private static function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ExportAction::make()
                ->exporter(\App\Filament\Exports\LoanApplicationExporter::class)
                ->label(__('filament.actions.export_excel'))
                ->icon('heroicon-o-table-cells')
                ->color('success'),
            Action::make('exportPdfReport')
                ->label(__('filament.actions.export_report'))
                ->icon('heroicon-o-document-chart-bar')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Eksport Laporan PDF')
                ->modalDescription('Ini akan menjana laporan PDF dengan statistik untuk semua permohonan pinjaman.')
                ->action(function () {
                    $applications = LoanApplication::with(['division'])->get();

                    return app(\App\Services\LoanApplicationPdfExporter::class)->exportReport($applications);
                }),
        ];
    }

    /**
     * @return array<int, BulkAction|BulkActionGroup>
     */
    private static function getBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                BulkAction::make('approve')
                    ->label(__('filament.actions.approve'))
                    ->color('success')
                    ->authorizeIndividualRecords('approve')
                    ->action(function (BulkAction $action, Collection $records): void {
                        foreach ($records as $application) {
                            if (! $application instanceof LoanApplication) {
                                continue;
                            }

                            $success = $application->update([
                                'status' => LoanStatus::APPROVED,
                                'approved_at' => now(),
                                'rejected_reason' => null,
                            ]);

                            if (! $success) {
                                $action->reportBulkProcessingFailure(
                                    'approve_failed',
                                    message: fn (int $failureCount, int $totalCount): string => match (true) {
                                        ($failureCount === 1) && ($totalCount === 1) => 'Permohonan gagal diluluskan.',
                                        $failureCount === $totalCount => 'Semua permohonan gagal diluluskan.',
                                        $failureCount === 1 => 'Satu daripada permohonan dipilih gagal diluluskan.',
                                        default => "{$failureCount} daripada {$totalCount} permohonan dipilih gagal diluluskan.",
                                    },
                                );
                            }
                        }
                    })
                    ->successNotificationTitle('Permohonan diluluskan')
                    ->failureNotificationTitle(fn (int $successCount, int $totalCount): string => $successCount
                        ? "{$successCount} daripada {$totalCount} permohonan diluluskan"
                        : 'Tiada permohonan berjaya diluluskan'),
                BulkAction::make('decline')
                    ->label(__('filament.actions.decline'))
                    ->color('danger')
                    ->authorizeIndividualRecords('approve')
                    ->schema([
                        Textarea::make('reason')
                            ->label(__('filament.actions.reason'))
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (BulkAction $action, Collection $records, array $data): void {
                        foreach ($records as $application) {
                            if (! $application instanceof LoanApplication) {
                                continue;
                            }

                            $success = $application->update([
                                'status' => LoanStatus::REJECTED,
                                'rejected_reason' => (string) $data['reason'],
                                'approval_token' => null,
                                'approval_token_expires_at' => null,
                            ]);

                            if (! $success) {
                                $action->reportBulkProcessingFailure(
                                    'decline_failed',
                                    message: fn (int $failureCount, int $totalCount): string => match (true) {
                                        ($failureCount === 1) && ($totalCount === 1) => 'Permohonan gagal ditolak.',
                                        $failureCount === $totalCount => 'Semua permohonan gagal ditolak.',
                                        $failureCount === 1 => 'Satu daripada permohonan dipilih gagal ditolak.',
                                        default => "{$failureCount} daripada {$totalCount} permohonan dipilih gagal ditolak.",
                                    },
                                );
                            }
                        }
                    })
                    ->successNotificationTitle('Permohonan ditolak')
                    ->failureNotificationTitle(fn (int $successCount, int $totalCount): string => $successCount
                        ? "{$successCount} daripada {$totalCount} permohonan ditolak"
                        : 'Tiada permohonan berjaya ditolak'),
                DeleteBulkAction::make()->label(__('filament.actions.delete_selected')),
                RestoreBulkAction::make()->label(__('filament.actions.restore_selected')),
            ]),
        ];
    }

    /**
     * @param  array<int, LoanStatus|LoanPriority>  $cases
     * @return array<string, string>
     */
    private static function enumOptions(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn (LoanStatus|LoanPriority $case): array => [$case->value => $case->label()])
            ->all();
    }
}
