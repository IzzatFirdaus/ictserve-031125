<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Asset Transaction Service
 *
 * Manages asset check-out, check-in, and transaction lifecycle with condition tracking.
 *
 * @see D03-FR-003.2 Asset check-out process
 * @see D03-FR-003.3 Asset check-in process
 * @see D03-FR-018.3 Transaction history
 * @see D04 §5.3 Asset transaction management
 */
class AssetTransactionService
{
    public function __construct(
        private NotificationService $notificationService,
        private CrossModuleIntegrationService $integrationService
    ) {}

    /**
     * Process asset issuance (check-out)
     *
     * @param LoanApplication $application
     * @param array $issuanceData Asset issuance details
     * @return void
     * @throws \Exception
     */
    public function processAssetIssuance(LoanApplication $application, array $issuanceData): void
    {
        DB::beginTransaction();

        try {
            foreach ($application->loanItems as $loanItem) {
                $asset = $loanItem->asset;
                $assetData = $issuanceData['assets'][$asset->id] ?? [];

                // Record condition before issuance
                $conditionBefore = $asset->condition;
                $loanItem->update(['condition_before' => $conditionBefore]);

                // Update asset status
                $asset->update([
                    'status' => AssetStatus::LOANED,
                ]);

                // Create transaction record
                AssetTransaction::create([
                    'asset_id' => $asset->id,
                    'loan_application_id' => $application->id,
                    'type' => 'issue',
                    'user_id' => $application->user_id,
                    'processed_by' => auth()->id(),
                    'condition_before' => $conditionBefore,
                    'condition_after' => $conditionBefore,
                    'accessories' => $assetData['accessories'] ?? [],
                    'notes' => $assetData['notes'] ?? null,
                    'location_from' => $asset->location,
                    'location_to' => $application->location,
                    'transaction_date' => now(),
                ]);

                // Update loan item with accessories issued
                $loanItem->update([
                    'accessories_issued' => $assetData['accessories'] ?? [],
                ]);
            }

            // Update application status
            $application->update(['status' => LoanStatus::ISSUED]);

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate($application);

            DB::commit();

            Log::info('Asset issuance processed', [
                'application_number' => $application->application_number,
                'assets_count' => $application->loanItems->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process asset issuance', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
            ]);
            throw $e;
        }
    }

    /**
     * Process asset return (check-in)
     *
     * @param LoanApplication $application
     * @param array $returnData Asset return details with condition assessment
     * @return void
     * @throws \Exception
     */
    public function processAssetReturn(LoanApplication $application, array $returnData): void
    {
        DB::beginTransaction();

        try {
            foreach ($application->loanItems as $loanItem) {
                $asset = $loanItem->asset;
                $assetReturnData = $returnData['assets'][$asset->id];

                $conditionAfter = AssetCondition::from($assetReturnData['condition']);

                // Record condition after return
                $loanItem->update([
                    'condition_after' => $conditionAfter,
                    'accessories_returned' => $assetReturnData['accessories'] ?? [],
                    'damage_report' => $assetReturnData['damage_report'] ?? null,
                ]);

                // Determine new asset status based on condition
                $newStatus = $this->determineAssetStatus($conditionAfter);

                // Update asset
                $asset->update([
                    'status' => $newStatus,
                    'condition' => $conditionAfter,
                    'last_maintenance_date' => $conditionAfter === AssetCondition::DAMAGED ? now() : $asset->last_maintenance_date,
                ]);

                // Create transaction record
                AssetTransaction::create([
                    'asset_id' => $asset->id,
                    'loan_application_id' => $application->id,
                    'type' => 'return',
                    'user_id' => $application->user_id,
                    'processed_by' => auth()->id(),
                    'condition_before' => $loanItem->condition_before,
                    'condition_after' => $conditionAfter,
                    'accessories' => $assetReturnData['accessories'] ?? [],
                    'damage_description' => $assetReturnData['damage_report'] ?? null,
                    'notes' => $assetReturnData['notes'] ?? null,
                    'location_from' => $application->location,
                    'location_to' => $asset->location,
                    'transaction_date' => now(),
                ]);

                // Create helpdesk ticket for damaged assets
                if ($loanItem->hasDamage()) {
                    $this->integrationService->createMaintenanceTicket($asset, $application, $assetReturnData);
                }
            }

            // Update application status
            $application->update(['status' => LoanStatus::RETURNED]);

            // Send notification to applicant
            $this->notificationService->sendLoanStatusUpdate($application);

            DB::commit();

            Log::info('Asset return processed', [
                'application_number' => $application->application_number,
                'assets_count' => $application->loanItems->count(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process asset return', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
            ]);
            throw $e;
        }
    }

    /**
     * Determine asset status based on condition
     *
     * @param AssetCondition $condition
     * @return AssetStatus
     */
    private function determineAssetStatus(AssetCondition $condition): AssetStatus
    {
        return match ($condition) {
            AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR => AssetStatus::AVAILABLE,
            AssetCondition::POOR, AssetCondition::DAMAGED => AssetStatus::MAINTENANCE,
        };
    }

    /**
     * Get transaction history for asset
     *
     * @param int $assetId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAssetTransactionHistory(int $assetId)
    {
        return AssetTransaction::where('asset_id', $assetId)
            ->with(['loanApplication', 'user', 'processedByUser'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    /**
     * Get transaction history for loan application
     *
     * @param int $applicationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoanTransactionHistory(int $applicationId)
    {
        return AssetTransaction::where('loan_application_id', $applicationId)
            ->with(['asset', 'processedByUser'])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }
}
