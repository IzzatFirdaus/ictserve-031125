<?php

namespace App\Services;

use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResponsibleOfficerService
{
    /**
     * Handle the initial setup for a delegated application.
     */
    public function handleDelegatedApplication(LoanApplication $application): void
    {
        if ($application->is_applicant_responsible) {
            return;
        }

        // Generate sponsorship token
        $token = Str::random(64);
        $application->sponsorship_token = $token;
        $application->sponsorship_token_expires_at = now()->addHours(48);
        $application->save();

        // Trigger email to responsible officer (handled by event/listener or caller)
        Log::info("Sponsorship token generated for application {$application->application_number}");
    }

    /**
     * Acknowledge sponsorship using the token.
     */
    public function acknowledgeSponsorshipToken(string $token): ?LoanApplication
    {
        $application = LoanApplication::where('sponsorship_token', $token)
            ->where('sponsorship_token_expires_at', '>', now())
            ->whereNull('responsible_officer_acknowledged_at')
            ->first();

        if (! $application) {
            return null;
        }

        $application->responsible_officer_acknowledged_at = now();
        // Clear token to prevent reuse? Or keep for audit?
        // Usually good to keep or mark as used.
        // We check whereNull('responsible_officer_acknowledged_at') so it's effectively single use.
        $application->save();

        Log::info("Sponsorship acknowledged for application {$application->application_number}");

        return $application;
    }

    /**
     * Get the party responsible for the loan (Applicant or Responsible Officer).
     */
    public function getResponsibleParty(LoanApplication $application): array
    {
        if ($application->is_applicant_responsible) {
            return [
                'name' => $application->applicant_name,
                'email' => $application->applicant_email,
                'phone' => $application->applicant_phone,
                'position' => $application->applicant_position,
                'grade' => $application->applicant_grade,
                'type' => 'applicant',
            ];
        }

        return [
            'name' => $application->responsible_officer_name,
            'email' => $application->responsible_officer_email,
            'phone' => $application->responsible_officer_phone,
            'position' => $application->responsible_officer_position,
            'grade' => $application->responsible_officer_grade,
            'type' => 'officer',
        ];
    }
}
