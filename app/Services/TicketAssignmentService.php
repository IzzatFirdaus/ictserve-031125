<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Ticket Assignment Service
 *
 * Handles manual and automatic ticket assignment with notification workflows.
 *
 * @trace Requirements 2.2, 2.5, 13.1-13.5
 */
class TicketAssignmentService
{
    /**
     * Manually assign ticket to division, user, or external agency
     */
    public function assignTicket(
        HelpdeskTicket $ticket,
        ?int $divisionId = null,
        ?int $userId = null,
        ?string $agency = null
    ): bool {
        try {
            DB::beginTransaction();

            $ticket->update([
                'assigned_to_division' => $divisionId,
                'assigned_to_user' => $userId,
                'assigned_to_agency' => $agency,
                'assigned_at' => now(),
                'status' => $ticket->status === 'open' ? 'assigned' : $ticket->status,
            ]);

            // Send notification to assigned user
            if ($userId) {
                $assignedUser = User::find($userId);
                if ($assignedUser) {
                    $assignedUser->notify(new TicketAssignedNotification($ticket));
                }
            }

            // Log assignment
            Log::info('Ticket assigned', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'division_id' => $divisionId,
                'user_id' => $userId,
                'agency' => $agency,
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ticket assignment failed', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Automatically assign ticket based on category and workload
     */
    public function autoAssignTicket(HelpdeskTicket $ticket): bool
    {
        try {
            // Get category's default division
            $division = $ticket->category?->default_division_id
                ? Division::find($ticket->category->default_division_id)
                : null;

            if (! $division) {
                // Fallback to ticket's division
                $division = $ticket->division;
            }

            if (! $division) {
                Log::warning('Cannot auto-assign ticket - no division found', [
                    'ticket_id' => $ticket->id,
                ]);

                return false;
            }

            // Find user with least active tickets in the division
            $assignedUser = $this->findLeastBusyUser($division->id, $ticket->category_id);

            return $this->assignTicket(
                $ticket,
                $division->id,
                $assignedUser?->id
            );
        } catch (\Exception $e) {
            Log::error('Auto-assignment failed', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Find user with least active tickets in division
     */
    private function findLeastBusyUser(int $divisionId, ?int $categoryId = null): ?User
    {
        return User::query()
            ->where('division_id', $divisionId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('role', 'admin')
                    ->orWhere('role', 'staff');
            })
            ->withCount([
                'assignedHelpdeskTickets' => fn ($query) => $query->whereIn('status', [
                    'assigned',
                    'in_progress',
                    'pending_user',
                ]),
            ])
            ->orderBy('assigned_helpdesk_tickets_count', 'asc')
            ->first();
    }

    /**
     * Reassign ticket to different user/division
     */
    public function reassignTicket(
        HelpdeskTicket $ticket,
        ?int $newDivisionId = null,
        ?int $newUserId = null,
        ?string $reason = null
    ): bool {
        $oldDivisionId = $ticket->assigned_to_division;
        $oldUserId = $ticket->assigned_to_user;

        $result = $this->assignTicket($ticket, $newDivisionId, $newUserId);

        if ($result && $reason) {
            // Add internal comment about reassignment
            $ticket->comments()->create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'commenter_name' => auth()->check() ? auth()->user()?->name : 'System',
                'commenter_email' => auth()->check() ? auth()->user()?->email : 'system@ictserve.local',
                'comment' => "Ticket reassigned: {$reason}",
                'is_internal' => true,
            ]);
        }

        return $result;
    }

    /**
     * Unassign ticket
     */
    public function unassignTicket(HelpdeskTicket $ticket): bool
    {
        return $ticket->update([
            'assigned_to_division' => null,
            'assigned_to_user' => null,
            'assigned_to_agency' => null,
            'assigned_at' => null,
            'status' => 'open',
        ]);
    }
}
