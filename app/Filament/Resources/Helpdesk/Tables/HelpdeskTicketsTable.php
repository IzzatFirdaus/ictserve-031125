<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Tables;

use App\Filament\Resources\Helpdesk\Actions\AssignTicketAction;
use App\Models\Division;
use App\Models\User;
use App\Services\TicketStatusTransitionService;
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
                    ->label(__('helpdesk.ticket_number'))
                    ->searchable()
                    ->sortable(),

                // Hybrid submission type badge
                Tables\Columns\TextColumn::make('submission_type')
                    ->label(__('helpdesk.submission_type'))
                    ->badge()
                    ->state(fn ($record) => $record->isGuestSubmission() ? __('helpdesk.submission_type_guest') : __('helpdesk.submission_type_authenticated'))
                    ->color(fn ($record) => $record->isGuestSubmission() ? 'warning' : 'success')
                    ->icon(fn ($record) => $record->isGuestSubmission() ? 'heroicon-o-user' : 'heroicon-o-user-circle')
                    ->tooltip(fn ($record) => $record->isGuestSubmission()
                        ? __('helpdesk.submission_tooltip_guest', ['name' => $record->guest_name, 'email' => $record->guest_email])
                        : __('helpdesk.submission_tooltip_authenticated', ['name' => $record->user->name, 'email' => $record->user->email]))
                    ->sortable(query: fn ($query, $direction) => $query->orderByRaw("CASE WHEN user_id IS NULL THEN 0 ELSE 1 END {$direction}")),

                Tables\Columns\TextColumn::make('subject')
                    ->label(__('helpdesk.subject'))
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name_ms')
                    ->label(__('helpdesk.category'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('helpdesk.priority'))
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
                    ->formatStateUsing(fn ($state) => $state ? $state->diffForHumans() : '-')
                    ->tooltip(fn ($record) => optional($record->sla_resolution_due_at)?->toDayDateTimeString())
                    ->color(fn ($record) => $record->sla_resolution_due_at && now()->greaterThan($record->sla_resolution_due_at) ? 'danger' : 'success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('helpdesk.created_at'))
                    ->dateTime('d M Y h:i A')
                    ->sortable(),

                // SLA status indicator used by tests ("overdue" when due date passed)
                Tables\Columns\TextColumn::make('sla_status')
                    ->label(__('helpdesk.sla_status'))
                    ->state(function ($record): string {
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
                            $dueAt = optional($record->created_at)?->copy()->addHours($hours);
                        }

                        return ($dueAt && now()->greaterThan($dueAt)) ? 'overdue' : 'ok';
                    })
                    ->badge()
                    ->color(fn (string $state) => $state === 'overdue' ? 'danger' : 'success')
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
                    ->indicator('Aset'),

                Tables\Filters\SelectFilter::make('asset_id')
                    ->relationship('relatedAsset', 'name')
                    ->label(__('helpdesk.specific_asset'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                // Enhanced SLA filter with better visibility
                Tables\Filters\Filter::make('sla_breached')
                    ->label('⚠️ SLA Melebihi')
                    ->query(fn ($query) => $query->whereNotNull('sla_resolution_due_at')->where('sla_resolution_due_at', '<', now()))
                    ->toggle()
                    ->indicator('SLA'),

                // Additional useful filters
                Tables\Filters\Filter::make('unassigned')
                    ->label('Belum Ditugaskan')
                    ->query(fn ($query) => $query->whereNull('assigned_to_user'))
                    ->toggle(),

                Tables\Filters\Filter::make('my_tickets')
                    ->label('Tiket Saya')
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
                    ->relationship('assignedDivision', 'name_ms')
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
                    ->visible(fn ($record) => Auth::user()?->can('delete', $record) === true),
                Action::make('updateStatus')
                    ->label(__('helpdesk.update_status'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form(function ($record) {
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
                                ->helperText('Catatan tambahan untuk perubahan status (pilihan)'),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $transitionService = app(TicketStatusTransitionService::class);
                        try {
                            $transitionService->transition($record, $data['status'], $data['notes'] ?? null);
                            Notification::make()
                                ->title(__('helpdesk.status_updated'))
                                ->success()
                                ->body("Status tiket {$record->ticket_number} telah dikemaskini.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('common.error'))
                                ->danger()
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => $record->status !== 'closed'),
                Action::make('markResolved')
                    ->label('Tanda Selesai')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'resolved' && $record->status !== 'closed')
                    ->action(function ($record) {
                        $transitionService = app(TicketStatusTransitionService::class);
                        try {
                            $transitionService->transition($record, 'resolved');
                            Notification::make()
                                ->title('Tiket Diselesaikan')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Ralat')
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
                                ->options(fn () => Division::query()->orderBy('name_ms')->pluck('name_ms', 'id'))
                                ->label('Bahagian')
                                ->searchable()
                                ->preload(),
                            Select::make('assigned_to_user')
                                ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                                ->label('Pegawai')
                                ->searchable()
                                ->preload(),
                            TextInput::make('assigned_to_agency')
                                ->label('Agensi Luar')
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $success = 0;
                            $failed = 0;

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
                                ->body("{$success} tiket berjaya ditugaskan".($failed > 0 ? ", {$failed} gagal" : ''))
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
                                ->label('Status'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $success = 0;
                            $failed = 0;

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
                                ->body("{$success} tiket berjaya dikemaskini".($failed > 0 ? ", {$failed} gagal" : ''))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export')
                        ->label(__('helpdesk.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->form([
                            Select::make('format')
                                ->label('Format')
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

                            // Export logic would go here
                            // For now, just show notification
                            Notification::make()
                                ->title(__('helpdesk.export_initiated'))
                                ->success()
                                ->body("{$records->count()} tiket akan dieksport ke format {$format}")
                                ->send();

                            // TODO: Implement actual export functionality
                        }),

                    BulkAction::make('close')
                        ->label(__('helpdesk.close_ticket'))
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $success = 0;
                            $failed = 0;

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
                                ->body("{$success} tiket berjaya ditutup".($failed > 0 ? ", {$failed} gagal" : ''))
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    private static function statusLabels(): array
    {
        return [
            'open' => 'Open',
            'assigned' => 'Assigned',
            'in_progress' => 'In Progress',
            'pending_user' => 'Pending User',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

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
