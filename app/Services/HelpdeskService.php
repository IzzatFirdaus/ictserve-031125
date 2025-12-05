<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\HelpdeskServiceInterface;
use App\Events\TicketAssigned;
use App\Events\TicketStatusChanged;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Notifications\HelpdeskTicketStatusUpdated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Helpdesk Service Implementation
 *
 * Implements helpdesk ticket management with hybrid architecture support.
 * Handles both guest submissions (user_id = NULL) and authenticated submissions.
 *
 * Features:
 * - Hybrid user_id logic (Auth::check() determines user association)
 * - Status token generation for guest status checking
 * - SLA calculation based on category and priority
 * - Ticket assignment with real-time notifications
 * - Audit trail logging via dual audit system
 *
 * @see D03 SRS-HELP-001, SRS-HELP-002, SRS-HELP-003
 * @see Requirements 1.5, 2.1, 5.3, 5.4
 */
class HelpdeskService implements HelpdeskServiceInterface
{
    /**
     * Default SLA hours if category not configured
     */
    private const DEFAULT_RESPONSE_HOURS = 4;

    private const DEFAULT_RESOLUTION_HOURS = 24;

    public function __construct(
        private TokenService $tokenService,
        private SLAManagementService $slaService
    ) {}

    /**
     * Create a new helpdesk ticket with hybrid user_id logic
     *
     * Implements True Hybrid Architecture:
     * - If Auth::check() === true: Links to user_id, auto-fills from profile
     * - If Auth::check() === false: Sets user_id = NULL, uses guest fields
     *
     * @param  array  $data  Ticket data including:
     *                       - category_id (required)
     *                       - subject (required)
     *                       - description (required)
     *                       - priority (optional, default: 'normal')
     *                       - guest_name, guest_email, guest_phone (for guest submissions)
     *                       - division_id, job_grade (for authenticated submissions)
     * @return HelpdeskTicket The created ticket with status token
     *
     * @throws \Exception If ticket creation fails
     */
    public function createTicket(array $data): HelpdeskTicket
    {
        try {
            DB::beginTransaction();

            // Determine if user is authenticated
            $user = Auth::user();
            $isAuthenticated = $user !== null;

            // Prepare ticket data with hybrid logic
            $ticketData = [
                'ticket_number' => 'TEMP-'.uniqid(), // Temporary, will be replaced
                'user_id' => $isAuthenticated ? $user->id : null,
                'form_reference_code' => 'PK.(S).MOTAC.07.(L1)', // Official form code per Req 24
                'category_id' => $data['category_id'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'normal',
                'status' => 'open',
                'declaration_accepted' => $data['declaration_accepted'] ?? false,
            ];

            // Add authenticated user fields
            if ($isAuthenticated) {
                $ticketData['division_id'] = $data['division_id'] ?? $user->division_id;
                $ticketData['job_grade'] = $data['job_grade'] ?? $user->grade;
                // Guest fields are NULL for authenticated submissions
                $ticketData['guest_name'] = null;
                $ticketData['guest_email'] = null;
                $ticketData['guest_phone'] = null;
            } else {
                // Add guest submission fields
                $ticketData['guest_name'] = $data['guest_name'];
                $ticketData['guest_email'] = $data['guest_email'];
                $ticketData['guest_phone'] = $data['guest_phone'] ?? null;
                $ticketData['guest_staff_id'] = $data['guest_staff_id'] ?? null;
                $ticketData['guest_grade'] = $data['guest_grade'] ?? null;
                $ticketData['guest_division'] = $data['guest_division'] ?? null;
                $ticketData['division_id'] = $data['division_id'] ?? null;
            }

            // Optional fields
            if (isset($data['asset_id'])) {
                $ticketData['asset_id'] = $data['asset_id'];
            }
            if (isset($data['damage_type'])) {
                $ticketData['damage_type'] = $data['damage_type'];
            }

            // Create ticket
            $ticket = HelpdeskTicket::create($ticketData);

            // Generate proper ticket number based on ID
            $ticket->ticket_number = $this->generateTicketNumber($ticket->id);
            $ticket->save();

            // Generate status token for guest status checking (per Req 1.5, 2.1)
            $statusToken = $this->tokenService->generateStatusToken($ticket);

            // Calculate and set SLA due dates
            $this->setSLADueDates($ticket);

            DB::commit();

            Log::info('Helpdesk ticket created', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'user_id' => $ticket->user_id,
                'is_guest' => ! $isAuthenticated,
                'guest_email' => $ticket->guest_email,
            ]);

            // Store status token in ticket for email notification
            $ticket->setAttribute('plain_status_token', $statusToken);

            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Helpdesk ticket creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            throw $e;
        }
    }

