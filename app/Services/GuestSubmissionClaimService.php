<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\SubmissionClaimedMail;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\PortalActivity;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Guest Submission Claim Service
 *
 * Handles claiming of guest submissions by authenticated users.
 * Verifies email ownership and links submissions to user accounts.
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Guest Submission Claim Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirement 2.5
 */
class GuestSubmissionClaimService
{
    /**
     * Find claimable submissions for user
     */
    public function findClaimableSubmissions(User $user): Collection
    {
        $tickets = HelpdeskTicket::where('guest_email', $user->email)
            ->whereNull('user_id')
            ->get()
            ->map(fn ($ticket) => [
                'id' => $ticket->id,
                'type' => 'ticket',
                'number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
                'model' => $ticket,
            ]);

        $loans = LoanApplication::with(['loanItems.asset:id,name'])
            ->where('applicant_email', $user->email)
            ->whereNull('user_id')
            ->get()
            ->map(fn ($loan) => [
                'id' => $loan->id,
                'type' => 'loan',
                'number' => $loan->application_number,
                'subject' => $loan->loanItems->pluck('asset.name')->join(', ') ?: 'Asset Loan',
                'status' => $loan->status,
                'created_at' => $loan->created_at,
                'model' => $loan,
            ]);

        return $tickets->concat($loans)->sortByDesc('created_at');
    }

    /**
     * Claim a submission for user
     *
     * @param  mixed  $submission  HelpdeskTicket|LoanApplication
     *
     * @throws \Exception
     */
    public function claimSubmission(User $user, mixed $submission): bool
    {
        if (! $this->verifyOwnership($user, $submission)) {
            throw new \Exception('Email mismatch - cannot claim submission');
        }

        DB::transaction(function () use ($user, $submission) {
            // Update submission with user_id
            $submission->update(['user_id' => $user->id]);

            // Log activity
            PortalActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'submission_claimed',
                'subject_type' => get_class($submission),
                'subject_id' => $submission->id,
                'metadata' => [
                    'submission_type' => $submission instanceof HelpdeskTicket ? 'ticket' : 'loan',
                    'submission_number' => $submission instanceof HelpdeskTicket
                        ? $submission->ticket_number
                        : $submission->application_number,
                    'claimed_at' => now()->toISOString(),
                ],
            ]);

            // Send confirmation email
            $this->sendClaimConfirmation($user, $submission);
        });

        return true;
    }

    /**
     * Verify user owns the submission via email
     */
    protected function verifyOwnership(User $user, mixed $submission): bool
    {
        $email = $submission instanceof HelpdeskTicket
            ? $submission->guest_email
            : $submission->applicant_email;

        return $email === $user->email;
    }

    /**
     * Send claim confirmation email
     */
    protected function sendClaimConfirmation(User $user, mixed $submission): void
    {
        Mail::to($user->email)->send(
            new SubmissionClaimedMail($user, $submission)
        );
    }

    /**
     * Get count of claimable submissions
     */
    public function getClaimableCount(User $user): int
    {
        $ticketCount = HelpdeskTicket::where('guest_email', $user->email)
            ->whereNull('user_id')
            ->count();

        $loanCount = LoanApplication::where('applicant_email', $user->email)
            ->whereNull('user_id')
            ->count();

        return $ticketCount + $loanCount;
    }

    /**
     * Claim all submissions for user
     */
    public function claimAllSubmissions(User $user): array
    {
        $ticketsClaimed = HelpdeskTicket::where('guest_email', $user->email)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'claimed_at' => now(),
            ]);

        $loansClaimed = LoanApplication::where('applicant_email', $user->email)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'claimed_at' => now(),
            ]);

        $totalClaimed = $ticketsClaimed + $loansClaimed;

        if ($totalClaimed > 0) {
            PortalActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'bulk_claim',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'description' => "Claimed {$totalClaimed} submissions ({$ticketsClaimed} tickets, {$loansClaimed} loans)",
                'metadata' => [
                    'tickets' => $ticketsClaimed,
                    'loans' => $loansClaimed,
                ],
            ]);
        }

        return [
            'tickets' => $ticketsClaimed,
            'loans' => $loansClaimed,
            'total' => $totalClaimed,
        ];
    }
}
