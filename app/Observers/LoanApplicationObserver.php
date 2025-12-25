<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\StatusUpdated;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;

/**
 * Loan Application Observer
 *
 * Handles loan application lifecycle events and triggers real-time broadcasting.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.1, 2.2
 */
class LoanApplicationObserver
{
    /**
     * Handle the LoanApplication "updated" event.
     */
    public function updated(LoanApplication $loanApplication): void
    {
        // Check if status changed
        if ($loanApplication->isDirty('status')) {
            $oldStatus = $loanApplication->getOriginal('status');
            $newStatus = $loanApplication->status;

            // Skip notification if old status is null (initial creation)
            if ($oldStatus === null) {
                return;
            }

            // Convert enum values to strings for the event
            $oldStatusString = is_string($oldStatus) ? $oldStatus : $oldStatus->value;
            $newStatusString = is_string($newStatus) ? $newStatus : $newStatus->value;

            // Broadcast real-time status change event
            StatusUpdated::dispatch($loanApplication, $oldStatusString, $newStatusString);

            // PKS 5.2.1: All loan applications must have user_id (NOT NULL)
            Log::info('Loan application status updated', [
                'loan_application_id' => $loanApplication->id,
                'application_number' => $loanApplication->application_number,
                'old_status' => $oldStatusString,
                'new_status' => $newStatusString,
                'user_id' => $loanApplication->user_id,
            ]);
        }
    }
}
