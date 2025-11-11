<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Loan Application Service
 *
 * Handles creation and management of loan applications with hybrid architecture support.
 *
 * @see D03-FR-001.1 Hybrid application creation
 * @see D03-FR-001.2 Guest and authenticated submissions
 * @see D04 §2.1 Business logic services
 */
class LoanApplicationService
{
    public function __construct(
        private DualApprovalService $approvalService,
        private NotificationService $notificationService
    ) {}

    /**
     * Create hybrid loan application (guest or authenticated)
     *
     * @param  array  $data  Application data
     * @param  User|null  $user  Authenticated user (null for guest)
     *
     * @throws \Exception
     */
    public function createHybridApplication(array $data, ?User $user = null): LoanApplication
    {
        DB::beginTransaction();

        try {
            // Create loan application
            $application = LoanApplication::create([
                'application_number' => LoanApplication::generateApplicationNumber(),
                'user_id' => $user?->id, // Null for guest applications
                // Guest fields (always populated)
                'applicant_name' => $data['applicant_name'],
                'applicant_email' => $data['applicant_email'],
                'applicant_phone' => $data['applicant_phone'],
                'staff_id' => $data['staff_id'],
                'grade' => $data['grade'],
                'division_id' => $data['division_id'],
                // Application details
                'purpose' => $data['purpose'],
                'location' => $data['location'],
                'return_location' => $data['return_location'] ?? $data['location'],
                'loan_start_date' => $data['loan_start_date'],
                'loan_end_date' => $data['loan_end_date'],
                'status' => LoanStatus::SUBMITTED,
                'priority' => $data['priority'] ?? LoanPriority::NORMAL,
                'special_instructions' => $data['special_instructions'] ?? null,
            ]);

            // Create loan items
            $this->createLoanItems($application, $data['items']);

            // Calculate total value
            $this->calculateTotalValue($application);

            // Send confirmation email
            $this->notificationService->sendLoanApplicationConfirmation($application);

            // Route to appropriate approver via email
            $this->approvalService->sendApprovalRequest($application);

            DB::commit();

            Log::info('Loan application created', [
                'application_number' => $application->application_number,
                'user_id' => $user?->id,
                'is_guest' => $application->isGuestSubmission(),
            ]);

            return $application;
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
     * Create loan items for application
     *
     * @param  array  $items  Array of asset IDs or asset data
     */
    private function createLoanItems(LoanApplication $application, array $items): void
    {
        foreach ($items as $item) {
            $assetId = is_array($item) ? $item['asset_id'] : $item;
            $quantity = is_array($item) ? ($item['quantity'] ?? 1) : 1;

            $asset = \App\Models\Asset::findOrFail($assetId);

            LoanItem::create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'quantity' => $quantity,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value * $quantity,
            ]);
        }
    }

    /**
     * Calculate and update total value of loan application
     */
    private function calculateTotalValue(LoanApplication $application): void
    {
        $totalValue = $application->loanItems()->sum('total_value');
        $application->update(['total_value' => $totalValue]);
    }

    /**
     * Update loan application status
     */
    public function updateStatus(LoanApplication $application, LoanStatus $status, ?string $notes = null): void
    {
        $previousStatus = $application->status->value;

        $application->update([
            'status' => $status,
        ]);

        if ($notes) {
            $application->update(['special_instructions' => $notes]);
        }

        // Send status update notification
        $this->notificationService->sendLoanStatusUpdate($application->refresh(), $previousStatus);

        Log::info('Loan application status updated', [
            'application_number' => $application->application_number,
            'status' => $status->value,
        ]);
    }

    /**
     * Approve a loan application (portal-based approval)
     *
     * @see D03-FR-023.2 Approval/rejection actions
     */
    public function approveApplication(
        LoanApplication $application,
        User $approver,
        ?string $remarks = null,
        string $method = 'portal'
    ): void {
        $result = $this->approvalService->processPortalApproval(
            $application,
            $approver,
            true,
            $remarks
        );

        if (! $result['success']) {
            throw new \Exception($result['message']);
        }
    }

    /**
     * Reject a loan application (portal-based rejection)
     *
     * @see D03-FR-023.2 Approval/rejection actions
     */
    public function rejectApplication(
        LoanApplication $application,
        User $approver,
        ?string $remarks = null,
        string $method = 'portal'
    ): void {
        $result = $this->approvalService->processPortalApproval(
            $application,
            $approver,
            false,
            $remarks
        );

        if (! $result['success']) {
            throw new \Exception($result['message']);
        }
    }

    /**
     * Process loan extension request
     *
     * @see D03-FR-011.4 Extension requests keep status IN_USE
     */
    public function requestExtension(LoanApplication $application, string $newEndDate, string $justification): void
    {
        $application->update([
            'loan_end_date' => $newEndDate,
            'special_instructions' => trim((string) $application->special_instructions) !== ''
                ? $application->special_instructions."\nExtension requested: {$justification}"
                : "Extension requested: {$justification}",
        ]);

        Log::info('Loan extension requested', [
            'application_number' => $application->application_number,
            'new_end_date' => $newEndDate,
        ]);
    }

    /**
     * Claim a guest loan application to an authenticated user account.
     *
     * @throws Exception
     */
    public function claimGuestApplication(LoanApplication $application, User $user): bool
    {
        if (! $application->isGuestSubmission()) {
            throw new Exception('Loan application is already linked to an account.');
        }

        if (strtolower($application->applicant_email) !== strtolower($user->email)) {
            throw new Exception('Email does not match the original applicant.');
        }

        $application->update(['user_id' => $user->id]);

        Log::info('Loan application claimed by user', [
            'application_number' => $application->application_number,
            'user_id' => $user->id,
        ]);

        $this->notificationService->sendLoanStatusUpdate($application);

        return true;
    }
}
