<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Actions;

use App\Events\TicketAssigned;
use App\Mail\Helpdesk\TicketAssignedMail;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Assign Ticket Action
 *
 * Provides individual ticket assignment with division/agency selection,
 * priority adjustment, SLA calculation, and email notifications.
 *
 * @trace Requirements D03-FR-001.3, D04 §4.1, Requirement 1.3, 10.2
 */
class AssignTicketAction
{
    public static function make(): Action
    {
        return Action::make('assign')
            ->label('Tugaskan Tiket')
            ->icon('heroicon-o-user-group')
            ->color('primary')
            ->form([
                Select::make('assigned_to_division')
                    ->label('Bahagian')
                    ->options(fn () => Division::query()->orderBy('name_ms')->pluck('name_ms', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('assigned_to_user', null)),

                Select::make('assigned_to_user')
                    ->label('Pegawai')
                    ->options(function (callable $get) {
                        $divisionId = $get('assigned_to_division');
                        if (! $divisionId) {
                            return User::query()->orderBy('name')->pluck('name', 'id');
                        }

                        return User::query()
                            ->where('division_id', $divisionId)
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->live(),

                TextInput::make('assigned_to_agency')
                    ->label('Agensi Luar')
                    ->maxLength(255)
                    ->helperText('Isi jika tiket ditugaskan kepada agensi luar'),

                Select::make('priority')
                    ->label('Keutamaan')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default(fn (HelpdeskTicket $record) => $record->priority)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Calculate SLA deadline based on priority
                        $hours = match ($state) {
                            'urgent' => 4,
                            'high' => 24,
                            'normal' => 72,
                            'low' => 168,
                            default => 72,
                        };
                        $set('sla_resolution_due_at', now()->addHours($hours));
                    }),

                DateTimePicker::make('sla_resolution_due_at')
                    ->label('Tarikh Akhir SLA')
                    ->native(false)
                    ->seconds(false)
                    ->helperText('Tarikh akhir untuk menyelesaikan tiket'),

                Textarea::make('assignment_notes')
                    ->label(__('helpdesk.assignment_notes'))
                    ->rows(3)
                    ->required()
                    ->helperText(__('helpdesk.assignment_notes_helper')),
            ])
            ->action(function (HelpdeskTicket $record, array $data): void {
                // Update ticket with assignment details
                $priority = $data['priority'] ?? $record->priority;
                $slaDueAt = $data['sla_resolution_due_at'] ?? (function () use ($priority) {
                    $hours = match ($priority) {
                        'urgent' => 4,
                        'high' => 24,
                        'normal' => 72,
                        'low' => 168,
                        default => 72,
                    };

                    return now()->addHours($hours);
                })();

                $record->update([
                    'assigned_to_division' => $data['assigned_to_division'] ?? null,
                    'assigned_to_user' => $data['assigned_to_user'] ?? null,
                    'assigned_to_agency' => $data['assigned_to_agency'] ?? null,
                    'assigned_at' => now(),
                    'priority' => $priority,
                    'sla_resolution_due_at' => $slaDueAt,
                    'status' => $record->status === 'open' ? 'assigned' : $record->status,
                ]);

                // Log assignment notes as internal comment if provided
                if (! empty($data['assignment_notes'])) {
                    $record->internalComments()->create([
                        'comment' => $data['assignment_notes'],
                        'user_id' => Auth::id(),
                        'type' => 'assignment',
                    ]);
                }

                // Send email notification (60-second SLA)
                $assignedUserId = isset($data['assigned_to_user']) ? (int) $data['assigned_to_user'] : null;
                if ($assignedUserId) {
                    $assignedUser = User::find($assignedUserId);
                    if ($assignedUser) {
                        // Send email notification
                        Mail::to($assignedUser->email)
                            ->queue(new TicketAssignedMail($record, $assignedUser));

                        // Dispatch WebSocket notification via Laravel Reverb
                        // Per Requirements 5.4 - Real-time assignment notification
                        /** @var User|null $currentUser */
                        $currentUser = Auth::user();
                        event(new TicketAssigned(
                            ticket: $record,
                            assignedUser: $assignedUser,
                            assignedBy: $currentUser,
                        ));
                    }
                }

                // Audit trail is automatically logged by OwenIt\Auditing package

                // Show success notification
                Notification::make()
                    ->title(__('helpdesk.ticket_assigned_success'))
                    ->success()
                    ->body(__('helpdesk.ticket_assigned_body', ['number' => $record->ticket_number]))
                    ->send();
            })
            ->requiresConfirmation()
            ->modalHeading('Tugaskan Tiket')
            ->modalDescription('Tugaskan tiket kepada bahagian, pegawai, atau agensi luar.')
            ->modalSubmitActionLabel('Tugaskan')
            ->successRedirectUrl(null)
            ->visible(fn (HelpdeskTicket $record) => Auth::check() && Auth::user()?->can('update', $record));
    }
}
