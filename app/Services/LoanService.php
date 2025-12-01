<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LoanServiceInterface;
use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Loan Service Implementation
 *
 * Implements loan application management with hybrid architecture support.
 * Handles both guest submissions (user_id = NULL) and authenticated submissions.
 *
 * Features:
 * - Hybrid user_id logic (Auth::check() determines user association)
 * - Real-time asset availability checking with conflict detection
 * - Asset soft-lock on application submission
 * - Check-out/Check-in transaction recording
 * - Automatic maintenance ticket creation for damaged assets
 * - Audit trail logging via dual audit system
 *
 * @see D03 SRS-LOAN-001, SRS-LOAN-002, SRS-LOAN-003
 * @see Requirements 3.2, 3.4, 4.5, 6.1, 6.2, 6.3
 */
class LoanService implements LoanServiceInterface
{
    public function __construct(
        private AssetAvailabilityService $availabilityService,
        private TokenService $tokenService,
        private NotificationService $notificationService,
        private HelpdeskService $helpdeskService
    ) {}

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
    public function createApplication(array $data, ?User $user = null): LoanApplication
    {
        try {
            DB::beginTransaction();

            // Determine if user is authenticated
            $isAuthenticated = $user !== null;

            // Check asset availability before creating application
            $assetIds = $data['asset_ids'] ?? [];
            $startDate = Carbon::parse($data['loan_start_date']);
            $endDate = Carbon::parse($data['loan_end_date']);

            $availabilityCheck = $this->checkAssetAvailability($assetIds, $startDate, $endDate);

            if (! $availabilityCheck['available']) {
                throw new \Exception('One or more requested assets are not available for the specified date range.');
            }

            // Generate application reference
            $reference = LoanApplication::generateReferenceV3();

            // Prepare application data with hybrid logic
            $applicationData = [
                'application_number' => $reference,
                'user_id' => $isAuthenticated ? $user->id : null,
                'form_reference_code' => 'PK.(S).MOTAC.07.(L3)', // Official form code per Req 24
                'purpose' => $data['purpose'],
                'location' => $data['location'],
                'loan_start_date' => $startDate,
                'loan_end_date' => $endDate,
                'expected_return_date' => $data['expected_return_date'] ?? $endDate,
                'status' => LoanStatus::SUBMITTED,
                'priority' => $data['priority'] ?? 'normal',
                'terms_acknowledged' => $data['terms_acknowledged'] ?? false,
            ];

            // Add authenticated user fields
            if ($isAuthenticated) {
                $applicationData['applicant_name'] = $user->name;
                $applicationData['applicant_email'] = $user->email;
                $applicationData['applicant_phone'] = $user->phone ?? null;
                $applicationData['staff_id'] = $user->staff_number ?? null;
                $applicationData['grade'] = $user->grade ?? null;
                $applicationData['division_id'] = $user->division_id ?? null;
            } else {
                // Add guest submission fields
                $applicationData['applicant_name'] = $data['applicant_name'];
                $applicationData['applicant_email'] = $data['applicant_email'];
                $applicationData['applicant_phone'] = $data['applicant_phone'] ?? null;
                $applicationData['staff_id'] = $data['staff_id'] ?? null;
                $applicationData['grade'] = $data['grade'] ?? null;
                $applicationData['division_id'] = $data['division_id'] ?? null;
            }

            // Add Responsible Officer fields (Req 25)
            $applicationData['is_applicant_responsible'] = $data['is_applicant_responsible'] ?? true;

            if (! $applicationData['is_applicant_responsible']) {
                $applicationData['responsible_officer_name'] = $data['responsible_officer_name'];
                $applicationData['responsible_officer_grade'] = $data['responsible_officer_grade'];
                $applicationData['responsible_officer_phone'] = $data['responsible_officer_phone'];
                $applicationData['responsible_officer_email'] = $data['responsible_officer_email'] ?? null;
            }

            // Create loan application
            $application = LoanApplication::create($applicationData);

            // Generate status token for guest status checking (Req 2.1)
            $statusToken = $this->tokenService->generateStatusToken($application);

            // Generate approval token for email-based approval (Req 4.1)
            $approvalToken = $this->tokenService->generateApprovalToken($application, 72);

            // Create loan items for requested assets
            foreach ($assetIds as $assetId) {
                $asset = Asset::findOrFail($assetId);

                $application->loanItems()->create([
                    'asset_id' => $asset->id,
                    'equipment_type' => $asset->category_id,
                    'quantity' => 1,
                    'unit_value' => $asset->current_value,
                    'total_value' => $asset->current_value,
                ]);

                // Soft-lock asset (Req 3.4)
                $asset->update(['status' => 'reserved']);
            }

            // Calculate total value
            $totalValue = $application->loanItems()->sum('total_value');
            $application->update(['total_value' => $totalValue]);

            DB::commit();

            // Send confirmation notification
            $this->notificationService->sendLoanApplicationConfirmation($application);

            // Send approval request to designated approver
            $this->sendApprovalRequest($application, $approvalToken['token']);

            Log::info('Loan application created', [
                'application_number' => $application->application_number,
                'user_id' => $user?->id,
                'is_guest' => $application->isGuestSubmission(),
                'asset_count' => count($assetIds),
            ]);

            return $application->fresh();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create loan application', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

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
    ): array {
        $conflicts = [];
        $alternatives = [];
        $allAvailable = true;

        foreach ($assetIds as $assetId) {
            $result = $this->availabilityService->checkAssetAvailability(
                $assetId,
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $excludeApplicationId
            );

            if (! $result['available']) {
                $allAvailable = false;
                $conflicts[$assetId] = $result['conflicts'];

                // Find alternative asset in same category
                $asset = Asset::find($assetId);
                if ($asset) {
                    $alternative = $this->availabilityService->findAvailableAsset(
                        $asset->category_id,
                        $startDate->format('Y-m-d'),
                        $endDate->format('Y-m-d')
                    );

                    if ($alternative) {
                        $alternatives[$assetId] = [
                            'id' => $alternative->id,
                            'name' => $alternative->name,
                            'asset_tag' => $alternative->asset_tag,
                        ];
                    }
                }
            }
        }

        return [
            'available' => $allAvailable,
            'conflicts' => $conflicts,
            'alternatives' => $alternatives,
        ];
    }

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
    ): void {
        try {
            DB::beginTransaction();

            $previousStatus = $application->status;

            if ($decision === 'approved') {
                $application->update([
                    'status' => LoanStatus::APPROVED,
                    'approved_at' => now(),
                    'approved_by_name' => $approverEmail,
                    'approval_remarks' => $remarks,
                    'approval_method' => 'email',
                ]);

                // Update asset status from reserved to allocated
                foreach ($application->loanItems as $item) {
                    $item->asset->update(['status' => 'allocated']);
                }
            } else {
                $application->update([
                    'status' => LoanStatus::REJECTED,
                    'rejected_at' => now(),
                    'rejected_by' => $approverEmail,
                    'rejection_reason' => $remarks,
                ]);

                // Release reserved assets
                foreach ($application->loanItems as $item) {
                    $item->asset->update(['status' => 'available']);
                }
            }

            DB::commit();

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate($application, $previousStatus->value);

            Log::info('Loan application approval processed', [
                'application_number' => $application->application_number,
                'decision' => $decision,
                'approver_email' => $approverEmail,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process loan approval', [
                'application_number' => $application->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

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
    ): void {
        try {
            DB::beginTransaction();

            // Create check-out transaction for each asset
            foreach ($application->loanItems as $item) {
                LoanTransaction::create([
                    'loan_application_id' => $application->id,
                    'asset_id' => $item->asset_id,
                    'transaction_type' => TransactionType::ISSUE,
                    'performed_by_admin_id' => $admin->id,
                    'performed_at' => now(),
                    'condition_notes' => $transactionData['condition_notes'] ?? null,
                    'damage_reported' => false,
                ]);

                // Update asset status to in_use
                $item->asset->update(['status' => 'in_use']);
            }

            // Update application status
            $application->update([
                'status' => LoanStatus::ISSUED,
            ]);

            DB::commit();

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate($application, LoanStatus::APPROVED->value);

            Log::info('Loan check-out completed', [
                'application_number' => $application->application_number,
                'admin_id' => $admin->id,
                'asset_count' => $application->loanItems->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process loan check-out', [
                'application_number' => $application->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

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
    ): void {
        try {
            DB::beginTransaction();

            $damageReported = $returnData['damage_reported'] ?? false;

            // Create check-in transaction for each asset
            foreach ($application->loanItems as $item) {
                $transaction = LoanTransaction::create([
                    'loan_application_id' => $application->id,
                    'asset_id' => $item->asset_id,
                    'transaction_type' => TransactionType::RETURN,
                    'performed_by_admin_id' => $admin->id,
                    'performed_at' => now(),
                    'condition_notes' => $returnData['condition_notes'] ?? null,
                    'damage_reported' => $damageReported,
                    'damage_photos' => $returnData['damage_photos'] ?? null,
                ]);

                // Update asset status
                if ($damageReported) {
                    $item->asset->update(['status' => 'maintenance']);

                    // Create maintenance ticket (Req 6.3)
                    $this->createMaintenanceTicket(
                        $application,
                        $item->asset,
                        $returnData['condition_notes'] ?? 'Asset returned with damage',
                        $returnData['damage_photos'] ?? []
                    );
                } else {
                    $item->asset->update(['status' => 'available']);
                }
            }

            // Update application status
            $application->update([
                'status' => $damageReported ? LoanStatus::MAINTENANCE_REQUIRED : LoanStatus::RETURNED,
            ]);

            DB::commit();

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate($application, LoanStatus::IN_USE->value);

            Log::info('Loan check-in completed', [
                'application_number' => $application->application_number,
                'admin_id' => $admin->id,
                'damage_reported' => $damageReported,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process loan check-in', [
                'application_number' => $application->application_number,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

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
    ): HelpdeskTicket {
        try {
            // Prepare ticket data
            $ticketData = [
                'category_id' => $this->getMaintenanceCategoryId(),
                'subject' => "Asset Damage Report - {$asset->name} ({$asset->asset_tag})",
                'description' => "Asset returned with damage from loan application {$application->application_number}.\n\n"
                    ."Damage Description: {$damageDescription}\n\n"
                    ."Applicant: {$application->applicant_name}\n"
                    .'Return Date: '.now()->format('Y-m-d H:i:s'),
                'priority' => 'high',
                'asset_tag' => $asset->asset_tag,
                'related_loan_application_id' => $application->id,
                'declaration_accepted' => true,
                // Use application submitter details for ticket
                'guest_name' => $application->applicant_name,
                'guest_email' => $application->applicant_email,
                'guest_phone' => $application->applicant_phone,
            ];

            // Create ticket using HelpdeskService
            $ticket = $this->helpdeskService->createTicket($ticketData);

            // Attach damage photos if provided
            if (! empty($damagePhotos)) {
                foreach ($damagePhotos as $photoPath) {
                    $ticket->attachments()->create([
                        'file_path' => $photoPath,
                        'file_name' => basename($photoPath),
                        'file_type' => 'image',
                    ]);
                }
            }

            Log::info('Maintenance ticket created for damaged asset', [
                'ticket_number' => $ticket->ticket_number,
                'application_number' => $application->application_number,
                'asset_tag' => $asset->asset_tag,
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Failed to create maintenance ticket', [
                'application_number' => $application->application_number,
                'asset_tag' => $asset->asset_tag,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Send approval request email to designated approver
     *
     * @param  LoanApplication  $application  The loan application
     * @param  string  $approvalToken  The approval token
     */
    private function sendApprovalRequest(LoanApplication $application, string $approvalToken): void
    {
        // Find designated approver (Grade 41+ officer)
        $approver = $this->findApprover($application);

        if ($approver) {
            $this->notificationService->sendApprovalRequest($application, $approver, $approvalToken);
        }
    }

    /**
     * Find designated approver for loan application
     *
     * @param  LoanApplication  $application  The loan application
     * @return array{email: string, name: string}|null Approver data
     */
    private function findApprover(LoanApplication $application): ?array
    {
        // Logic to find Grade 41+ officer in applicant's division
        // This is a simplified implementation - actual logic may be more complex
        $approver = User::where('division_id', $application->division_id)
            ->where('grade', '>=', 41)
            ->where('role', 'admin')
            ->first();

        return $approver ? ['email' => $approver->email, 'name' => $approver->name] : null;
    }

    /**
     * Get maintenance category ID for helpdesk tickets
     *
     * @return int Category ID for maintenance tickets
     */
    private function getMaintenanceCategoryId(): int
    {
        // Find or create maintenance category
        $category = \App\Models\TicketCategory::firstOrCreate(
            ['name' => 'Asset Maintenance'],
            [
                'description' => 'Asset maintenance and repair requests',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 48,
                'is_active' => true,
            ]
        );

        return $category->id;
    }
}
