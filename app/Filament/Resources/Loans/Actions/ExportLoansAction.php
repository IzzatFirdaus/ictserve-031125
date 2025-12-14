<?php

declare(strict_types=1);

namespace App\Filament\Resources\Loans\Actions;

use App\Models\LoanApplication;
use Filament\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Response;
use UnitEnum;

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
                /** @var HasTable|null $livewire */
                $livewire = $this->getLivewire();
                if ($livewire === null || ! method_exists($livewire, 'getFilteredTableQuery')) {
                    return null;
                }

                $query = $livewire->getFilteredTableQuery();
                if ($query === null) {
                    return null;
                }
                /** @var EloquentCollection<int, LoanApplication> $loans */
                $loans = $query->with(['division', 'loanItems.asset', 'user'])->get();

                $csv = $this->generateCsv($loans);

                return Response::streamDownload(
                    fn () => print $csv,
                    'loan-applications-'.now()->format('Y-m-d-His').'.csv',
                    ['Content-Type' => 'text/csv; charset=UTF-8']
                );
            })
            ->successNotificationTitle('Data telah dieksport');
    }

    /**
     * Generate CSV content from loan applications
     *
     * @param  EloquentCollection<int, LoanApplication>  $loans
     */
    private function generateCsv(EloquentCollection $loans): string
    {
        $output = fopen('php://temp', 'r+');
        if ($output === false) {
            return '';
        }

        // Add UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // CSV Headers (Bahasa Melayu - ICTServe v3.6.0)
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
            $divisionName = $loan->division ? $loan->division->name_ms : '-';
            fputcsv($output, [
                $loan->application_number,
                $loan->applicant_name,
                $loan->applicant_email,
                $loan->applicant_phone ?? '-',
                $loan->staff_id ?? '-',
                $loan->grade ?? '-',
                $divisionName,
                $this->getStatusLabel($loan->status),
                $this->getPriorityLabel($loan->priority),
                $loan->purpose,
                $loan->location ?? '-',
                $loan->loan_start_date ? $loan->loan_start_date->format('d/m/Y') : '-',
                $loan->loan_end_date ? $loan->loan_end_date->format('d/m/Y') : '-',
                number_format((float) ($loan->total_value ?? 0), 2, '.', ''),
                $this->getApprovalStatus($loan),
                $loan->approved_by_name ?? '-',
                $loan->approved_at ? $loan->approved_at->format('d/m/Y H:i') : '-',
                $loan->approval_method ? ucfirst($loan->approval_method) : '-',
                $loan->rejected_reason ?? '-',
                $loan->special_instructions ?? '-',
                $loan->user_id ? 'Authenticated' : 'Guest',
                $loan->created_at ? $loan->created_at->format('d/m/Y H:i') : '-',
            ]);
        }

        rewind($output);
        /** @var string $csv */
        $csv = stream_get_contents($output) ?: '';
        fclose($output);

        return $csv;
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel(string|UnitEnum|null $status): string
    {
        if ($status instanceof UnitEnum) {
            if (method_exists($status, 'label')) {
                /** @var callable $callable */
                $callable = [$status, 'label'];

                return (string) \call_user_func($callable);
            }

            if ($status instanceof \BackedEnum) {
                return ucfirst(str_replace('_', ' ', (string) $status->value));
            }

            return ucfirst(str_replace('_', ' ', $status->name));
        }

        if (\is_string($status)) {
            return ucfirst(str_replace('_', ' ', $status));
        }

        return '-';
    }

    /**
     * Get human-readable priority label
     */
    private function getPriorityLabel(string|UnitEnum|null $priority): string
    {
        if ($priority instanceof UnitEnum) {
            if (method_exists($priority, 'label')) {
                /** @var callable $callable */
                $callable = [$priority, 'label'];

                return (string) \call_user_func($callable);
            }

            if ($priority instanceof \BackedEnum) {
                return ucfirst(str_replace('_', ' ', (string) $priority->value));
            }

            return ucfirst(str_replace('_', ' ', $priority->name));
        }

        if (\is_string($priority)) {
            return ucfirst(str_replace('_', ' ', $priority));
        }

        return '-';
    }

    /**
     * Get approval status text
     */
    private function getApprovalStatus(LoanApplication $loan): string
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
