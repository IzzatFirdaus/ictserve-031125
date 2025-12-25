<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PKS 5.2.1 Compliant Loan Application Service
 *
 * SSO-only architecture - all loan applications require authenticated users.
 * Guest submission functionality has been removed per PKS 5.2.1 Accountability requirements.
 *
 * @see D03-FR-001.1 Application creation (authenticated only)
 * @see D04 §2.1 Business logic services
 *
 * @trace Requirements 1.4, 1.5, 9.1, 25.1
 */
class LoanApplicationService
{
    public function __construct(
        private DualApprovalService $approvalService,
        private NotificationService $notificationService
    ) {}

    /**
     * Create loan application (authenticated users only)
     *
     * PKS 5.2.1: All applications MUST have a mandatory user_id (NOT NULL).
     *
     * @param  array<string, mixed>  $data  Application data
     * @param  User  $user  Authenticated user (REQUIRED)
     *
     * @throws \Exception
     *
     * @trace Requirements 1.4, 1.5, 9.1, 25.1
     */
    public function createApplication(array $data, User $user): LoanApplication
    {
        DB::beginTransaction();

        try {
            // Create loan application with mandatory user_id
            $application = LoanApplication::create([
                'application_number' => LoanApplication::generateApplicationNumber(),
                'user_id' => $user->id, // MANDATORY per PKS 5.2.1
                'staff_id' => $data['staff_id'] ?? $user->staff_id,
                'grade' => $data['grade'] ?? $user->grade,
                'division_id' => $data['division_id'] ?? $user->division_id,
                // BAHAGIAN 1: Extended applicant info
                'applicant_position' => $data['applicant_position'] ?? $user->position ?? null,
                'applicant_grade' => $data['applicant_grade'] ?? $user->grade ?? null,
                'purpose' => $data['purpose'],
                'location' => $data['location'],
                'loan_start_date' => $data['loan_start_date'],
                'expected_return_date' => $data['expected_return_date'] ?? $data['loan_end_date'],
                // BAHAGIAN 2: Responsible officer (conditional)
                'is_responsible_officer' => $data['is_responsible_officer'] ?? true,
                'responsible_officer_name' => $data['responsible_officer_name'] ?? null,
                'responsible_officer_position' => $data['responsible_officer_position'] ?? null,
                'responsible_officer_grade' => $data['responsible_officer_grade'] ?? null,
                'responsible_officer_phone' => $data['responsible_officer_phone'] ?? null,
                // BAHAGIAN 4: Applicant declaration
                'applicant_digital_signature' => $data['applicant_digital_signature'] ?? null,
                'applicant_declaration_date' => now(),
                'terms_acknowledged' => $data['terms_acknowledged'] ?? false,
                // Application details
                'return_location' => $data['return_location'] ?? $data['location'],
                'loan_end_date' => $data['loan_end_date'] ?? $data['expected_return_date'],
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

            Log::info('Loan application created (PKS 5.2.1 compliant)', [
                'application_number' => $application->application_number,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return $application;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create loan application', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Create loan items for application
     *
     * @param  array<int|array<string, mixed>>  $items  Array of asset IDs or asset data
     */
    private function createLoanItems(LoanApplication $application, array $items): void
    {
        foreach ($items as $item) {
            // Handle both asset_id format and equipment_type format
            $assetId = \is_array($item) ? ($item['asset_id'] ?? null) : $item;
            $quantity = \is_array($item) ? ($item['quantity'] ?? 1) : 1;
            $equipmentType = \is_array($item) ? ($item['equipment_type'] ?? null) : null;

            // For equipment_type (category_id), find available asset
            if ($equipmentType && ! $assetId) {
                $asset = \App\Models\Asset::where('category_id', $equipmentType)
                    ->where('status', 'available')
                    ->first();

                if (! $asset) {
                    throw new \Exception("No available asset found for category ID: {$equipmentType}");
                }
            } else {
                $asset = \App\Models\Asset::findOrFail($assetId);
            }

            LoanItem::create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'equipment_type' => $equipmentType ?? $asset->category_id,
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
            'special_instructions' => \trim((string) $application->special_instructions) !== ''
                ? $application->special_instructions."\nExtension requested: {$justification}"
                : "Extension requested: {$justification}",
        ]);

        Log::info('Loan extension requested', [
            'application_number' => $application->application_number,
            'new_end_date' => $newEndDate,
        ]);
    }

    /**
     * Check if user can access loan application
     *
     * PKS 5.2.1: Access is based on user_id ownership only.
     */
    public function canUserAccessApplication(LoanApplication $application, User $user): bool
    {
        return $application->user_id === $user->id;
    }

    /**
     * Get user's loan applications
     *
     * PKS 5.2.1: Returns only applications where user_id matches.
     *
     * @return \Illuminate\Database\Eloquent\Builder<LoanApplication>
     */
    public function getUserApplications(User $user): \Illuminate\Database\Eloquent\Builder
    {
        return LoanApplication::query()
            ->where('user_id', $user->id)
            ->with(['loanItems.asset', 'division'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get loan application statistics for user
     *
     * @return array<string, int>
     */
    public function getUserApplicationStats(User $user): array
    {
        $baseQuery = LoanApplication::query()->where('user_id', $user->id);

        return [
            'total' => (clone $baseQuery)->count(),
            'submitted' => (clone $baseQuery)->where('status', LoanStatus::SUBMITTED)->count(),
            'approved' => (clone $baseQuery)->where('status', LoanStatus::APPROVED)->count(),
            'rejected' => (clone $baseQuery)->where('status', LoanStatus::REJECTED)->count(),
            'in_use' => (clone $baseQuery)->where('status', LoanStatus::IN_USE)->count(),
            'returned' => (clone $baseQuery)->where('status', LoanStatus::RETURNED)->count(),
        ];
    }
}
