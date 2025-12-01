<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Account Linking Service Interface for ICTServe v3.5.0
 *
 * Provides functionality to link historical guest submissions (helpdesk tickets
 * and loan applications) to newly registered staff accounts. This enables staff
 * to view their complete submission history after self-registration.
 *
 * Key Features:
 * - Find unlinked submissions by email (guest submissions where user_id is NULL)
 * - Link submissions atomically with database transaction
 * - Track linked submission count for user profile
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D02 FR-050 Optional account linking
 * @see D03 SRS-DATA-001 Hybrid data association
 * @see D09 §4.6 Audit trail requirements
 * @see Requirements 18.2, 18.4
 */
interface AccountLinkingServiceInterface
{
    /**
     * Find all unlinked submissions for a given email address
     *
     * Searches for helpdesk tickets and loan applications where:
     * - guest_email (tickets) or applicant_email (loans) matches the provided email
     * - user_id is NULL (indicating guest submission)
     *
     * Returns a collection containing both ticket and loan submissions
     * with metadata for display in the linking confirmation UI.
     *
     * @param  string  $email  The email address to search for (case-insensitive)
     * @return Collection<int, array{type: string, id: int, reference: string, created_at: string, subject: string|null, status: string}>
     *
     * @see Requirements 18.2 - Search for matching submissions
     */
    public function findUnlinkedSubmissions(string $email): Collection;

    /**
     * Link confirmed submissions to a user account
     *
     * Updates the user_id field on all specified submissions within
     * an atomic database transaction. Also:
     * - Logs the linking action in audit trail (owen-it + spatie)
     * - Updates the user's guest_submissions_linked counter
     *
     * @param  User  $user  The authenticated user to link submissions to
     * @param  array<int, array{type: string, id: int}>  $submissionIds  Array of submissions to link
     * @return int Number of submissions successfully linked
     *
     * @throws \Illuminate\Database\QueryException If database transaction fails
     *
     * @see Requirements 18.4 - Atomic transaction for linking
     */
    public function linkSubmissions(User $user, array $submissionIds): int;

    /**
     * Get the count of submissions linked to a user's account
     *
     * Returns the total number of previously guest submissions
     * that have been linked to this user account.
     *
     * @param  User  $user  The user to check
     * @return int Total count of linked submissions
     */
    public function getLinkedSubmissionCount(User $user): int;

    /**
     * Check if a user has any unlinked submissions available
     *
     * Quick check to determine if the account linking feature
     * should be prominently displayed to the user.
     *
     * @param  User  $user  The user to check
     * @return bool True if unlinked submissions exist for user's email
     */
    public function hasUnlinkedSubmissions(User $user): bool;

    /**
     * Get summary statistics for account linking
     *
     * Returns counts of linked and unlinked submissions for display
     * in the user's dashboard or profile.
     *
     * @param  User  $user  The user to get statistics for
     * @return array{linked_tickets: int, linked_loans: int, unlinked_tickets: int, unlinked_loans: int}
     */
    public function getLinkingStatistics(User $user): array;
}
