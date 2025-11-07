<?php

declare(strict_types=1);

namespace App\Filament\Resources\Helpdesk\Pages;

use App\Filament\Resources\Helpdesk\Actions\AssignTicketAction;
use App\Filament\Resources\Helpdesk\HelpdeskTicketResource;
use App\Services\TicketStatusTransitionService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

/**
 * View Helpdesk Ticket Page
 *
 * Enhanced with quick actions for assignment, status updates, and export.
 * Displays assignment history and status timeline via relation managers.
 *
 * @trace Requirements 1.2, 7.1
 */
class ViewHelpdeskTicket extends ViewRecord
{
    protected static string $resource = HelpdeskTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Quick assign action
            AssignTicketAction::make('assign')
                ->label('Assign Ticket')
                ->icon(Heroicon::OutlineUserPlus)
                ->color('primary')
                ->visible(fn () => auth()->user()->can('update', $this->record)),

            // Quick status update action
            Actions\Action::make('updateStatus')
                ->label('Update Status')
                ->icon(Heroicon::OutlineArrowPath)
                ->color('warning')
                ->form(function () {
                    $statusService = app(TicketStatusTransitionService::class);
                    $validStatuses = $statusService->getValidNextStatuses($this->record->status);

                    return [
                        \Filament\Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options(array_combine($validStatuses, array_map(
                                fn ($status) => ucfirst(str_replace('_', ' ', $status)),
                                $validStatuses
                            )))
                            ->required()
                            ->helperText('Only valid status transitions are shown'),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Status Change Notes')
                            ->rows(3)
                            ->placeholder('Optional notes about this status change'),
                    ];
                })
                ->action(function (array $data) {
                    $statusService = app(TicketStatusTransitionService::class);
                    $statusService->transitionStatus(
                        $this->record,
                        $data['status'],
                        $data['notes'] ?? null
                    );

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Status Updated')
                        ->body("Ticket status changed to {$data['status']}")
                        ->send();
                })
                ->visible(fn () => auth()->user()->can('update', $this->record)),

            // Export ticket action
            Actions\Action::make('export')
                ->label('Export')
                ->icon(Heroicon::OutlineArrowDownTray)
                ->color('gray')
                ->dropdown()
                ->dropdownActions([
                    Actions\Action::make('exportPdf')
                        ->label('Export as PDF')
                        ->icon(Heroicon::OutlineDocumentText)
                        ->action(fn () => $this->exportTicket('pdf')),
                    Actions\Action::make('exportCsv')
                        ->label('Export as CSV')
                        ->icon(Heroicon::OutlineTableCells)
                        ->action(fn () => $this->exportTicket('csv')),
                ])
                ->visible(fn () => auth()->user()->can('view', $this->record)),

            // Edit action
            Actions\EditAction::make()
                ->icon(Heroicon::OutlinePencilSquare),
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
            ->title('Export Initiated')
            ->body("Exporting ticket {$this->record->ticket_number} as {$format}")
            ->send();
    }
}
