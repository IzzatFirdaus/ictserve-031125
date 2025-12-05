<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AccountLinkingServiceInterface;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Account Linking Service Implementation for ICTServe v3.5.0
 *
 * Implements the optional account linking feature that allows newly registered
 * staff to link their historical guest submissions to their new account.
 *
 * This service:
 * - Finds unlinked helpdesk tickets and loan applications by email
 * - Links submissions atomically within a database transaction
 * - Maintains audit trail for compliance (owen-it + spatie)
 * - Updates user's guest_submissions_linked counter
 *
 * Security Considerations:
 * - Email matching is case-insensitive
 * - Only submissions with NULL user_id can be linked
 * - All operations are logged for audit compliance
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D02 FR-050 Optional account linking
 * @see D03 SRS-DATA-001 Hybrid data association
 * @see D09 §4.6 Audit trail requirements
 * @see Requirements 18.2, 18.4
 */
class AccountLinkingService implements AccountLinkingServiceInterface
{
    /**
     * Submission type constants for internal use
     */
    private const TYPE_TICKET = 'ticket';

    private const TYPE_LOAN = 'loan';

    /**
     * Find all unlinked submissions for a given email address
     *
     * Searches both helpdesk_tickets (guest_email) and loan_applications
     * (applicant_email) for submissions where user_id is NULL.
     *
     * @param  string  $email  The email address to search for
     * @return Collection<int, array{type: string, id: int, reference: string, created_at: string, subject: string|null, status: string}>
     */
    public function findUnlinkedSubmissions(string $email): Collection
    {
        $normalizedEmail = $this->normalizeEmail($email);

        if (empty($normalizedEmail)) {
            return collect();
        }

        $tickets = $this->findUnlinkedTickets($normalizedEmail);
        $loans = $this->findUnlinkedLoans($normalizedEmail);

        // Merge and sort by created_at descending while preserving array payloads
        return collect($tickets->all())
            ->merge($loans->all())
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Link confirmed submissions to a user account
     *
     * Uses database transaction to ensure atomicity. If any update fails,
     * all changes are rolled back.
     *
     * @param  User  $user  The user to link submissions to
     * @param  array<int, array{type: string, id: int}>  $submissionIds  Submissions to link
     * @return int Number of successfully linked submissions
     *
     * @throws \Illuminate\Database\QueryException If transaction fails
     */
    public function linkSubmissions(User $user, array $submissionIds): int
    {
        if (empty($submissionIds)) {
            return 0;
        }

        $linkedCount = 0;

        DB::transaction(function () use ($user, $submissionIds, &$linkedCount): void {
            foreach ($submissionIds as $submission) {
                $type = $submission['type'] ?? null;
                $id = $submission['id'] ?? null;

                if (! $type || ! $id) {
                    continue;
                }

                $linked = match ($type) {
                    self::TYPE_TICKET => $this->linkTicket($user, (int) $id),
                    self::TYPE_LOAN => $this->linkLoan($user, (int) $id),
                    default => false,
                };

                if ($linked) {
                    $linkedCount++;
                }
            }

            // Update user's linked submission counter
            if ($linkedCount > 0) {
                $user->increment('guest_submissions_linked', $linkedCount);

                $this->logLinkingActivity($user, $linkedCount, $submissionIds);
            }
        });

        Log::info('Account linking completed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'linked_count' => $linkedCount,
            'requested_count' => count($submissionIds),
        ]);

        return $linkedCount;
    }

    /**
     * Get the count of submissions linked to a user's account
     *
     * @param  User  $user  The user to check
     * @return int Total linked submission count
     */
    public function getLinkedSubmissionCount(User $user): int
    {
        return (int) ($user->guest_submissions_linked ?? 0);
    }

    /**
     * Check if a user has any unlinked submissions available
     *
     * @param  User  $user  The user to check
     * @return bool True if unlinked submissions exist
     */
    public function hasUnlinkedSubmissions(User $user): bool
    {
        $email = $this->normalizeEmail($user->email);

        if (empty($email)) {
            return false;
        }

        // Check tickets first (faster query)
        $hasTickets = HelpdeskTicket::whereNull('user_id')
            ->whereRaw('LOWER(guest_email) = ?', [$email])
            ->exists();

        if ($hasTickets) {
            return true;
        }

        // Check loans
        return LoanApplication::whereNull('user_id')
            ->whereRaw('LOWER(applicant_email) = ?', [$email])
            ->exists();
    }

