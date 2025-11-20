<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\Actions\AssignTicketAction;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Models\HelpdeskTicket;
use App\Services\TicketStatusTransitionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

/**
 * View Helpdesk Ticket Page
 *
 * Enhanced with quick actions for assignment, status updates, and export.
 * Displays assignment history and status timeline via relation managers.
 *
 * @trace Requirements 1.2, 7.1
 *
 * @property HelpdeskTicket $record
 */
class ViewHelpdeskTicket extends ViewRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Quick assign action
            AssignTicketAction::make()
                ->label(__('helpdesk.assign_ticket'))
                ->icon(Heroicon::OutlinedUserPlus)
                ->color('primary')
                ->visible(fn () => auth()->user()?->can('update', $this->record) ?? false),

            // Quick status update action
            Action::make('updateStatus')
                ->label(__('helpdesk.update_status'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->form(function () {
                    $statusService = app(TicketStatusTransitionService::class);
                    $validStatuses = $statusService->getAllowedTransitions($this->record->status);

                    return [
                        \Filament\Forms\Components\Select::make('status')
                            ->label(__('helpdesk.new_status'))
                            ->options(array_combine($validStatuses, array_map(
                                fn ($status) => ucfirst(str_replace('_', ' ', $status)),
                                $validStatuses
                            )))
                            ->required()
                            ->helperText(__('helpdesk.valid_status_transitions')),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label(__('helpdesk.status_change_notes'))
                            ->rows(3)
                            ->placeholder(__('helpdesk.optional_status_notes')),
                    ];
                })
                ->action(function (array $data) {
                    $statusService = app(TicketStatusTransitionService::class);
                    $statusService->transition(
                        $this->record,
                        $data['status'],
                        $data['notes'] ?? null
                    );

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('helpdesk.status_updated'))
                        ->body(__('helpdesk.ticket_status_changed', ['status' => $data['status']]))
                        ->send();
                })
                ->visible(fn () => auth()->user()?->can('update', $this->record) ?? false),

            // Export ticket action
            ActionGroup::make([
                Action::make('exportPdf')
                    ->label(__('helpdesk.export_as_pdf'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->action(fn () => $this->exportTicket('pdf')),
                Action::make('exportCsv')
                    ->label(__('helpdesk.export_as_csv'))
                    ->icon(Heroicon::OutlinedTableCells)
                    ->action(fn () => $this->exportTicket('csv')),
            ])
                ->label(__('helpdesk.export'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->visible(fn () => auth()->user()?->can('view', $this->record) ?? false),

            // Edit action
            EditAction::make()
                ->icon(Heroicon::OutlinedPencilSquare),
        ];
    }

    /**
     * Export ticket details in specified format
     */
    protected function exportTicket(string $format): void
    {
        // Placeholder for export functionality
        // Full implementation would generate PDF/CSV with ticket details
        \Filament\Notifications\Notification::make()
            ->info()
            ->title(__('helpdesk.export_initiated'))
            ->body("Exporting ticket {$this->record->ticket_number} as {$format}")
            ->send();
    }
}
