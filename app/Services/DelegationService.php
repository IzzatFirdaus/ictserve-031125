<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApprovalDelegation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DelegationService
 *
 * Manages approval delegation workflows for Grade 41+ approvers.
 * Handles creation, validation, and management of temporary approval delegations.
 */
class DelegationService
{
    /**
     * Create a new delegation
     *
     * @throws \InvalidArgumentException
     */
    public function createDelegation(
        int $originalApproverId,
        int $delegatedApproverId,
        Carbon $startDate,
        Carbon $endDate,
        string $reason,
        int $createdBy
    ): ApprovalDelegation {
        // Validate dates
        if ($startDate->gte($endDate)) {
            throw new \InvalidArgumentException(__('delegation.error.start_before_end'));
        }

        if ($startDate->lt(now()->startOfDay())) {
            throw new \InvalidArgumentException(__('delegation.error.start_not_past'));
        }

        // Validate users exist
        $originalApprover = User::find($originalApproverId);
        $delegatedApprover = User::find($delegatedApproverId);

        if (! $originalApprover) {
            throw new \InvalidArgumentException(__('delegation.error.original_not_found'));
        }

        if (! $delegatedApprover) {
            throw new \InvalidArgumentException(__('delegation.error.delegated_not_found'));
        }

        // Validate both users have approver role (Grade 41+)
        if (! $this->isApprover($originalApprover)) {
            throw new \InvalidArgumentException(__('delegation.error.original_not_approver'));
        }

        if (! $this->isApprover($delegatedApprover)) {
            throw new \InvalidArgumentException(__('delegation.error.delegated_not_approver'));
        }

        if ($originalApproverId === $delegatedApproverId) {
            throw new \InvalidArgumentException(__('delegation.error.same_user'));
        }

        return DB::transaction(function () use (
            $originalApproverId,
            $delegatedApproverId,
            $startDate,
            $endDate,
            $reason,
            $createdBy
        ) {
            $delegation = new ApprovalDelegation([
                'original_approver_id' => $originalApproverId,
                'delegated_approver_id' => $delegatedApproverId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
                'created_by' => $createdBy,
            ]);

            if ($delegation->hasOverlap()) {
                throw new \InvalidArgumentException(__('delegation.error.overlap'));
            }

            $delegation->save();

            Log::info('Delegation created', [
                'delegation_id' => $delegation->id,
                'original_approver_id' => $originalApproverId,
                'delegated_approver_id' => $delegatedApproverId,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'created_by' => $createdBy,
            ]);

            return $delegation;
        });
    }

    /**
     * Check if user is an approver (Grade 41+)
     */
    private function isApprover(User $user): bool
    {
        // Check if user has approver role or grade >= 41
        if (method_exists($user, 'hasRole') && $user->hasRole('approver')) {
            return true;
        }

        // Fallback to grade check
        $grade = $user->grade ?? $user->gred ?? null;
        if ($grade !== null) {
            $gradeNumber = (int) preg_replace('/[^0-9]/', '', (string) $grade);

            return $gradeNumber >= 41;
        }

        return false;
    }

    /**
     * Get the effective approver for a given original approver
     * Returns the delegated approver if an active delegation exists
     */
    public function getEffectiveApprover(int $originalApproverId): ?User
    {
        $delegation = ApprovalDelegation::getActiveDelegationFor($originalApproverId);

        if ($delegation) {
            Log::info('Using delegated approver', [
                'original_approver_id' => $originalApproverId,
                'delegated_approver_id' => $delegation->delegated_approver_id,
                'delegation_id' => $delegation->id,
            ]);

            return $delegation->delegatedApprover;
        }

        return User::find($originalApproverId);
    }

    /**
     * Check if a user can approve on behalf of another user
     */
    public function canApproveOnBehalf(int $delegatedUserId, int $originalUserId): bool
    {
        return ApprovalDelegation::where('original_approver_id', $originalUserId)
            ->where('delegated_approver_id', $delegatedUserId)
            ->active()
            ->exists();
    }

    /**
     * Get all users that a delegated approver can approve on behalf of
     */
    public function getDelegationsToUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return ApprovalDelegation::getActiveDelegationsToUser($userId);
    }

    /**
     * Get all delegations created by a user (as original approver)
     */
    public function getDelegationsByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return ApprovalDelegation::where('original_approver_id', $userId)
            ->with(['delegatedApprover', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Deactivate a delegation
     */
    public function deactivateDelegation(ApprovalDelegation $delegation): bool
    {
        $result = $delegation->deactivate();

        Log::info('Delegation deactivated', [
            'delegation_id' => $delegation->id,
            'original_approver_id' => $delegation->original_approver_id,
            'delegated_approver_id' => $delegation->delegated_approver_id,
        ]);

        return $result;
    }

    /**
     * Get available approvers for delegation (excluding self)
     */
    public function getAvailableApprovers(int $excludeUserId): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('id', '!=', $excludeUserId)
            ->where(function ($query) {
                // Users with approver role
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'approver');
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
