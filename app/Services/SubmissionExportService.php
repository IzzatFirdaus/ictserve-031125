<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Collection;

/**
 * Submission Export Service
 *
 * Handles exporting submission data (tickets and loans) to various formats.
 * Supports CSV and PDF exports with customizable column selection.
 *
 * @package App\Services
 */
class SubmissionExportService
{
    /**
     * Export tickets to CSV format
     *
     * @param  Collection<int, HelpdeskTicket>  $tickets
     * @return string CSV content
     */
    public function exportTicketsToCSV(Collection $tickets): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Header row
        fputcsv($output, [
            'Ticket Number',
            'Subject',
            'Category',
            'Status',
            'Priority',
            'Division',
            'Created At',
            'Updated At',
            'Assigned To',
        ]);

        // Data rows
        foreach ($tickets as $ticket) {
            fputcsv($output, [
                $ticket->ticket_number,
                $ticket->subject,
                $ticket->category?->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', $ticket->status->value)),
                ucfirst($ticket->priority->value),
                $ticket->division?->name ?? 'N/A',
                $ticket->created_at->format('Y-m-d H:i:s'),
                $ticket->updated_at->format('Y-m-d H:i:s'),
                $ticket->assignedUser?->name ?? 'Unassigned',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export loan applications to CSV format
     *
     * @param  Collection<int, LoanApplication>  $loans
     * @return string CSV content
     */
    public function exportLoansToCSV(Collection $loans): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Header row
        fputcsv($output, [
            'Application Number',
            'Applicant Name',
            'Division',
            'Purpose',
            'Loan Start Date',
            'Expected Return Date',
            'Status',
            'Approval Status',
            'Created At',
        ]);

        // Data rows
        foreach ($loans as $loan) {
            fputcsv($output, [
                $loan->application_number,
                $loan->applicant_name,
                $loan->division?->name ?? 'N/A',
                $loan->purpose,
                $loan->loan_start_date?->format('Y-m-d') ?? 'N/A',
                $loan->expected_return_date?->format('Y-m-d') ?? 'N/A',
                ucfirst(str_replace('_', ' ', $loan->status->value)),
                $loan->approval_status ? ucfirst($loan->approval_status->value) : 'N/A',
                $loan->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generate filename for export
     *
     * @param  string  $type  Type of export (tickets or loans)
     * @param  string  $format  Export format (csv or pdf)
     * @return string Filename with timestamp
     */
    public function generateFilename(string $type, string $format = 'csv'): string
    {
        $timestamp = now()->format('Y-m-d_His');
        
        return "{$type}_export_{$timestamp}.{$format}";
    }
}
