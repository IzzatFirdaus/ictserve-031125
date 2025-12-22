<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ResponsibleOfficerServiceInterface;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Responsible Officer Service for ICTServe v3.5.0
 *
 * Manages the "Pegawai Bertanggungjawab" (Responsible Officer) functionality
 * for loan applications as per PK.(S).MOTAC.07.(L3) Part 2.
 *
 * @see D03 SRS-LOAN-001 Responsible Officer Requirements
 * @see PK.(S).MOTAC.07.(L3) Part 2 - Responsible Officer Section
 * @see Requirements 25.1, 25.2, 25.3, 25.4
 */
class ResponsibleOfficerService implements ResponsibleOfficerServiceInterface
{
    /**
     * Set the Responsible Officer details on a loan application
     *
     * @param  LoanApplication  $app  The loan application to update
     * @param  array{name: string, grade: string, phone: string, email?: string, position?: string}  $officerData
     *
     * @see Requirements 25.4 - Responsible Officer data storage
     */
    

/**
 * @param array<string, mixed> $officerData
 */
public function setResponsibleOfficer(LoanApplication $app, array $officerData): void
    {
        $app->is_applicant_responsible = false;
        $app->responsible_officer_name = $officerData['name'];
        $app->responsible_officer_grade = $officerData['grade'];
        $app->responsible_officer_phone = $officerData['phone'];
        $app->responsible_officer_email = $officerData['email'] ?? null;
        $app->responsible_officer_position = $officerData['position'] ?? null;
        $app->save();

        Log::info("Responsible Officer set for application {$app->application_number}", [
            'application_id' => $app->id,
            'officer_name' => $officerData['name'],
        ]);
    }

    /**
     * Copy Applicant data as Responsible Officer
     *
     * @see Requirements 25.3 - Auto-populate from Applicant data
     */
    public function copyApplicantAsResponsibleOfficer(LoanApplication $app): void
    {
        $app->is_applicant_responsible = true;
        $app->responsible_officer_name = $app->applicant_name;
        $app->responsible_officer_grade = $app->applicant_grade ?? $app->grade;
        $app->responsible_officer_phone = $app->applicant_phone;
        $app->responsible_officer_email = $app->applicant_email;
        $app->responsible_officer_position = $app->applicant_position ?? null;
        $app->save();

        Log::info("Applicant copied as Responsible Officer for application {$app->application_number}", [
            'application_id' => $app->id,
        ]);
    }

    /**
     * Get the Responsible Officer details for a loan application
     *
     * @return array{name: string, grade: string, phone: string, email: string|null, position: string|null, type: string}
     *
     * @see Requirements 25.5 - Display Responsible Officer information
     */
    public function getResponsibleOfficerDetails(LoanApplication $app): array
    {
        if ($app->is_applicant_responsible) {
            return [
                'name' => $app->applicant_name,
                'grade' => $app->applicant_grade ?? $app->grade ?? '',
                'phone' => $app->applicant_phone,
                'email' => $app->applicant_email,
                'position' => $app->applicant_position,
                'type' => 'applicant',
            ];
        }

        return [
            'name' => $app->responsible_officer_name ?? '',
            'grade' => $app->responsible_officer_grade ?? '',
            'phone' => $app->responsible_officer_phone ?? '',
            'email' => $app->responsible_officer_email,
            'position' => $app->responsible_officer_position,
            'type' => 'officer',
        ];
    }

    /**
     * Check if the Applicant is the Responsible Officer
     *
     * @see Requirements 25.2 - Conditional fields toggle
     */
    public function isApplicantResponsible(LoanApplication $app): bool
    {
        return (bool) $app->is_applicant_responsible;
    }

    /**
     * Handle delegated application workflow
     *
     * When the Responsible Officer is different from the Applicant,
     * this method initiates the sponsorship workflow.
     */
    public function handleDelegatedApplication(LoanApplication $app): void
    {
        if ($app->is_applicant_responsible) {
            return;
        }

        // Generate sponsorship token for acknowledgement workflow
        $token = Str::random(64);
        $app->sponsorship_token = $token;
        $app->sponsorship_token_expires_at = now()->addHours(48);
        $app->save();

        Log::info("Sponsorship token generated for application {$app->application_number}", [
            'application_id' => $app->id,
            'expires_at' => $app->sponsorship_token_expires_at,
        ]);
    }

    /**
     * Acknowledge sponsorship using the token
     */
    public function acknowledgeSponsorshipToken(string $token): ?LoanApplication
    {
        $application = LoanApplication::where('sponsorship_token', $token)
            ->where('sponsorship_token_expires_at', '>', now())
            ->whereNull('responsible_officer_acknowledged_at')
            ->first();

        if (! $application) {
            Log::warning('Invalid or expired sponsorship token attempted', [
                'token_prefix' => substr($token, 0, 8).'...',
            ]);

            return null;
        }

        $application->responsible_officer_acknowledged_at = now();
        $application->save();

        Log::info("Sponsorship acknowledged for application {$application->application_number}", [
            'application_id' => $application->id,
            'acknowledged_at' => $application->responsible_officer_acknowledged_at,
        ]);

        return $application;
    }

    /**
     * Get the party responsible for the loan
     *
     * @return array{name: string, email: string|null, phone: string|null, position: string|null, grade: string|null, type: string}
     */
    public function getResponsibleParty(LoanApplication $app): array
    {
        if ($app->is_applicant_responsible) {
            return [
                'name' => $app->applicant_name,
                'email' => $app->applicant_email,
                'phone' => $app->applicant_phone,
                'position' => $app->applicant_position,
                'grade' => $app->applicant_grade ?? $app->grade,
                'type' => 'applicant',
            ];
        }

        return [
            'name' => $app->responsible_officer_name ?? '',
            'email' => $app->responsible_officer_email,
            'phone' => $app->responsible_officer_phone,
            'position' => $app->responsible_officer_position,
            'grade' => $app->responsible_officer_grade,
            'type' => 'officer',
        ];
    }

    /**
     * Validate Responsible Officer data
     */
    

/**
 * @param array<string, mixed> $officerData
 */
public function validateResponsibleOfficerData(array $officerData): bool
    {
        // Required fields: name, grade, phone
        if (empty($officerData['name']) || ! is_string($officerData['name'])) {
            return false;
        }

        if (empty($officerData['grade']) || ! is_string($officerData['grade'])) {
            return false;
        }

        if (empty($officerData['phone']) || ! is_string($officerData['phone'])) {
            return false;
        }

        // Validate phone format (Malaysian format)
        $phone = preg_replace('/[^0-9]/', '', $officerData['phone']);
        if ($phone === null || strlen($phone) < 9 || strlen($phone) > 12) {
            return false;
        }

        // Validate email if provided
        if (! empty($officerData['email']) && ! filter_var($officerData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

    /**
     * Clear Responsible Officer data
     */
    public function clearResponsibleOfficer(LoanApplication $app): void
    {
        $app->is_applicant_responsible = true;
        $app->responsible_officer_name = null;
        $app->responsible_officer_grade = null;
        $app->responsible_officer_phone = null;
        $app->responsible_officer_email = null;
        $app->responsible_officer_position = null;
        $app->sponsorship_token = null;
        $app->sponsorship_token_expires_at = null;
        $app->responsible_officer_acknowledged_at = null;
        $app->save();

        Log::info("Responsible Officer data cleared for application {$app->application_number}", [
            'application_id' => $app->id,
        ]);
    }
}
