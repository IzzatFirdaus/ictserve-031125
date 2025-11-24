<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

use function random_int;

class OTPHandoverService
{
    private const OTP_LENGTH = 4;

    private const OTP_MAX_VALUE = 9999;

    private const OTP_EXPIRY_HOURS = 24;

    /**
     * Generate a new 4-digit OTP for pickup.
     *
     * @return string The plain text OTP (to be sent via email)
     */
    public function generatePickupOTP(LoanApplication $application): string
    {
        try {
            $otp = str_pad((string) random_int(0, self::OTP_MAX_VALUE), self::OTP_LENGTH, '0', STR_PAD_LEFT);
        } catch (Throwable $e) {
            Log::error('Failed to generate secure OTP value', ['error' => $e->getMessage()]);
            throw new RuntimeException('Failed to generate OTP', 0, $e);
        }

        $generatedAt = now();
        $expiresAt = $generatedAt->copy()->addHours(self::OTP_EXPIRY_HOURS);

        try {
            $application->pickup_otp_hash = Hash::make($otp);
            $application->pickup_otp_expires_at = $expiresAt;
            $application->pickup_otp_attempts = 0;
            $application->pickup_otp_generated_at = $generatedAt;
            $application->save();

            Log::info(
                'OTP generated for loan application pickup',
                $this->logContext($application, ['expires_at' => $expiresAt])
            );

            return $otp;
        } catch (Throwable $e) {
            Log::error(
                'Failed to save OTP for loan application pickup',
                $this->logContext($application, ['error' => $e->getMessage()])
            );
            throw new RuntimeException('Failed to generate OTP', 0, $e);
        }
    }

    /**
     * Validate the provided OTP.
     *
     * @param  User|null  $validator  The user validating the OTP (admin/officer)
     */
    public function validatePickupOTP(LoanApplication $application, string $otp, ?User $validator = null): bool
    {
        try {
            if ($application->pickup_otp_hash === null || $application->pickup_otp_expires_at === null) {
                Log::warning(
                    'OTP validation attempted without an active OTP',
                    $this->logContext($application)
                );

                return false;
            }

            if ($application->isOtpLocked()) {
                Log::warning(
                    'OTP validation attempted on locked application',
                    $this->logContext($application, ['attempts' => $application->pickup_otp_attempts])
                );

                return false;
            }

            if ($application->pickup_otp_expires_at < now()) {
                Log::info(
                    'Expired OTP validation attempted for application',
                    $this->logContext($application, ['expires_at' => $application->pickup_otp_expires_at])
                );

                return false;
            }
        } catch (Throwable $e) {
            Log::error(
                'Failed to check OTP status for validation',
                $this->logContext($application, ['error' => $e->getMessage()])
            );

            return false;
        }

        try {
            if (Hash::check($otp, $application->pickup_otp_hash)) {
                // Success
                $application->pickup_otp_validated_at = now();
                $application->pickup_otp_validated_by = $validator?->id;
                $application->save();

                Log::info(
                    'OTP validated successfully for loan application',
                    $this->logContext($application, ['validated_by' => $validator?->id ?? 'system'])
                );

                return true;
            }
        } catch (Throwable $e) {
            Log::error(
                'Failed to validate OTP for loan application',
                $this->logContext($application, ['error' => $e->getMessage()])
            );
            throw new RuntimeException('Failed to validate OTP', 0, $e);
        }

        // Failure
        try {
            $application->incrementOtpAttempts();
            Log::info(
                'Invalid OTP attempt for loan application',
                $this->logContext($application, ['attempts' => $application->pickup_otp_attempts])
            );
        } catch (Throwable $e) {
            Log::error(
                'Failed to increment OTP attempts for loan application',
                $this->logContext($application, ['error' => $e->getMessage()])
            );
        }

        return false;
    }

    /**
     * Regenerate OTP if expired or locked (requires admin override for locked).
     *
     * @return string New OTP
     */
    public function regenerateOTP(LoanApplication $application): string
    {
        try {
            $application->clearOtp();

            return $this->generatePickupOTP($application);
        } catch (Throwable $e) {
            Log::error(
                'Failed to regenerate OTP for loan application',
                $this->logContext($application, ['error' => $e->getMessage()])
            );
            throw new RuntimeException('Failed to regenerate OTP', 0, $e);
        }
    }

    /**
     * Generate return receipt HTML content with ISO document ID.
     *
     * @return string HTML content
     */
    public function generateReturnReceipt(LoanApplication $application): string
    {
        // In a real implementation, this would render a Blade view to HTML
        // and potentially convert to PDF using dompdf or snappy.
        // For now, we return the HTML string.

        $isoId = e('PK.(S).MOTAC.07.(L3)');
        $date = e(now()->format('d/m/Y H:i:s'));
        $applicationNumber = e($application->application_number);
        $applicantName = e($application->applicant_name);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: sans-serif; }
                .header { text-align: center; margin-bottom: 20px; }
                .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10pt; }
                .content { margin: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Loan Return Receipt / Resit Pemulangan Pinjaman</h2>
                <p>Application No: {$applicationNumber}</p>
            </div>
            
            <div class="content">
                <p>Date: {$date}</p>
                <p>Applicant: {$applicantName}</p>
                <p>Items Returned:</p>
                <ul>
                    <!-- Iterate items here -->
                </ul>
            </div>

            <div class="footer">
                <p>MOTAC BPM Official Document | {$isoId}</p>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Provide standard context for OTP logging.
     *
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function logContext(LoanApplication $application, array $extra = []): array
    {
        return array_merge([
            'application_id' => $application->id,
            'application_number' => $application->application_number,
        ], $extra);
    }
}
