<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Loan Reminder Service (Wrapper)
 *
 * Minimal wrapper to provide a plan-matching file name and API while delegating
 * reminder logic to `AssetTransactionService` for backward compatibility.
 * trace: SRS-FR-014; D04 §3.6; author: ictserve-team
 */
class LoanReminderService
{
    public function __construct(private AssetTransactionService $assetTransactionService) {}

    /**
     * Send return reminders and delegate to the AssetTransactionService
     */
    public function sendReturnReminders(): void
    {
        $this->assetTransactionService->sendReturnReminders();
    }

    /**
     * Track overdue assets and delegate to the AssetTransactionService
     */
    public function trackOverdueAssets(): void
    {
        $this->assetTransactionService->trackOverdueAssets();
    }
}
