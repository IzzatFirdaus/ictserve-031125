<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid Helpdesk Service
 *
 * Handles guest ticket claiming and hybrid architecture operations.
 *
 * @trace Requirements 1.1, 1.2, 22.2, 22.6
 */
class HybridHelpdeskService
{
    /**
     * Create a guest ticket submission
     */
    public function createGuestTicket(array $data): HelpdeskTicket
    {
        try {
            // Create the ticket with a temporary ticket number
            $ticket = HelpdeskTicket::create([
                'ticket_number' => 'TEMP-'.uniqid(), // Temporary, will be replaced
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'guest_staff_id' => $data['guest_staff_id'] ?? null,
                'guest_grade' => $data['guest_grade'] ?? null,
                'guest_division' => $data['guest_division'] ?? null,
                'category_id' => $data['category_id'],
                'priority' => $data['priority'] ?? 'normal',
                'subject' => $data['title'] ?? $data['subject'] ?? '',
                'description' => $data['description'],
                'damage_type' => $data['damage_type'] ?? null,
                'asset_id' => $data['asset_id'] ?? null,
                'status' => 'open',
                'user_id' => null, // Guest submission
            ]);

            // Generate proper ticket number based on ID
            $ticket->ticket_number = HelpdeskTicket::generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates if category has SLA settings
            if ($ticket->category) {
                $ticket->calculateSLADueDates();
            }

            Log::info('Guest ticket created', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'guest_email' => $ticket->guest_email,
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Guest ticket creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Claim a guest ticket to authenticated user account
     */
    public function claimGuestTicket(HelpdeskTicket $ticket, User $user): bool
    {
        // Verify ticket can be claimed
        if (! $ticket->canBeClaimedBy($user)) {
            Log::warning('Ticket claim denied - email mismatch', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'ticket_email' => $ticket->guest_email,
                'user_email' => $user->email,
            ]);

            return false;
        }

        try {
            DB::beginTransaction();

            // Transfer guest data to authenticated user
            $ticket->update([
                'user_id' => $user->id,
                // Keep guest fields for audit trail
            ]);

            // Add internal comment about claim
            $ticket->comments()->create([
                'user_id' => $user->id,
                'commenter_name' => $user->name,
                'commenter_email' => $user->email,
                'comment' => 'Ticket claimed by authenticated user.',
                'is_internal' => true,
            ]);

            DB::commit();

            Log::info('Guest ticket claimed', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'user_id' => $user->id,
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ticket claim failed', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get all claimable tickets for a user
     */
    public function getClaimableTickets(User $user)
    {
        return HelpdeskTicket::query()
            ->whereNull('user_id')
            ->where('guest_email', $user->email)
            ->with(['category', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user can access ticket (either as owner or by email match)
     */
    public function canUserAccessTicket(HelpdeskTicket $ticket, User $user): bool
    {
        // Direct ownership
        if ($ticket->user_id === $user->id) {
            return true;
        }

        // Email match for guest submissions
        if ($ticket->isGuestSubmission() && $ticket->guest_email === $user->email) {
            return true;
        }

        return false;
    }

    /**
     * Get all tickets accessible by user (owned + guest with matching email)
     */
    public function getUserAccessibleTickets(User $user)
    {
        return HelpdeskTicket::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($subQuery) use ($user) {
                        $subQuery->whereNull('user_id')
                            ->where('guest_email', $user->email);
                    });
            })
            ->with(['category', 'assignedUser', 'assignedDivision'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Create an authenticated ticket submission
     *
     * @trace Requirements 1.1, 1.2, 1.3, 4.2
     */
    public function createAuthenticatedTicket(array $data, User $user): HelpdeskTicket
    {
        try {
            // Create the ticket with a temporary ticket number
            $ticket = HelpdeskTicket::create([
                'ticket_number' => 'TEMP-'.uniqid(), // Temporary, will be replaced
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'priority' => $data['priority'] ?? 'normal',
                'subject' => $data['title'] ?? $data['subject'] ?? '',
                'description' => $data['description'],
                'damage_type' => $data['damage_type'] ?? null,
                'asset_id' => $data['asset_id'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'status' => 'open',
                // Guest fields are null for authenticated submissions
                'guest_name' => null,
                'guest_email' => null,
                'guest_phone' => null,
                'guest_staff_id' => null,
                'guest_grade' => null,
                'guest_division' => null,
            ]);

            // Generate proper ticket number based on ID
            $ticket->ticket_number = HelpdeskTicket::generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates if category has SLA settings
            if ($ticket->category) {
                $ticket->calculateSLADueDates();
            }

            Log::info('Authenticated ticket created', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);

            return $ticket;
        } catch (\Exception $e) {
            Log::error('Authenticated ticket creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $data,
            ]);

            throw $e;
        }
    }
}
