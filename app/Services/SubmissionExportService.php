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
            /** @var \App\Enums\TicketStatus $status */
            $status = $ticket->status;
            /** @var \App\Enums\TicketPriority $priority */
            $priority = $ticket->priority;
            
            fputcsv($output, [
                $ticket->ticket_number,
                $ticket->subject,
                $ticket->category?->name ?? 'N/A',
                ucfirst(str_replace('_', ' ', (string) $status->value)),
                ucfirst((string) $priority->value),
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
            /** @var \App\Enums\LoanStatus $loanStatus */
            $loanStatus = $loan->status;
            
            fputcsv($output, [
                $loan->application_number,
                $loan->applicant_name,
                $loan->division?->name ?? 'N/A',
                $loan->purpose,
                $loan->loan_start_date?->format('Y-m-d') ?? 'N/A',
                $loan->expected_return_date?->format('Y-m-d') ?? 'N/A',
                ucfirst(str_replace('_', ' ', (string) $loanStatus->value)),
                $loan->approval_status ? ucfirst((string) $loan->approval_status->value) : 'N/A',
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

    /**
     * Export tickets to printable HTML format (for PDF printing)
     *
     * @param  Collection<int, HelpdeskTicket>  $tickets
     * @return string HTML content
     */
    public function exportTicketsToHTML(Collection $tickets): string
    {
        $html = $this->getHTMLHeader(__('common.tickets_export'));

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>'.__('common.ticket_number').'</th>';
        $html .= '<th>'.__('common.subject').'</th>';
        $html .= '<th>'.__('common.category').'</th>';
        $html .= '<th>'.__('common.status').'</th>';
        $html .= '<th>'.__('common.priority').'</th>';
        $html .= '<th>'.__('common.division').'</th>';
        $html .= '<th>'.__('common.created_date').'</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ($tickets as $ticket) {
            /** @var \App\Enums\TicketStatus $status */
            $status = $ticket->status;
            /** @var \App\Enums\TicketPriority $priority */
            $priority = $ticket->priority;
            
            $html .= '<tr>';
            $html .= '<td>'.e($ticket->ticket_number).'</td>';
            $html .= '<td>'.e($ticket->subject).'</td>';
            $html .= '<td>'.e($ticket->category?->name ?? 'N/A').'</td>';
            $html .= '<td>'.e(ucfirst(str_replace('_', ' ', (string) $status->value))).'</td>';
            $html .= '<td>'.e(ucfirst((string) $priority->value)).'</td>';
            $html .= '<td>'.e($ticket->division?->name ?? 'N/A').'</td>';
            $html .= '<td>'.e($ticket->created_at->format('Y-m-d H:i')).'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= $this->getHTMLFooter();

        return $html;
    }

    /**
     * Export loans to printable HTML format (for PDF printing)
     *
     * @param  Collection<int, LoanApplication>  $loans
     * @return string HTML content
     */
    public function exportLoansToHTML(Collection $loans): string
    {
        $html = $this->getHTMLHeader(__('common.loans_export'));

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th>'.__('common.application_number').'</th>';
        $html .= '<th>'.__('common.applicant_name').'</th>';
        $html .= '<th>'.__('common.division').'</th>';
        $html .= '<th>'.__('common.purpose').'</th>';
        $html .= '<th>'.__('common.loan_period').'</th>';
        $html .= '<th>'.__('common.status').'</th>';
        $html .= '<th>'.__('common.created_date').'</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        foreach ($loans as $loan) {
            /** @var \App\Enums\LoanStatus $loanStatus */
            $loanStatus = $loan->status;
            
            $loanPeriod = ($loan->loan_start_date?->format('Y-m-d') ?? 'N/A').' - '.($loan->expected_return_date?->format('Y-m-d') ?? 'N/A');
            $html .= '<tr>';
            $html .= '<td>'.e($loan->application_number).'</td>';
            $html .= '<td>'.e($loan->applicant_name).'</td>';
            $html .= '<td>'.e($loan->division?->name ?? 'N/A').'</td>';
            $html .= '<td>'.e($loan->purpose).'</td>';
            $html .= '<td>'.e($loanPeriod).'</td>';
            $html .= '<td>'.e(ucfirst(str_replace('_', ' ', (string) $loanStatus->value))).'</td>';
            $html .= '<td>'.e($loan->created_at->format('Y-m-d H:i')).'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= $this->getHTMLFooter();

        return $html;
    }

    /**
     * Get HTML header for printable export
     */
    protected function getHTMLHeader(string $title): string
    {
        $date = now()->format('Y-m-d H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        .meta { color: #666; margin-bottom: 20px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
        @media print {
            body { margin: 0; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body>
    <h1>{$title}</h1>
    <div class="meta">Generated: {$date}</div>
HTML;
    }

    /**
     * Get HTML footer for printable export
     */
    protected function getHTMLFooter(): string
    {
        return <<<'HTML'
    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
HTML;
    }
}
