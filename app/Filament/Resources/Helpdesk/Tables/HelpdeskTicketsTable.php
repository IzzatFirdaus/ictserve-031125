<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Tables;

use App\Filament\Resources\Helpdesk\Actions\AssignTicketAction;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\TicketStatusTransitionService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Helpdesk Ticket table definition with SLA indicators and bulk workflows.
 */
class HelpdeskTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Prevent Filament from generating default record URL (which expects a view page)
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label((string) __('helpdesk.ticket_number'))
                    ->searchable()
                    ->sortable(),

                // Hybrid submission type badge
                Tables\Columns\TextColumn::make('submission_type')
                    ->label((string) __('helpdesk.submission_type'))
                    ->badge()
                    ->state(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? (string) __('helpdesk.submission_type_guest') : (string) __('helpdesk.submission_type_authenticated'))
                    ->color(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'warning' : 'success')
                    ->icon(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'heroicon-o-user' : 'heroicon-o-user-circle')
                    ->tooltip(fn (HelpdeskTicket $record): string => $record->isGuestSubmission()
                        ? (string) __('helpdesk.submission_tooltip_guest', ['name' => $record->guest_name, 'email' => $record->guest_email])
                        : (string) __('helpdesk.submission_tooltip_authenticated', ['name' => $record->user?->name, 'email' => $record->user?->email]))
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("CASE WHEN user_id IS NULL THEN 0 ELSE 1 END {$direction}")),

                Tables\Columns\TextColumn::make('subject')
                    ->label((string) __('helpdesk.subject'))
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name_ms')
                    ->label((string) __('helpdesk.category'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label((string) __('helpdesk.priority'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low' => 'gray',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('helpdesk.status'))
                    ->badge()
                    ->color(fn (string $state) => self::statusColors()[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable(),

                // Asset linkage display
                Tables\Columns\TextColumn::make('relatedAsset.name')
                    ->label(__('helpdesk.related_asset'))
                    ->placeholder('-')
                    ->icon('heroicon-o-cube')
                    ->color('info')
                    ->tooltip(fn ($record) => $record->relatedAsset
                        ? "Asset Tag: {$record->relatedAsset->asset_tag}"
                        : null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('helpdesk.assigned_to'))
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sla_resolution_due_at')
                    ->label(__('helpdesk.sla_resolution_due_at'))
                    ->formatStateUsing(fn (?Carbon $state): string => $state ? $state->diffForHumans() : '-')
                    ->tooltip(function (HelpdeskTicket $record): ?string {
                        $dueAt = $record->sla_resolution_due_at;

                        return $dueAt instanceof Carbon ? $dueAt->toDayDateTimeString() : null;
                    })
                    ->color(fn (HelpdeskTicket $record): string => $record->sla_resolution_due_at && now()->greaterThan($record->sla_resolution_due_at) ? 'danger' : 'success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('helpdesk.created_at'))
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                // SLA status indicator with warning for approaching breach (25% remaining time)
                // Per Requirements 5.5 - SLA indicators and warning display
                Tables\Columns\TextColumn::make('sla_status')
                    ->label(__('helpdesk.sla_status'))
                    ->state(function (HelpdeskTicket $record): string {
                        // Prefer stored due date, otherwise compute from priority baseline
                        $dueAt = $record->sla_resolution_due_at;
                        if (! $dueAt) {
                            $hours = match ($record->priority) {
                                'urgent' => 4,
                                'high' => 24,
                                'normal' => 72,
                                'low' => 168,
                                default => 72,
                            };
                            $createdAt = $record->created_at;
                            if ($createdAt instanceof Carbon) {
                                $dueAt = $createdAt->copy()->addHours($hours);
                            }
                        }

                        if (! $dueAt) {
                            return 'ok';
                        }

                        $now = now();

                        // Check if already breached
                        if ($now->greaterThan($dueAt)) {
                            return 'overdue';
                        }

                        // Check if approaching breach (25% remaining time)
                        // Calculate total SLA duration and remaining time
                        $createdAt = $record->created_at;
                        if ($createdAt instanceof Carbon) {
                            $totalDuration = $createdAt->diffInMinutes($dueAt);
                            $remainingTime = $now->diffInMinutes($dueAt, false);
                            $percentRemaining = $totalDuration > 0 ? ($remainingTime / $totalDuration) * 100 : 0;

                            if ($percentRemaining <= 25 && $percentRemaining > 0) {
                                return 'at_risk';
                            }
                        }

                        return 'ok';
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'overdue' => 'danger',
                        'at_risk' => 'warning',
                        default => 'success',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'overdue' => 'heroicon-o-exclamation-triangle',
                        'at_risk' => 'heroicon-o-clock',
                        default => 'heroicon-o-check-circle',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'overdue' => __('helpdesk.sla_overdue'),
                        'at_risk' => __('helpdesk.sla_at_risk'),
                        default => __('helpdesk.sla_on_track'),
                    })
                    ->tooltip(function (HelpdeskTicket $record): ?string {
                        $dueAt = $record->sla_resolution_due_at;
                        if (! $dueAt) {
                            return null;
                        }

                        $now = now();
                        if ($now->greaterThan($dueAt)) {
                            return __('helpdesk.sla_breached_tooltip', ['time' => $dueAt->diffForHumans()]);
                        }

                        return __('helpdesk.sla_due_tooltip', ['time' => $dueAt->diffForHumans()]);
                    })
                    ->toggleable(),
            ])
            ->filters([
                // Enhanced filter organization with groups
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::statusLabels())
                    ->label(__('helpdesk.status'))
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('helpdesk.priority'))
                    ->options([
                        'low' => __('helpdesk.priority_low'),
                        'normal' => __('helpdesk.priority_normal'),
                        'high' => __('helpdesk.priority_high'),
                        'urgent' => __('helpdesk.priority_urgent'),
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name_en')
                    ->label(__('helpdesk.category'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Enhanced hybrid submission type filter with better UI
                Tables\Filters\SelectFilter::make('submission_type')
                    ->label(__('helpdesk.submission_type'))
                    ->options([
                        'guest' => '👤 '.__('helpdesk.submission_type_guest'),
                        'authenticated' => '🔐 '.__('helpdesk.submission_type_authenticated'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'guest') {
                            return $query->whereNull('user_id');
                        }
                        if ($data['value'] === 'authenticated') {
                            return $query->whereNotNull('user_id');
                        }

                        return $query;
                    })
                    ->indicator(__('helpdesk.indicator_submission_type')),

                // Enhanced asset linkage filters
                Tables\Filters\Filter::make('has_asset')
                    ->label(__('helpdesk.has_related_asset'))
                    ->query(fn ($query) => $query->whereNotNull('asset_id'))
                    ->toggle()
                    ->indicator(__('helpdesk.filter_indicator_asset')),

                Tables\Filters\SelectFilter::make('asset_id')
                    ->relationship('relatedAsset', 'name')
                    ->label(__('helpdesk.specific_asset'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Enhanced SLA filter with better visibility - Per Requirements 5.5
                Tables\Filters\SelectFilter::make('sla_status')
                    ->label(__('helpdesk.sla_status'))
                    ->options([
                        'breached' => '🔴 '.__('helpdesk.sla_overdue'),
                        'at_risk' => '🟡 '.__('helpdesk.sla_at_risk'),
                        'on_track' => '🟢 '.__('helpdesk.sla_on_track'),
                    ])
                    ->query(function ($query, array $data) {
                        $now = now();

                        return match ($data['value'] ?? null) {
                            'breached' => $query->whereNotNull('sla_resolution_due_at')
                                ->where('sla_resolution_due_at', '<', $now),
                            'at_risk' => $query->whereNotNull('sla_resolution_due_at')
                                ->where('sla_resolution_due_at', '>=', $now)
                                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, sla_resolution_due_at) * 0.25 >= TIMESTAMPDIFF(MINUTE, ?, sla_resolution_due_at)', [$now]),
                            'on_track' => $query->where(function ($q) use ($now) {
                                $q->whereNull('sla_resolution_due_at')
                                    ->orWhere('sla_resolution_due_at', '>', $now);
                            }),
                            default => $query,
                        };
                    })
                    ->indicator(__('helpdesk.filter_indicator_sla')),

                Tables\Filters\Filter::make('sla_breached')
                    ->label(__('helpdesk.filter_sla_breached'))
                    ->query(fn ($query) => $query->whereNotNull('sla_resolution_due_at')->where('sla_resolution_due_at', '<', now()))
                    ->toggle()
                    ->indicator(__('helpdesk.filter_indicator_sla_breached')),

                // Additional useful filters
                Tables\Filters\Filter::make('unassigned')
                    ->label(__('helpdesk.filter_unassigned'))
                    ->query(fn ($query) => $query->whereNull('assigned_to_user'))
                    ->toggle(),

                Tables\Filters\Filter::make('my_tickets')
                    ->label(__('helpdesk.filter_my_tickets'))
                    ->query(fn ($query) => $query->where('assigned_to_user', Auth::id()))
                    ->toggle(),

                // Date range filter
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('helpdesk.date_from')),
                        DatePicker::make('created_until')
                            ->label(__('helpdesk.date_to')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = __('helpdesk.date_from').': '.\Carbon\Carbon::parse($data['created_from'])->format('d M Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = __('helpdesk.date_to').': '.\Carbon\Carbon::parse($data['created_until'])->format('d M Y');
                        }

                        return $indicators;
                    }),

                // Division filter
                Tables\Filters\SelectFilter::make('assigned_to_division')
                    ->relationship('assignedDivision', app()->getLocale() === 'ms' ? 'name_ms' : 'name_en')
                    ->label(__('helpdesk.assigned_division'))
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->persistFiltersInSession()
            ->poll('60s')
            ->actions([
                EditAction::make(),
                AssignTicketAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (HelpdeskTicket $record) => Auth::user()?->can('delete', $record) === true),
                // Status update action with required comment per Requirements 5.3
                Action::make('updateStatus')
                    ->label(__('helpdesk.update_status'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form(function (HelpdeskTicket $record) {
                        $transitionService = app(TicketStatusTransitionService::class);
                        $allowedStatuses = $transitionService->getAllowedTransitions($record->status);

                        return [
                            Select::make('status')
                                ->label(__('helpdesk.new_status'))
                                ->options(array_combine(
                                    $allowedStatuses,
                                    array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), $allowedStatuses)
                                ))
                                ->required()
                                ->helperText(__('helpdesk.valid_status_transitions')),
                            Textarea::make('notes')
                                ->label(__('helpdesk.status_change_notes'))
                                ->rows(3)
                                ->required() // Per Requirements 5.3 - Status management with required comment
                                ->minLength(10)
                                ->helperText(__('helpdesk.status_update_notes_required_helper')),
                        ];
                    })
                    ->action(function (HelpdeskTicket $record, array $data) {
                        $transitionService = app(TicketStatusTransitionService::class);
                        try {
                            $transitionService->transition($record, $data['status'], $data['notes'] ?? null);
                            Notification::make()
                                ->title(__('helpdesk.status_updated'))
                                ->success()
                                ->body(__('helpdesk.status_update_body', ['number' => $record->ticket_number]))
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('helpdesk.error_title'))
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => $record->status !== 'closed'),
                Action::make('markResolved')
                    ->label(__('helpdesk.action_mark_resolved'))
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (HelpdeskTicket $record) => $record->status !== 'resolved' && $record->status !== 'closed')
                    ->action(function (HelpdeskTicket $record) {
                        $transitionService = app(TicketStatusTransitionService::class);
                        try {
                            $transitionService->transition($record, 'resolved');
                            Notification::make()
                                ->title(__('helpdesk.ticket_resolved_notification'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('helpdesk.error_title'))
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('assign')
                        ->label(__('helpdesk.assign'))
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Select::make('assigned_to_division')
                                ->options(fn () => Division::query()->orderBy(app()->getLocale() === 'ms' ? 'name_ms' : 'name_en')->pluck(app()->getLocale() === 'ms' ? 'name_ms' : 'name_en', 'id'))
                                ->label(__('helpdesk.bulk_assign_division'))
                                ->searchable()
                                ->preload(),
                            Select::make('assigned_to_user')
                                ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                                ->label(__('helpdesk.bulk_assign_staff'))
                                ->searchable()
                                ->preload(),
                            TextInput::make('assigned_to_agency')
                                ->label(__('helpdesk.bulk_assign_agency'))
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $success = 0;
                            $failed = 0;

                            /** @var HelpdeskTicket $ticket */
                            foreach ($records as $ticket) {
                                try {
                                    $ticket->update([
                                        'assigned_to_division' => $data['assigned_to_division'] ?? null,
                                        'assigned_to_user' => $data['assigned_to_user'] ?? null,
                                        'assigned_to_agency' => $data['assigned_to_agency'] ?? null,
                                        'assigned_at' => now(),
                                        'status' => $ticket->status === 'open' ? 'assigned' : $ticket->status,
                                    ]);

                                    // Audit trail is automatically logged by OwenIt\Auditing package

                                    $success++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title(__('helpdesk.assignment_completed'))
                                ->success()
                                ->body(__('helpdesk.bulk_success_message', ['count' => $success, 'action' => __('helpdesk.bulk_action_assigned')]).($failed > 0 ? __('helpdesk.bulk_failed_message', ['count' => $failed]) : ''))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('update_status')
                        ->label(__('helpdesk.update_status'))
                        ->icon('heroicon-o-adjustments-vertical')
                        ->form([
                            Select::make('status')
                                ->options(self::statusLabels())
                                ->required()
                                ->label(__('helpdesk.label_status')),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $success = 0;
                            $failed = 0;

                            /** @var HelpdeskTicket $ticket */
                            foreach ($records as $ticket) {
                                try {
                                    $ticket->update([
                                        'status' => $data['status'],
                                        'resolved_at' => $data['status'] === 'resolved' ? now() : $ticket->resolved_at,
                                        'closed_at' => $data['status'] === 'closed' ? now() : $ticket->closed_at,
                                    ]);

                                    // Audit trail is automatically logged by OwenIt\Auditing package

                                    $success++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title(__('helpdesk.status_updated'))
                                ->success()
                                ->body(__('helpdesk.bulk_success_message', ['count' => $success, 'action' => __('helpdesk.bulk_action_updated')]).($failed > 0 ? __('helpdesk.bulk_failed_message', ['count' => $failed]) : ''))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export')
                        ->label(__('helpdesk.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->form([
                            Select::make('format')
                                ->label(__('helpdesk.label_format'))
                                ->options([
                                    'csv' => 'CSV',
                                    'xlsx' => 'Excel',
                                    'pdf' => 'PDF',
                                ])
                                ->default('csv')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $format = $data['format'];
                            $filename = 'helpdesk-tickets-'.now()->format('Y-m-d-His').'.'.$format;

                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title(__('helpdesk.error_title'))
                                    ->danger()
                                    ->body(__('No tickets selected for export.'))
                                    ->send();

                                return null;
                            }

                            $rows = $records->map(function (HelpdeskTicket $ticket): array {
                                $category = app()->getLocale() === 'ms'
                                    ? $ticket->category?->name_ms
                                    : $ticket->category?->name_en;

                                $division = app()->getLocale() === 'ms'
                                    ? $ticket->assignedDivision?->name_ms
                                    : $ticket->assignedDivision?->name_en;

                                return [
                                    __('helpdesk.ticket_number') => $ticket->ticket_number,
                                    __('helpdesk.subject') => $ticket->subject,
                                    __('helpdesk.status') => ucfirst(str_replace('_', ' ', $ticket->status)),
                                    __('helpdesk.priority') => ucfirst($ticket->priority),
                                    __('helpdesk.submission_type') => $ticket->isGuestSubmission()
                                        ? __('helpdesk.submission_type_guest')
                                        : __('helpdesk.submission_type_authenticated'),
                                    __('helpdesk.category') => $category ?? '-',
                                    __('helpdesk.assigned_to') => $ticket->assignedUser?->name ?? '-',
                                    __('helpdesk.assigned_division') => $division ?? '-',
                                    __('helpdesk.created_at') => $ticket->created_at?->format('Y-m-d H:i:s') ?? '-',
                                    __('helpdesk.sla_resolution_due_at') => $ticket->sla_resolution_due_at?->format('Y-m-d H:i:s') ?? '-',
                                ];
                            });

                            $export = new class($rows) implements FromCollection, WithHeadings {
                                public function __construct(private readonly Collection $rows)
                                {
                                }

                                public function collection(): Collection
                                {
                                    return $this->rows;
                                }

                                public function headings(): array
                                {
                                    return $this->rows->isNotEmpty()
                                        ? array_keys($this->rows->first())
                                        : [];
                                }
                            };

                            $writerType = match ($format) {
                                'xlsx' => ExcelFormat::XLSX,
                                'pdf' => ExcelFormat::DOMPDF,
                                default => ExcelFormat::CSV,
                            };

                            Notification::make()
                                ->title(__('helpdesk.export_initiated'))
                                ->success()
                                ->body(__('helpdesk.bulk_export_count', ['count' => $records->count(), 'format' => $format]))
                                ->send();

                            return Excel::download($export, $filename, $writerType);
                        }),

                    BulkAction::make('close')
                        ->label(__('helpdesk.close_ticket'))
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $success = 0;
                            $failed = 0;

                            /** @var HelpdeskTicket $ticket */
                            foreach ($records as $ticket) {
                                try {
                                    $ticket->update([
                                        'status' => 'closed',
                                        'closed_at' => now(),
                                    ]);

                                    // Audit trail is automatically logged by OwenIt\Auditing package

                                    $success++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title(__('helpdesk.ticket_resolved'))
                                ->success()
                                ->body(__('helpdesk.bulk_success_message', ['count' => $success, 'action' => __('helpdesk.bulk_action_closed')]).($failed > 0 ? __('helpdesk.bulk_failed_message', ['count' => $failed]) : ''))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private static function statusLabels(): array
    {
        return [
            'open' => __('helpdesk.status_open'),
            'assigned' => __('helpdesk.status_assigned'),
            'in_progress' => __('helpdesk.status_in_progress'),
            'pending_user' => __('helpdesk.status_pending_user'),
            'resolved' => __('helpdesk.status_resolved'),
            'closed' => __('helpdesk.status_closed'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function statusColors(): array
    {
        return [
            'open' => 'gray',
            'assigned' => 'primary',
            'in_progress' => 'warning',
            'pending_user' => 'secondary',
            'resolved' => 'success',
            'closed' => 'gray',
        ];
    }
}
