<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

/**
 * Export Loans Action
 *
 * Provides CSV export functionality for loan applications with current table filters applied.
 *
 * @trace Requirements 8.1, 8.2 (Reporting and analytics)
 */
class ExportLoansAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Eksport Data')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () {
                $query = $this->getLivewire()->getFilteredTableQuery();
                $loans = $query->with(['division', 'loanItems.asset', 'user'])->get();

                $csv = $this->generateCsv($loans);

                return Response::streamDownload(
                    fn () => print ($csv),
                    'loan-applications-'.now()->format('Y-m-d-His').'.csv',
                    ['Content-Type' => 'text/csv; charset=UTF-8']
                );
            })
            ->successNotificationTitle('Data telah dieksport');
    }

    /**
     * Generate CSV content from loan applications
     */
    private function generateCsv($loans): string
    {
        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // CSV Headers (Bilingual - Malay primary)
        fputcsv($output, [
            'No. Permohonan',
            'Nama Pemohon',
            'E-mel',
            'Telefon',
            'ID Kakitangan',
            'Gred',
            'Bahagian',
            'Status',
            'Keutamaan',
            'Tujuan',
            'Lokasi Penggunaan',
            'Tarikh Mula',
            'Tarikh Tamat',
            'Nilai (RM)',
            'Status Kelulusan',
            'Diluluskan Oleh',
            'Tarikh Kelulusan',
            'Kaedah Kelulusan',
            'Sebab Penolakan',
            'Arahan Khas',
            'Jenis Penghantaran',
            'Tarikh Dicipta',
        ]);

        // Data rows
        foreach ($loans as $loan) {
            fputcsv($output, [
                $loan->application_number,
                $loan->applicant_name,
                $loan->applicant_email,
                $loan->applicant_phone ?? '-',
                $loan->staff_id ?? '-',
                $loan->grade ?? '-',
                $loan->division?->name_ms ?? '-',
                $this->getStatusLabel($loan->status),
                $this->getPriorityLabel($loan->priority),
                $loan->purpose,
                $loan->location ?? '-',
                $loan->loan_start_date?->format('d/m/Y') ?? '-',
                $loan->loan_end_date?->format('d/m/Y') ?? '-',
                number_format((float) ($loan->total_value ?? 0), 2, '.', ''),
                $this->getApprovalStatus($loan),
                $loan->approved_by_name ?? '-',
                $loan->approved_at?->format('d/m/Y H:i') ?? '-',
                $loan->approval_method ? ucfirst($loan->approval_method) : '-',
                $loan->rejected_reason ?? '-',
                $loan->special_instructions ?? '-',
                $loan->user_id ? 'Authenticated' : 'Guest',
                $loan->created_at->format('d/m/Y H:i'),
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel($status): string
    {
        if (is_object($status) && method_exists($status, 'label')) {
            return $status->label();
        }

        return ucfirst(str_replace('_', ' ', (string) $status));
    }

    /**
     * Get human-readable priority label
     */
    private function getPriorityLabel($priority): string
    {
        if (is_object($priority) && method_exists($priority, 'label')) {
            return $priority->label();
        }

        return ucfirst(str_replace('_', ' ', (string) $priority));
    }

    /**
     * Get approval status text
     */
    private function getApprovalStatus($loan): string
    {
        if ($loan->approved_at) {
            return 'Diluluskan';
        }

        if ($loan->rejected_reason) {
            return 'Ditolak';
        }

        if ($loan->approval_token) {
            return 'Menunggu';
        }

        return 'Belum Dihantar';
    }
}
