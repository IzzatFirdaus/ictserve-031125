<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;

/**
 * Loan Service Interface
 *
 * Defines contract for loan application management with hybrid architecture support.
 *
 * @see D03 SRS-LOAN-001, SRS-LOAN-002, SRS-LOAN-003
 * @see Requirements 3.2, 3.4, 4.5, 6.1, 6.2, 6.3
 */
interface LoanServiceInterface
{
    /**
     * Create a new loan application with hybrid user_id logic
     *
     * Implements True Hybrid Architecture:
     * - If Auth::check() === true: Links to user_id, auto-fills from profile
     * - If Auth::check() === false: Sets user_id = NULL, uses guest fields
     *
     * @param  array  $data  Application data
     * @param  User|null  $user  Authenticated user (null for guest)
     * @return LoanApplication The created application with status token
     *
     * @throws \Exception If application creation fails
     */
    public function createApplication(array $data, ?User $user = null): LoanApplication;

    /**
     * Check asset availability with conflict detection
     *
     * Validates that requested assets are available for the specified date range.
     * Detects conflicts with existing approved/active loan applications.
     *
     * @param  array  $assetIds  Array of asset IDs to check
     * @param  Carbon  $startDate  Loan start date
     * @param  Carbon  $endDate  Loan end date
     * @param  int|null  $excludeApplicationId  Exclude this application from conflict check
     * @return array{available: bool, conflicts: array, alternatives: array}
     *
     * @see Requirements 3.2 Real-time availability checking
     */
    public function checkAssetAvailability(
        array $assetIds,
        Carbon $startDate,
        Carbon $endDate,
        ?int $excludeApplicationId = null
    ): array;

    /**
     * Process approval decision for loan application
     *
     * Updates application status based on approval decision.
     * Sends notifications to applicant and admin.
     *
     * @param  LoanApplication  $application  The loan application
     * @param  string  $decision  'approved' or 'rejected'
     * @param  string|null  $remarks  Optional approval/rejection remarks
     * @param  string|null  $approverEmail  Email of approver (for guest approval)
     *
     * @throws \Exception If approval processing fails
     *
     * @see Requirements 4.5 Approval status update
     */
    public function processApproval(
        LoanApplication $application,
        string $decision,
        ?string $remarks = null,
        ?string $approverEmail = null
    ): void;

    /**
     * Process asset check-out transaction
     *
     * Records asset issuance to applicant with condition notes.
     * Updates asset status and loan application status.
     *
     * @param  LoanApplication  $application  The loan application
     * @param  User  $admin  Admin performing check-out
     * @param  array  $transactionData  Transaction data including:
     *                                  - condition_notes (optional)
     *                                  - accessories (optional array)
     *
     * @throws \Exception If check-out fails
     *
     * @see Requirements 6.1 Check-out transaction recording
     */
    public function checkOut(
        LoanApplication $application,
        User $admin,
        array $transactionData = []
    ): void;

    /**
     * Process asset check-in transaction
     *
     * Records asset return from applicant with condition assessment.
     * Updates asset status and loan application status.
     * Creates maintenance ticket if damage is reported.
     *
     * @param  LoanApplication  $application  The loan application
     * @param  User  $admin  Admin performing check-in
     * @param  array  $returnData  Return data including:
     *                             - condition_notes (optional)
     *                             - damage_reported (boolean)
     *                             - damage_photos (optional array)
     *                             - accessories (optional array)
     *
     * @throws \Exception If check-in fails
     *
     * @see Requirements 6.2 Check-in status update
     */
    public function checkIn(
        LoanApplication $application,
        User $admin,
        array $returnData = []
    ): void;

    /**
     * Create maintenance ticket for damaged asset
     *
     * Automatically creates a helpdesk maintenance ticket when asset damage is reported.
     * Links ticket to loan application for cross-module integration.
     *
     * @param  LoanApplication  $application  The loan application
     * @param  Asset  $asset  The damaged asset
     * @param  string  $damageDescription  Description of damage
     * @param  array  $damagePhotos  Optional array of photo paths
     * @return HelpdeskTicket The created maintenance ticket
     *
     * @throws \Exception If ticket creation fails
     *
     * @see Requirements 6.3 Automatic maintenance ticket on damage
     */
    public function createMaintenanceTicket(
        LoanApplication $application,
        Asset $asset,
        string $damageDescription,
        array $damagePhotos = []
    ): HelpdeskTicket;
}
