<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\HelpdeskTicket;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

/**
 * Helpdesk Ticket Exporter
 *
 * Exports helpdesk ticket data to CSV, Excel formats.
 * Supports both guest and authenticated submissions.
 *
 * @see D03 Software Requirements Specification - Requirements 3.6, 8.5
 */
class HelpdeskTicketExporter extends Exporter
{
    protected static ?string $model = HelpdeskTicket::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('ticket_number')
                ->label('Ticket Number'),
            ExportColumn::make('created_at')
                ->label('Created Date'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('priority')
                ->label('Priority'),
            ExportColumn::make('subject')
                ->label('Subject'),
            ExportColumn::make('submitter_name')
                ->label('Submitter Name')
                ->state(fn (HelpdeskTicket $record): string => $record->getSubmitterName()),
            ExportColumn::make('submitter_email')
                ->label('Submitter Email')
                ->state(fn (HelpdeskTicket $record): string => $record->getSubmitterEmail()),
            ExportColumn::make('submission_type')
                ->label('Submission Type')
                ->state(fn (HelpdeskTicket $record): string => $record->isGuestSubmission() ? 'Guest' : 'Authenticated'),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('assignedUser.name')
                ->label('Assigned To'),
            ExportColumn::make('assignedDivision.name')
                ->label('Assigned Division'),
            ExportColumn::make('assigned_at')
                ->label('Assigned Date'),
            ExportColumn::make('responded_at')
                ->label('Response Date'),
            ExportColumn::make('resolved_at')
                ->label('Resolved Date'),
            ExportColumn::make('closed_at')
                ->label('Closed Date'),
            ExportColumn::make('sla_resolution_due_at')
                ->label('SLA Due Date'),
            ExportColumn::make('sla_status')
                ->label('SLA Status')
                ->state(function (HelpdeskTicket $record): string {
                    if (! $record->sla_resolution_due_at) {
                        return 'N/A';
                    }
                    if (! $record->resolved_at) {
                        return now() > $record->sla_resolution_due_at ? 'Breached' : 'In Progress';
                    }

                    return $record->resolved_at <= $record->sla_resolution_due_at ? 'Met' : 'Breached';
                }),
            ExportColumn::make('resolution_hours')
                ->label('Resolution Time (Hours)')
                ->state(function (HelpdeskTicket $record): ?string {
                    if (! $record->resolved_at) {
                        return null;
                    }

                    return (string) round($record->created_at->diffInHours($record->resolved_at), 2);
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your helpdesk ticket export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
