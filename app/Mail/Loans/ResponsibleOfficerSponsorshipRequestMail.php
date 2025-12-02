<?php

declare(strict_types=1);

namespace App\Mail\Loans;

use App\Mail\BaseMailable;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Responsible Officer Sponsorship Request Mail
 *
 * Notifies the responsible officer about a loan application made on their behalf.
 * Requirement 1A: Responsible Officer Delegation Workflow
 */
class ResponsibleOfficerSponsorshipRequestMail extends BaseMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LoanApplication $application;

    public string $acknowledgeUrl;

    public function __construct(LoanApplication $application)
    {
        $this->application = $application;
        
        // Generate secure acknowledge URL with token
        $this->acknowledgeUrl = route('loan.sponsorship.acknowledge', [
            'token' => $application->sponsorship_token,
        ]);
    }

    public function build(): self
    {
        $locale = session('locale', 'ms');
        
        return $this
            ->subject($locale === 'en' 
                ? 'Sponsorship Request - Loan Application ' . $this->application->application_number
                : 'Permintaan Penajaan - Permohonan Pinjaman ' . $this->application->application_number)
            ->markdown('emails.loans.sponsorship-request', [
                'application' => $this->application,
                'acknowledgeUrl' => $this->acknowledgeUrl,
                'expiresAt' => $this->application->sponsorship_token_expires_at,
                'locale' => $locale,
            ]);
    }
}
