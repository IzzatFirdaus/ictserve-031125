<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Log;

/**
 * PKS 5.2.1 Compliant Helpdesk Service
 *
 * SSO-only architecture - all ticket submissions require authenticated users.
 * Guest submission functionality has been removed per PKS 5.2.1 Accountability requirements.
 *
 * @trace Requirements 1.1, 1.2, 1.3, 4.2, 25.1
 *
 * @see D04 Software Design Document - PKS-Compliant SSO-Only Architecture
 */
class HybridHelpdeskService
{
    /**
     * Create an authenticated ticket submission
     *
     * PKS 5.2.1: All tickets MUST have a mandatory user_id (NOT NULL).
     *
     * @param  array<string, mixed>  $data  Ticket data
     * @param  User  $user  Authenticated user (REQUIRED)
     *
     * @trace Requirements 1.1, 1.2, 1.3, 4.2, 25.1
     */
    public function createTicket(array $data, User $user): HelpdeskTicket
    {
        try {
            // Create the ticket with a temporary ticket number
            $ticket = HelpdeskTicket::create([
                'ticket_number' => 'TEMP-'.\uniqid(), // Temporary, will be replaced
                'user_id' => $user->id, // MANDATORY per PKS 5.2.1
                'division_id' => $data['division_id'] ?? null,
                'job_grade' => $data['job_grade'] ?? null,
                'staff_id' => $data['staff_id'] ?? $user->staff_id ?? null,
                'declaration_accepted' => $data['declaration_accepted'] ?? false,
                'category_id' => $data['category_id'],
                'priority' => $data['priority'] ?? 'normal',
                'subject' => $data['title'] ?? $data['subject'] ?? '',
                'description' => $data['description'],
                'damage_type' => $data['damage_type'] ?? null,
                'asset_id' => $data['asset_id'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'status' => 'open',
                'source' => $data['source'] ?? 'web',
            ]);

            // Generate proper ticket number based on ID
            $ticket->ticket_number = HelpdeskTicket::generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates if category has SLA settings
            if ($ticket->category) {
                $ticket->calculateSLADueDates();
            }

            Log::info('Ticket created (PKS 5.2.1 compliant)', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Ticket creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Check if user can access ticket
     *
     * PKS 5.2.1: Access is based on user_id ownership only.
     */
    public function canUserAccessTicket(HelpdeskTicket $ticket, User $user): bool
    {
        // Direct ownership check
        return $ticket->user_id === $user->id;
    }

    /**
     * Get all tickets owned by user
     *
     * PKS 5.2.1: Returns only tickets where user_id matches.
     */
    public function getUserTickets(User $user): EloquentBuilder
    {
        return HelpdeskTicket::query()
            ->where('user_id', $user->id)
            ->with(['category', 'assignedUser', 'assignedDivision'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get ticket statistics for user
     *
     * @return array<string, int>
     */
    public function getUserTicketStats(User $user): array
    {
        $baseQuery = HelpdeskTicket::query()->where('user_id', $user->id);

        return [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];
    }

    /**
     * Update ticket status
     *
     * @param  array<string, mixed>  $data  Update data
     */
    public function updateTicket(HelpdeskTicket $ticket, array $data, User $user): HelpdeskTicket
    {
        // Verify user has access
        if (! $this->canUserAccessTicket($ticket, $user)) {
            throw new \RuntimeException('Unauthorized access to ticket');
        }

        $ticket->update($data);

        Log::info('Ticket updated', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $user->id,
            'updated_fields' => \array_keys($data),
        ]);

        return $ticket->fresh() ?? $ticket;
    }
}
