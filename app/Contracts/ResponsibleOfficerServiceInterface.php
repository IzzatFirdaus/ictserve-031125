<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\LoanApplication;

/**
 * Responsible Officer Service Interface for ICTServe v3.5.0
 *
 * Manages the "Pegawai Bertanggungjawab" (Responsible Officer) functionality
 * for loan applications as per PK.(S).MOTAC.07.(L3) Part 2.
 *
 * The Responsible Officer is the staff member accountable for the loaned
 * equipment's use, safety, and any damage during the loan period. This may
 * be the same person as the Applicant (default) or a different designated officer.
 *
 * Key Features:
 * - Set/update Responsible Officer details on loan applications
 * - Auto-populate Responsible Officer from Applicant data when same person
 * - Retrieve Responsible Officer details for display and notifications
 * - Check if Applicant is the Responsible Officer
 *
 * @see D03 SRS-LOAN-001 Responsible Officer Requirements
 * @see PK.(S).MOTAC.07.(L3) Part 2 - Responsible Officer Section
 * @see Requirements 25.1, 25.2, 25.3, 25.4
 */
interface ResponsibleOfficerServiceInterface
{
    /**
     * Set the Responsible Officer details on a loan application
     *
     * Updates the loan application with the provided Responsible Officer
     * information. This is used when the Responsible Officer is different
     * from the Applicant.
     *
     * @param  LoanApplication  $app  The loan application to update
     * @param  array{name: string, grade: string, phone: string, email?: string, position?: string}  $officerData  Responsible Officer details
     *
     * @see Requirements 25.4 - Responsible Officer data storage
     */
    

/**
 * @param array<string, mixed> $officerData
 */
public function setResponsibleOfficer(LoanApplication $app, array $officerData): void;

    /**
     * Copy Applicant data as Responsible Officer
     *
     * When the Applicant is the same as the Responsible Officer (default case),
     * this method copies the Applicant's details to the Responsible Officer fields.
     * Sets is_applicant_responsible to true.
     *
     * @param  LoanApplication  $app  The loan application to update
     *
     * @see Requirements 25.3 - Auto-populate from Applicant data
     */
    public function copyApplicantAsResponsibleOfficer(LoanApplication $app): void;

    /**
     * Get the Responsible Officer details for a loan application
     *
     * Returns the details of the person responsible for the loaned equipment.
     * If is_applicant_responsible is true, returns Applicant details.
     * Otherwise, returns the designated Responsible Officer details.
     *
     * @param  LoanApplication  $app  The loan application to query
     * @return array{name: string, grade: string, phone: string, email: string|null, position: string|null, type: string}
     *
     * @see Requirements 25.5 - Display Responsible Officer information
     */
    public function getResponsibleOfficerDetails(LoanApplication $app): array;

    /**
     * Check if the Applicant is the Responsible Officer
     *
     * Returns true if the Applicant and Responsible Officer are the same person.
     * This is the default case when the "Applicant is same as Responsible Officer"
     * checkbox is checked in the loan application form.
     *
     * @param  LoanApplication  $app  The loan application to check
     * @return bool True if Applicant is the Responsible Officer
     *
     * @see Requirements 25.2 - Conditional fields toggle
     */
    public function isApplicantResponsible(LoanApplication $app): bool;

    /**
     * Handle delegated application workflow
     *
     * When the Responsible Officer is different from the Applicant,
     * this method initiates the sponsorship workflow by generating
     * a sponsorship token and preparing for acknowledgement.
     *
     * @param  LoanApplication  $app  The loan application to process
     */
    public function handleDelegatedApplication(LoanApplication $app): void;

    /**
     * Acknowledge sponsorship using the token
     *
     * Validates the sponsorship token and marks the Responsible Officer
     * as having acknowledged their responsibility for the loan.
     *
     * @param  string  $token  The sponsorship token to validate
     * @return LoanApplication|null The application if token is valid, null otherwise
     */
    public function acknowledgeSponsorshipToken(string $token): ?LoanApplication;

    /**
     * Get the party responsible for the loan
     *
     * Returns the details of the accountable party (either Applicant
     * or designated Responsible Officer) for notifications and display.
     *
     * @param  LoanApplication  $app  The loan application to query
     * @return array{name: string, email: string|null, phone: string|null, position: string|null, grade: string|null, type: string}
     */
    public function getResponsibleParty(LoanApplication $app): array;

    /**
     * Validate Responsible Officer data
     *
     * Validates that the provided Responsible Officer data meets
     * the minimum requirements (name, grade, phone).
     *
     * @param  array<string, mixed>  $officerData  The data to validate
     * @return bool True if data is valid
     */
    

/**
 * @param array<string, mixed> $officerData
 */
public function validateResponsibleOfficerData(array $officerData): bool;

    /**
     * Clear Responsible Officer data
     *
     * Removes the Responsible Officer data from a loan application,
     * typically used when switching from delegated to self-responsible.
     *
     * @param  LoanApplication  $app  The loan application to update
     */
    public function clearResponsibleOfficer(LoanApplication $app): void;
}
