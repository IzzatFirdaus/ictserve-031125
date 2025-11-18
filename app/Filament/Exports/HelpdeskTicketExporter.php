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
                ->label(__('helpdesk.exporter.ticket_number')),
            ExportColumn::make('created_at')
                ->label(__('helpdesk.exporter.created_at')),
            ExportColumn::make('status')
                ->label(__('helpdesk.exporter.status')),
            ExportColumn::make('priority')
                ->label(__('helpdesk.exporter.priority')),
            ExportColumn::make('subject')
                ->label(__('helpdesk.exporter.subject')),
            ExportColumn::make('submitter_name')
                ->label(__('helpdesk.exporter.submitter_name'))
                ->state(fn (HelpdeskTicket $record): string => $record->getSubmitterName()),
            ExportColumn::make('submitter_email')
                ->label(__('helpdesk.exporter.submitter_email'))
                ->state(fn (HelpdeskTicket $record): string => $record->getSubmitterEmail()),
            ExportColumn::make('submission_type')
                ->label(__('helpdesk.exporter.submission_type'))
                ->state(fn (HelpdeskTicket $record): string => $record->isGuestSubmission()
                    ? __('helpdesk.exporter.submission_guest')
                    : __('helpdesk.exporter.submission_authenticated')),
            ExportColumn::make('category.name')
                ->label(__('helpdesk.exporter.category')),
            ExportColumn::make('assignedUser.name')
                ->label(__('helpdesk.exporter.assigned_to')),
            ExportColumn::make('assignedDivision.name')
                ->label(__('helpdesk.exporter.assigned_division')),
            ExportColumn::make('assigned_at')
                ->label(__('helpdesk.exporter.assigned_date')),
            ExportColumn::make('responded_at')
                ->label(__('helpdesk.exporter.response_date')),
            ExportColumn::make('resolved_at')
                ->label(__('helpdesk.exporter.resolved_date')),
            ExportColumn::make('closed_at')
                ->label(__('helpdesk.exporter.closed_date')),
            ExportColumn::make('sla_resolution_due_at')
                ->label(__('helpdesk.exporter.sla_due_date')),
            ExportColumn::make('sla_status')
                ->label(__('helpdesk.exporter.sla_status'))
                ->state(function (HelpdeskTicket $record): string {
                    if (! $record->sla_resolution_due_at) {
                        return __('helpdesk.exporter.sla_not_applicable');
                    }
                    if (! $record->resolved_at) {
                        return now() > $record->sla_resolution_due_at
                            ? __('helpdesk.exporter.sla_breached')
                            : __('helpdesk.exporter.sla_in_progress');
                    }

                    return $record->resolved_at <= $record->sla_resolution_due_at
                        ? __('helpdesk.exporter.sla_met')
                        : __('helpdesk.exporter.sla_breached');
                }),
            ExportColumn::make('resolution_hours')
                ->label(__('helpdesk.exporter.resolution_time_hours'))
                ->state(function (HelpdeskTicket $record): ?string {
                    if (! $record->resolved_at || ! $record->created_at) {
                        return null;
                    }

                    return (string) round($record->created_at->diffInHours($record->resolved_at), 2);
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $successfulRows = number_format($export->successful_rows);
        $failedRowsCount = $export->getFailedRowsCount();

        return trans_choice(
            'helpdesk.exporter.completed_body',
            $failedRowsCount ? 2 : 1,
            [
                'successful' => $successfulRows,
                'failed' => number_format($failedRowsCount),
            ]
        );
    }
}