    /**
     * Get summary statistics for account linking
     *
     * @param  User  $user  The user to get statistics for
     * @return array{linked_tickets: int, linked_loans: int, unlinked_tickets: int, unlinked_loans: int}
     */
    public function getLinkingStatistics(User $user): array
    {
        $email = $this->normalizeEmail($user->email);

        // Count linked submissions (user_id = user's id)
        $linkedTickets = HelpdeskTicket::where('user_id', $user->id)->count();
        $linkedLoans = LoanApplication::where('user_id', $user->id)->count();

        // Count unlinked submissions (user_id is NULL, email matches)
        $unlinkedTickets = 0;
        $unlinkedLoans = 0;

        if (! empty($email)) {
            $unlinkedTickets = HelpdeskTicket::whereNull('user_id')
                ->whereRaw('LOWER(guest_email) = ?', [$email])
                ->count();

            $unlinkedLoans = LoanApplication::whereNull('user_id')
                ->whereRaw('LOWER(applicant_email) = ?', [$email])
                ->count();
        }

        return [
            'linked_tickets' => $linkedTickets,
            'linked_loans' => $linkedLoans,
            'unlinked_tickets' => $unlinkedTickets,
            'unlinked_loans' => $unlinkedLoans,
        ];
    }

    /**
     * Find unlinked helpdesk tickets by email
     *
     * @param  string  $email  Normalized email address
     * @return Collection<int, array{type: string, id: int, reference: string, created_at: string, subject: string|null, status: string}>
     */
    private function findUnlinkedTickets(string $email): Collection
    {
        return HelpdeskTicket::whereNull('user_id')
            ->whereRaw('LOWER(guest_email) = ?', [$email])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (HelpdeskTicket $ticket): array => [
                'type' => self::TYPE_TICKET,
                'id' => $ticket->id,
                'reference' => $ticket->ticket_number ?? "HD-{$ticket->id}",
                'created_at' => $ticket->created_at?->toIso8601String() ?? '',
                'subject' => $ticket->subject,
                'status' => $ticket->status ?? 'unknown',
            ]);
    }

    /**
     * Find unlinked loan applications by email
     *
     * @param  string  $email  Normalized email address
     * @return Collection<int, array{type: string, id: int, reference: string, created_at: string, subject: string|null, status: string}>
     */
    private function findUnlinkedLoans(string $email): Collection
    {
        return LoanApplication::whereNull('user_id')
            ->whereRaw('LOWER(applicant_email) = ?', [$email])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (LoanApplication $loan): array => [
                'type' => self::TYPE_LOAN,
                'id' => $loan->id,
                'reference' => $loan->application_number ?? "LA-{$loan->id}",
                'created_at' => $loan->created_at?->toIso8601String() ?? '',
                'subject' => $loan->purpose,
                'status' => $loan->status?->value ?? $loan->status ?? 'unknown',
            ]);
    }

    /**
     * Link a single helpdesk ticket to a user
     *
     * @param  User  $user  The user to link to
     * @param  int  $ticketId  The ticket ID to link
     * @return bool True if successfully linked
     */
    private function linkTicket(User $user, int $ticketId): bool
    {
        $ticket = HelpdeskTicket::whereNull('user_id')
            ->whereRaw('LOWER(guest_email) = ?', [$this->normalizeEmail($user->email)])
            ->find($ticketId);

        if (! $ticket) {
            Log::warning('Attempted to link non-existent or already linked ticket', [
                'user_id' => $user->id,
                'ticket_id' => $ticketId,
            ]);

            return false;
        }

        $ticket->user_id = $user->id;
        $ticket->save();

        Log::info('Helpdesk ticket linked to user account', [
            'user_id' => $user->id,
            'ticket_id' => $ticketId,
            'ticket_number' => $ticket->ticket_number,
        ]);

        return true;
    }

    /**
     * Link a single loan application to a user
     *
     * @param  User  $user  The user to link to
     * @param  int  $loanId  The loan application ID to link
     * @return bool True if successfully linked
     */
    private function linkLoan(User $user, int $loanId): bool
    {
        $loan = LoanApplication::whereNull('user_id')
            ->whereRaw('LOWER(applicant_email) = ?', [$this->normalizeEmail($user->email)])
            ->find($loanId);

        if (! $loan) {
            Log::warning('Attempted to link non-existent or already linked loan', [
                'user_id' => $user->id,
                'loan_id' => $loanId,
            ]);

            return false;
        }

        $loan->user_id = $user->id;
        $loan->save();

        Log::info('Loan application linked to user account', [
            'user_id' => $user->id,
            'loan_id' => $loanId,
            'application_number' => $loan->application_number,
        ]);

        return true;
    }

    /**
     * Normalize email address for comparison
     *
     * @param  string  $email  The email to normalize
     * @return string Lowercase trimmed email
     */
    private function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    /**
     * Log account linking activity for audit compliance
     *
     * @param  User  $user  The user who performed linking
     * @param  int  $linkedCount  Number of submissions linked
     * @param  array<int, array{type: string, id: int}>  $submissions  The submissions that were linked
     */
    private function logLinkingActivity(User $user, int $linkedCount, array $submissions): void
    {
        activity('account_linking')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'linked_count' => $linkedCount,
                'submissions' => $submissions,
            ])
            ->log('User linked guest submissions to account');

        Log::channel('single')->info('Account linking activity', [
            'action' => 'guest_submissions_linked',
            'user_id' => $user->id,
            'email' => $user->email,
            'linked_count' => $linkedCount,
            'submissions' => $submissions,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
