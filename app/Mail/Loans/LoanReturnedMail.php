<?php

declare(strict_types=1);

namespace App\Mail\Loans;

use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoanReturnedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public LoanApplication $application,
        public LoanTransaction $transaction,
        public bool $hasDamagedAssets = false
    ) {
        $this->queue = 'emails';
        $this->delay = now()->addSeconds(5);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aset Telah Dipulangkan - '.$this->application->application_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.loans.loan-returned',
            with: [
                'application' => $this->application,
                'transaction' => $this->transaction,
                'applicantName' => $this->application->applicant_name,
                'applicationNumber' => $this->application->application_number,
                'returnedAt' => $this->transaction->transaction_date->format('d/m/Y H:i'),
                'returnedBy' => $this->transaction->returned_by_name,
                'assets' => $this->application->loanItems->map(fn ($item) => $item->asset->name)->join(', '),
                'accessoriesReturned' => $this->formatAccessories($this->transaction->accessories_returned ?? []),
                'missingAccessories' => $this->transaction->missing_accessories,
                'returnNotes' => $this->transaction->return_notes,
                'hasDamagedAssets' => $this->hasDamagedAssets,
            ],
        );
    }

    private function formatAccessories(array $accessories): string
    {
        if (empty($accessories)) {
            return 'Tiada aksesori';
        }

        $labels = [
            'power_adapter' => 'Penyesuai Kuasa',
            'mouse' => 'Tetikus',
            'keyboard' => 'Papan Kekunci',
            'cable' => 'Kabel',
            'bag' => 'Beg',
            'manual' => 'Manual Pengguna',
            'warranty_card' => 'Kad Waranti',
        ];

        return collect($accessories)
            ->map(fn ($key) => $labels[$key] ?? $key)
            ->join(', ');
    }
}
