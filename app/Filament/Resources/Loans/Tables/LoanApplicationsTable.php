<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Tables;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\Actions\ProcessIssuanceAction;
use App\Filament\Resources\Loans\Actions\ProcessReturnAction;
use App\Filament\Resources\Loans\LoanApplicationResource;
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
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('application_number')
                    ->label(__('filament.labels.application_number'))
                    ->searchable()
                    ->sortable(),

                // Form Reference Code - Requirement 24.2
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

                // Overdue indicator column with visual badges
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
                            $absDays = abs($daysOverdue);

                            return __('filament.status.overdue_days', ['days' => $absDays]);
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

                // Enhanced approval workflow visualization
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
                            return __('filament.tooltips.approval_approved', [
                                'date' => $record->approved_at->format('d M Y h:i A'),
                                'approver' => $record->approved_by_name,
                                'method' => ucfirst($record->approval_method ?? 'N/A'),
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

                // Submission type badge
                Tables\Columns\TextColumn::make('submission_type')
                    ->label(__('filament.labels.submission_type'))
                    ->badge()
                    ->state(fn (LoanApplication $record) => $record->user_id
                        ? __('filament.filters.authenticated_submission')
                        : __('filament.filters.guest_submission'))
                    ->color(fn (LoanApplication $record) => $record->user_id ? 'success' : 'warning')
                    ->icon(fn (LoanApplication $record) => $record->user_id ? 'heroicon-o-user-circle' : 'heroicon-o-user')
                    ->toggleable(),

                // Responsible Officer indicator - Requirement 25.5
                Tables\Columns\TextColumn::make('responsible_officer_status')
                    ->label(__('filament.labels.responsible_officer'))
                    ->badge()
                    ->state(function (LoanApplication $record): string {
                        if ($record->is_applicant_responsible) {
                            return __('filament.status.applicant_is_responsible');
                        }

                        return $record->responsible_officer_name ?? __('filament.status.different_officer');
                    })
                    ->color(fn (LoanApplication $record): string => $record->is_applicant_responsible ? 'gray' : 'info')
                    ->icon(fn (LoanApplication $record): string => $record->is_applicant_responsible
                        ? 'heroicon-o-user'
                        : 'heroicon-o-user-group')
                    ->tooltip(function (LoanApplication $record): ?string {
                        if ($record->is_applicant_responsible) {
                            return __('filament.tooltips.applicant_responsible');
                        }

                        return __('filament.tooltips.different_responsible_officer', [
                            'name' => $record->responsible_officer_name,
                            'grade' => $record->responsible_officer_grade,
                        ]);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Enhanced filter organization
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

                // Date range filter
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
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('filament.date_filters.from_date', ['date' => date('d M Y', strtotime($data['created_from']))]);
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('filament.date_filters.until_date', ['date' => date('d M Y', strtotime($data['created_until']))]);
                        }

                        return $indicators;
                    }),

                // Asset type filter (based on loan items)
                Tables\Filters\Filter::make('asset_type')
                    ->label(__('filament.labels.asset_type'))
                    ->schema([
                        \Filament\Forms\Components\Select::make('category')
                            ->label(__('filament.labels.category'))
                            ->options([
                                'computer' => __('filament.asset_categories.computer'),
                                'laptop' => __('filament.asset_categories.laptop'),
                                'printer' => __('filament.asset_categories.printer'),
                                'projector' => __('filament.asset_categories.projector'),
                                'camera' => __('filament.asset_categories.camera'),
                                'other' => __('filament.asset_categories.other'),
                            ])
                            ->searchable()
                            ->multiple(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $categoriesSelected = (array) ($data['category'] ?? []);
                        if (! empty($categoriesSelected)) {
                            return $query->whereHas('loanItems.asset', function (Builder $query) use ($categoriesSelected) {
                                $query->whereIn('category', $categoriesSelected);
                            });
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $categoriesSelected = (array) ($data['category'] ?? []);
                        if (! empty($categoriesSelected)) {
                            $categories = collect($categoriesSelected)->map(function (string $cat): string {
                                return match ($cat) {
                                    'computer' => __('filament.asset_categories.computer'),
                                    'laptop' => __('filament.asset_categories.laptop'),
                                    'printer' => __('filament.asset_categories.printer'),
                                    'projector' => __('filament.asset_categories.projector'),
                                    'camera' => __('filament.asset_categories.camera'),
                                    'other' => __('filament.asset_categories.other'),
                                    default => $cat,
                                };
                            })->join(', ');

                            return [__('filament.date_filters.category_filter', ['categories' => $categories])];
                        }

                        return [];
                    }),

                // Enhanced approval status filters
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

                // Submission type filter (guest vs authenticated)
                Tables\Filters\SelectFilter::make('submission_type')
                    ->label(__('filament.labels.submission_type_filter'))
                    ->options([
                        'guest' => __('filament.filters.guest_submission'),
                        'authenticated' => __('filament.filters.authenticated_submission'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'guest') {
                            return $query->whereNull('user_id');
                        }
                        if ($data['value'] === 'authenticated') {
                            return $query->whereNotNull('user_id');
                        }

                        return $query;
                    }),

                // Approval method filter
                Tables\Filters\SelectFilter::make('approval_method')
                    ->label(__('filament.labels.approval_method'))
                    ->options([
                        'email' => __('filament.filters.email_approval'),
                        'portal' => __('filament.filters.portal_approval'),
                    ])
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ProcessIssuanceAction::make(),
                ProcessReturnAction::make(),
                Action::make('sendApproval')
                    ->label(__('filament.actions.send_for_approval'))
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (LoanApplication $record) => in_array(
                        $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                        [
                            LoanStatus::SUBMITTED->value,
                            LoanStatus::UNDER_REVIEW->value,
                        ]
                    ) && empty($record->approval_token))
                    ->requiresConfirmation()
                    ->action(fn (LoanApplication $record) => LoanApplicationResource::sendForApproval($record)),
                Action::make('approve')
                    ->label(__('filament.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (LoanApplication $record) => in_array(
                        $record->status instanceof LoanStatus ? $record->status->value : (string) $record->status,
                        [
                            LoanStatus::SUBMITTED->value,
                            LoanStatus::UNDER_REVIEW->value,
                            LoanStatus::PENDING_INFO->value,
                        ]
                    ))
                    ->schema([
                        Textarea::make('remarks')
                            ->label(__('filament.actions.approval_remarks'))
                            ->maxLength(500),
                    ])
                    ->action(function (LoanApplication $record, array $data) {
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
                    ->visible(fn (LoanApplication $record) => $record->status instanceof LoanStatus
                        ? ! $record->status->isTerminal()
                        : ! in_array((string) $record->status, [
                            LoanStatus::REJECTED->value,
                            LoanStatus::COMPLETED->value,
                        ]))
                    ->schema([
                        Textarea::make('reason')
                            ->label(__('filament.actions.rejection_reason'))
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(fn (LoanApplication $record, array $data) => $record->update([
                        'status' => LoanStatus::REJECTED,
                        'rejected_reason' => $data['reason'],
                        'approval_token' => null,
                        'approval_token_expires_at' => null,
                    ])),
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
                            ->minDate(fn (callable $get) => now()),
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
            ])
            ->headerActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ExportAction::make('exportExcel')
                        ->exporter(\App\Filament\Exports\LoanApplicationExporter::class)
                        ->label(__('filament.actions.export_excel'))
                        ->icon('heroicon-o-table-cells')
                        ->color('success'),
                    Action::make('exportPdfReport')
                        ->label(__('filament.actions.export_report'))
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading(__('filament.actions.export_report'))
                        ->modalDescription(__('filament.actions.export_report_description'))
                        ->action(function () {
                            $applications = LoanApplication::with(['division'])->get();

                            return app(\App\Services\LoanApplicationPdfExporter::class)->exportReport($applications);
                        }),
                ])
                    ->label(__('filament.actions.export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->button(),
            ])
            ->groupedBulkActions([
                BulkActionGroup::make([
                    // Bulk approve action exposed as 'approve' to satisfy tests expecting callTableBulkAction('approve')
                    BulkAction::make('approve')
                        ->label(__('filament.actions.approve'))
                        ->color('success')
                        ->action(function (Collection $records): void {
                            foreach ($records as $application) {
                                if (! $application instanceof LoanApplication) {
                                    continue;
                                }

                                $application->update([
                                    'status' => LoanStatus::APPROVED,
                                    'approved_at' => now(),
                                    'rejected_reason' => null,
                                ]);
                            }
                        }),
                    BulkAction::make('decline')
                        ->label(__('filament.actions.decline'))
                        ->color('danger')
                        ->schema([
                            Textarea::make('reason')
                                ->label(__('filament.actions.reason'))
                                ->required()
                                ->maxLength(500),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $application) {
                                if (! $application instanceof LoanApplication) {
                                    continue;
                                }

                                $application->update([
                                    'status' => LoanStatus::REJECTED,
                                    'rejected_reason' => $data['reason'],
                                    'approval_token' => null,
                                    'approval_token_expires_at' => null,
                                ]);
                            }
                        }),
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @param  array<int, LoanStatus|LoanPriority>  $cases
     * @return array<string, string>
     */

    /**
     * @param  array<string, mixed>  $cases
     */
    private static function enumOptions(array $cases): array
    {
        return collect($cases)
            ->mapWithKeys(fn (LoanStatus|LoanPriority $case): array => [$case->value => ucfirst(str_replace('_', ' ', $case->value))])
            ->all();
    }
}
