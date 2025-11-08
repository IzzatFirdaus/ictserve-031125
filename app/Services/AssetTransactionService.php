<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Events\AssetReturnedDamaged;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Asset Transaction Service
 *
 * Manages asset check-out, check-in, condition assessment, and damage reporting.
 *
 * @trace Requirements 2.3, 3.2, 3.5, 10.4
 *
 * @see D03-FR-018.3 Asset transaction management
 * @see D04 §2.4 Transaction processing
 */
class AssetTransactionService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Process asset check-out (issue to borrower)
     *
     * @param  array  $data  ['condition_before', 'accessories', 'notes']
     */
    public function checkOutAsset(
        LoanApplication $application,
        Asset $asset,
        User $processedBy,
        array $data
    ): LoanTransaction {
        DB::beginTransaction();

        try {
            // Create transaction record
            $transaction = LoanTransaction::create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'transaction_type' => TransactionType::ISSUE,
                'processed_by' => $processedBy->id,
                'processed_at' => now(),
                'condition_before' => $data['condition_before'] ?? $asset->condition,
                'accessories' => $data['accessories'] ?? $asset->accessories,
                'notes' => $data['notes'] ?? null,
            ]);

            // Update asset status
            $asset->update([
                'status' => AssetStatus::LOANED,
                'condition' => $data['condition_before'] ?? $asset->condition,
            ]);

            // Update loan application status
            $application->update([
                'status' => LoanStatus::IN_USE,
            ]);

            DB::commit();

            Log::info('Asset checked out successfully', [
                'transaction_id' => $transaction->id,
                'application_number' => $application->application_number,
                'asset_tag' => $asset->asset_tag,
                'processed_by' => $processedBy->name,
            ]);

            return $transaction;
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to check out asset', [
                'error' => $exception->getMessage(),
                'application_id' => $application->id,
                'asset_id' => $asset->id,
            ]);

            throw $exception;
        }
    }

    /**
     * Process asset check-in (return from borrower)
     *
     * @param  array  $data  ['condition_after', 'accessories', 'damage_report', 'notes']
     */
    public function checkInAsset(
        LoanApplication $application,
        Asset $asset,
        User $processedBy,
        array $data
    ): LoanTransaction {
        DB::beginTransaction();

        try {
            // Get original condition from issue transaction
            $issueTransaction = LoanTransaction::where('loan_application_id', $application->id)
                ->where('asset_id', $asset->id)
                ->where('transaction_type', TransactionType::ISSUE)
                ->first();

            // Create return transaction
            $transaction = LoanTransaction::create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'transaction_type' => TransactionType::RETURN,
                'processed_by' => $processedBy->id,
                'processed_at' => now(),
                'condition_before' => $issueTransaction?->condition_before ?? $asset->condition,
                'condition_after' => $data['condition_after'],
                'accessories' => $data['accessories'] ?? $asset->accessories,
                'damage_report' => $data['damage_report'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Determine new asset status based on condition
            $newStatus = $this->determineAssetStatus($data['condition_after']);

            // Update asset
            $asset->update([
                'status' => $newStatus,
                'condition' => $data['condition_after'],
            ]);

            // Update loan application status
            $application->update([
                'status' => $newStatus === AssetStatus::MAINTENANCE
                    ? LoanStatus::MAINTENANCE_REQUIRED
                    : LoanStatus::COMPLETED,
            ]);

            // Dispatch event for damaged asset (event-driven architecture)
            if ($transaction->hasDamage()) {
                AssetReturnedDamaged::dispatch($transaction, $asset);
            }

            DB::commit();

            Log::info('Asset checked in successfully', [
                'transaction_id' => $transaction->id,
                'application_number' => $application->application_number,
                'asset_tag' => $asset->asset_tag,
                'condition_after' => $data['condition_after']->value,
                'has_damage' => $transaction->hasDamage(),
            ]);

            return $transaction;
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Failed to check in asset', [
                'error' => $exception->getMessage(),
                'application_id' => $application->id,
                'asset_id' => $asset->id,
            ]);

            throw $exception;
        }
    }

    /**
     * Create maintenance ticket for damaged asset
     */
    protected function createMaintenanceTicket(
        LoanTransaction $transaction,
        Asset $asset,
        LoanApplication $application
    ): HelpdeskTicket {
        $ticket = HelpdeskTicket::create([
            'ticket_number' => 'HD'.date('Y').str_pad((string) (HelpdeskTicket::max('id') + 1), 6, '0', STR_PAD_LEFT),
            'user_id' => $transaction->processedBy->id,
            'asset_id' => $asset->id,
            'category_id' => 1, // Maintenance category
            'priority' => 'high',
            'subject' => "Asset Damage Report - {$asset->asset_tag}",
            'description' => "Asset returned with damage from loan application {$application->application_number}.\n\n".
                "Condition Before: {$transaction->condition_before->value}\n".
                "Condition After: {$transaction->condition_after->value}\n\n".
                "Damage Report:\n{$transaction->damage_report}",
            'status' => 'open',
        ]);

        // Send notification
        $this->notificationService->sendMaintenanceNotification($ticket, $asset, $application);

        Log::info('Maintenance ticket created for damaged asset', [
            'ticket_number' => $ticket->ticket_number,
            'asset_tag' => $asset->asset_tag,
            'application_number' => $application->application_number,
        ]);

        return $ticket;
    }

    /**
     * Determine asset status based on condition
     */
    protected function determineAssetStatus(AssetCondition $condition): AssetStatus
    {
        return match ($condition) {
            AssetCondition::DAMAGED, AssetCondition::POOR => AssetStatus::MAINTENANCE,
            default => AssetStatus::AVAILABLE,
        };
    }

    /**
     * Get transaction history for an asset
     */
    public function getAssetTransactionHistory(Asset $asset): \Illuminate\Support\Collection
    {
        return LoanTransaction::where('asset_id', $asset->id)
            ->with(['loanApplication', 'processedBy'])
            ->orderBy('processed_at', 'desc')
            ->get();
    }

    /**
     * Get transaction history for a loan application
     */
    public function getLoanTransactionHistory(LoanApplication $application): \Illuminate\Support\Collection
    {
        return LoanTransaction::where('loan_application_id', $application->id)
            ->with(['asset', 'processedBy'])
            ->orderBy('processed_at', 'asc')
            ->get();
    }

    /**
     * Track overdue assets and send reminders
     */
    public function trackOverdueAssets(): void
    {
        $overdueApplications = LoanApplication::where('status', LoanStatus::IN_USE)
            ->where('loan_end_date', '<', now())
            ->get();

        foreach ($overdueApplications as $application) {
            // Update status to overdue
            $application->update(['status' => LoanStatus::OVERDUE]);

            // Send overdue notification
            $this->notificationService->sendOverdueNotification($application);

            Log::info('Overdue notification sent', [
                'application_number' => $application->application_number,
                'days_overdue' => now()->diffInDays($application->loan_end_date),
            ]);
        }
    }

    /**
     * Send return reminders (48 hours before due date)
     */
    public function sendReturnReminders(): void
    {
        $upcomingReturns = LoanApplication::where('status', LoanStatus::IN_USE)
            ->whereBetween('loan_end_date', [now(), now()->addHours(48)])
            ->get();

        foreach ($upcomingReturns as $application) {
            $this->notificationService->sendReturnReminder($application);

            Log::info('Return reminder sent', [
                'application_number' => $application->application_number,
                'due_date' => $application->loan_end_date->format('Y-m-d'),
            ]);
        }
    }
}
