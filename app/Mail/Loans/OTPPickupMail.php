<?php

declare(strict_types=1);

namespace App\Mail\Loans;

use App\Models\LoanApplication;
use Illuminate\Mail\Mailable;

/**
 * OTP Pickup Mail
 *
 * Sends the 4-digit OTP to the applicant for asset pickup verification.
 * Requirement 3A: OTP Handshake for Digital Signature
 */
class OTPPickupMail extends Mailable
{
    public string $otp;

    public LoanApplication $application;

    public function __construct(LoanApplication $application, string $otp)
    {
        $this->application = $application;
        $this->otp = $otp;
    }

    public function build(): self
    {
        $locale = session('locale', 'ms');

        return $this
            ->subject($locale === 'en'
                ? 'Asset Pickup OTP - '.$this->application->application_number
                : 'OTP Pengambilan Aset - '.$this->application->application_number)
            ->markdown('emails.loans.otp-pickup', [
                'application' => $this->application,
                'otp' => $this->otp,
                'expiresAt' => $this->application->pickup_otp_expires_at,
                'locale' => $locale,
            ]);
    }
}