    /**
     * Update ticket status with required comment
     *
     * Implements audit trail requirement (Req 5.3) by requiring a comment
     * for all status changes. Logs change in dual audit system.
     *
     * @param  HelpdeskTicket  $ticket  The ticket to update
     * @param  string  $status  New status value
     * @param  string  $comment  Required comment explaining the status change
     *
     * @throws \InvalidArgumentException If comment is empty
     */
    public function updateStatus(HelpdeskTicket $ticket, string $status, string $comment): void
    {
        if (empty(trim($comment))) {
            throw new \InvalidArgumentException('Comment is required for status updates');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $ticket->status;

            // Update ticket status
            $ticket->status = $status;

            // Set timestamps based on status
            if ($status === 'resolved' && $ticket->resolved_at === null) {
                $ticket->resolved_at = now();
            }
            if ($status === 'closed' && $ticket->closed_at === null) {
                $ticket->closed_at = now();
            }

            $ticket->save();

            // Add comment to ticket
            $ticket->comments()->create([
                'user_id' => Auth::id(),
                'commenter_name' => Auth::user()?->name ?? 'System',
                'commenter_email' => Auth::user()?->email ?? 'system@ictserve.motac.gov.my',
                'comment' => $comment,
                'is_internal' => false,
                'is_status_change' => true,
            ]);

            DB::commit();

            Log::info('Ticket status updated', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'updated_by' => Auth::id(),
            ]);

            $notification = new HelpdeskTicketStatusUpdated(
                $ticket,
                $oldStatus,
                $status,
                $comment
            );

            if ($ticket->isGuestSubmission() && $ticket->guest_email) {
                Notification::route('mail', $ticket->guest_email)
                    ->notify($notification);
            }

            if ($ticket->user) {
                $ticket->user->notify($notification);
            }

            if ($ticket->assignedUser && $ticket->assigned_to_user !== $ticket->user_id) {
                $ticket->assignedUser->notify($notification);
            }

            event(new TicketStatusChanged($ticket));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Ticket status update failed', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Assign ticket to an admin user
     *
     * Implements real-time notification requirement (Req 5.4) via Laravel Reverb.
     * Records assignment timestamp for SLA tracking.
     *
     * @param  HelpdeskTicket  $ticket  The ticket to assign
     * @param  User  $admin  The admin user to assign to
     *
     * @throws \InvalidArgumentException If admin user is not valid
     */
    public function assignTicket(HelpdeskTicket $ticket, User $admin): void
    {
        // Validate admin has appropriate role
        if (! in_array($admin->role, ['admin', 'superuser'])) {
            throw new \InvalidArgumentException('User must have admin or superuser role');
        }

        try {
            DB::beginTransaction();

            $previousAssignee = $ticket->assigned_to_user;

            // Update assignment
            $ticket->assigned_to_user = $admin->id;
            $ticket->assigned_at = now();
            $ticket->save();

            // Add internal comment about assignment
            $ticket->comments()->create([
                'user_id' => Auth::id(),
                'commenter_name' => Auth::user()?->name ?? 'System',
                'commenter_email' => Auth::user()?->email ?? 'system@ictserve.motac.gov.my',
                'comment' => "Ticket assigned to {$admin->name}",
                'is_internal' => true,
            ]);

            DB::commit();

            Log::info('Ticket assigned', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'assigned_to' => $admin->id,
                'assigned_by' => Auth::id(),
                'previous_assignee' => $previousAssignee,
            ]);

            event(new TicketAssigned(
                ticket: $ticket,
                assignedUser: $admin,
                assignedBy: Auth::user(),
            ));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Ticket assignment failed', [
                'ticket_id' => $ticket->id,
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Retrieve ticket by status token
     *
     * Implements guest status checking (Req 2.1) using SHA-512 hashed tokens.
     * Validates token and returns ticket if found.
     *
     * @param  string  $token  The plain status token
     * @return HelpdeskTicket|null The ticket if found, null otherwise
     */
    public function getByStatusToken(string $token): ?HelpdeskTicket
    {
        try {
            $ticket = $this->tokenService->validateStatusToken($token, 'ticket');

            if ($ticket instanceof HelpdeskTicket) {
                Log::info('Ticket retrieved by status token', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                ]);

                return $ticket;
            }

            Log::warning('Invalid status token provided', [
                'token_length' => strlen($token),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Status token validation failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Calculate SLA due date based on category
     *
     * Implements SLA tracking requirement (Req 5.3, 5.5).
     * Considers category SLA settings and priority multipliers.
     *
     * @param  string  $category  The ticket category name or ID
     * @return Carbon The calculated SLA due date
     */
    public function calculateSLADueDate(string $category): Carbon
    {
        // Find category by name or ID
        $categoryModel = is_numeric($category)
            ? TicketCategory::find($category)
            : TicketCategory::where('name', $category)->first();

        // Get SLA hours from category or use defaults
        $resolutionHours = $categoryModel?->sla_resolution_hours ?? self::DEFAULT_RESOLUTION_HOURS;

        // Calculate due date from now
        return now()->addHours($resolutionHours);
    }

    /**
     * Check if ticket has breached SLA
     *
     * Implements SLA breach detection (Req 5.5) for warning notifications.
     * Checks both response and resolution SLA thresholds.
     *
     * @param  HelpdeskTicket  $ticket  The ticket to check
     * @return bool True if SLA is breached
     */
    public function checkSLABreach(HelpdeskTicket $ticket): bool
    {
        $now = now();

        // Check response SLA breach
        if ($ticket->sla_response_due_at && $ticket->responded_at === null) {
            if ($now->isAfter($ticket->sla_response_due_at)) {
                return true;
            }
        }

        // Check resolution SLA breach
        if ($ticket->sla_resolution_due_at && $ticket->resolved_at === null) {
            if ($now->isAfter($ticket->sla_resolution_due_at)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate ticket number in format HD-YYYYMM-XXXX
     *
     * @param  int  $ticketId  The ticket ID
     * @return string The formatted ticket number
     */
    private function generateTicketNumber(int $ticketId): string
    {
        $yearMonth = now()->format('Ym');
        $sequence = str_pad((string) $ticketId, 4, '0', STR_PAD_LEFT);

        return "HD-{$yearMonth}-{$sequence}";
    }

    /**
     * Set SLA due dates for ticket based on category
     *
     * @param  HelpdeskTicket  $ticket  The ticket to set SLA dates for
     */
    private function setSLADueDates(HelpdeskTicket $ticket): void
    {
        $dueDates = $this->slaService->calculateDueDates($ticket);

        $ticket->sla_response_due_at = $dueDates['sla_response_due_at'];
        $ticket->sla_resolution_due_at = $dueDates['sla_resolution_due_at'];
        $ticket->save();
    }
}
