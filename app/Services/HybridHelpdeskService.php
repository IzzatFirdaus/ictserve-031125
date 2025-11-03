<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskComment;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Notifications\HelpdeskTicketCreated;
use App\Notifications\HelpdeskTicketClaimed;
use App\Notifications\MaintenanceTicketCreated;
use App\Traits\CrossModuleIntegration as CrossModuleIntegrationTrait;
use App\Traits\OptimizedQueries;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

/**
 * HybridHelpdeskService - Service for managing helpdesk tickets with hybrid architecture
 *
 * Supports both guest submissions (no authentication) and authenticated submissions (with login).
 * Handles cross-module integration with asset loan system.
 *
 * @see D03 Software Requirements Specification - Requirements 1, 2, 8
 * @see D04 Software Design Document - Hybrid Architecture
 * @see updated-helpdesk-module/design.md - HybridHelpdeskService
 *
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 */
class HybridHelpdeskService
{
    use CrossModuleIntegrationTrait;
    use OptimizedQueries;

    /**
     * Create a guest ticket submission (no authentication required)
     *
     * @param  array  $data  Ticket data including enhanced guest fields
     * @return HelpdeskTicket Created ticket
     *
     * @throws Exception If ticket creation fails
     */
    public function createGuestTicket(array $data): HelpdeskTicket
    {
        DB::beginTransaction();

        try {
            // Create ticket with guest fields
            $ticket = HelpdeskTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'user_id' => null, // Always null for guest submissions

                // Enhanced guest information
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'],
                'guest_phone' => $data['guest_phone'],
                'guest_staff_id' => $data['guest_staff_id'],
                'guest_grade' => $data['guest_grade'],
                'guest_division' => $data['guest_division'],

                // Ticket details
                'subject' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'] ?? null,
                'damage_type' => $data['damage_type'],
                'asset_id' => $data['asset_id'] ?? null,
                'priority' => $this->determinePriority($data),
                'status' => 'new',
            ]);

            // Generate ticket number after creation (needs ID)
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates
            $ticket->calculateSLADueDates();

            // Handle file attachments
            $this->handleAttachments($ticket, $data['attachments'] ?? []);

            // Handle cross-module integration if asset is selected
            $this->handleCrossModuleIntegration($ticket);

            // Send notifications
            $this->notifyAdminsOfNewTicket($ticket);
            $this->sendGuestConfirmationEmail($ticket);

            DB::commit();

            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create guest ticket', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Create an authenticated ticket submission (login required)
     *
     * @param  array  $data  Ticket data with user_id
     * @return HelpdeskTicket Created ticket
     *
     * @throws Exception If ticket creation fails
     */
    public function createAuthenticatedTicket(array $data): HelpdeskTicket
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($data['user_id']);

            // Create ticket linked to authenticated user
            $ticket = HelpdeskTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'user_id' => $data['user_id'],

                // Ticket details
                'subject' => $data['title'],
                'description' => $data['description'],
                'category_id' => $data['category_id'] ?? null,
                'priority' => $data['priority'] ?? $this->determinePriority($data),
                'damage_type' => $data['damage_type'],
                'asset_id' => $data['asset_id'] ?? null,
                'status' => 'new',
            ]);

            // Generate ticket number after creation
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates
            $ticket->calculateSLADueDates();

            // Add internal notes if provided
            if (! empty($data['internal_notes'])) {
                $this->addInternalComment($ticket, $data['internal_notes'], $user);
            }

            // Handle file attachments
            $this->handleAttachments($ticket, $data['attachments'] ?? []);

            // Handle cross-module integration
            $this->handleCrossModuleIntegration($ticket);

            // Auto-assign ticket if possible
            $this->autoAssignTicket($ticket);

            // Send notifications
            $this->sendAuthenticatedNotifications($ticket);

            DB::commit();

            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create authenticated ticket', [
                'error' => $e->getMessage(),
                'user_id' => $data['user_id'] ?? null,
            ]);

            throw $e;
        }
    }

    /**
     * Claim a guest ticket by authenticated user
     *
     * @param  HelpdeskTicket  $ticket  Ticket to claim
     * @param  User  $user  User claiming the ticket
     * @return bool Success status
     *
     * @throws InvalidArgumentException If ticket is not a guest submission
     * @throws Exception If email doesn't match
     */
    public function claimGuestTicket(HelpdeskTicket $ticket, User $user): bool
    {
        if (! $ticket->isGuestSubmission()) {
            throw new InvalidArgumentException('Ticket is not a guest submission');
        }

        if ($ticket->guest_email !== $user->email) {
            throw new Exception('Email does not match ticket submitter');
        }

        DB::beginTransaction();

        try {
            // Link ticket to user
            $ticket->update(['user_id' => $user->id]);

            // Add system comment
            $this->addSystemComment(
                $ticket,
                "Ticket claimed by authenticated user: {$user->name}"
            );

            // Send notification
            $this->sendTicketClaimedNotification($ticket, $user);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to claim guest ticket', [
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle cross-module integration when ticket has related asset
     */
    private function handleCrossModuleIntegration(HelpdeskTicket $ticket): void
    {
        if (! $ticket->hasRelatedAsset()) {
            return;
        }

        // Create cross-module integration record
        CrossModuleIntegration::create([
            'helpdesk_ticket_id' => $ticket->id,
            'asset_loan_id' => null, // Will be linked when asset loan is created
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
            'integration_data' => [
                'asset_id' => $ticket->asset_id,
                'ticket_category' => $ticket->category->name ?? null,
                'damage_type' => $ticket->damage_type,
            ],
        ]);

        // Check for existing loan applications for this asset
        $this->linkExistingAssetLoans($ticket);
    }

    /**
     * Link existing active asset loans to the ticket
     */
    private function linkExistingAssetLoans(HelpdeskTicket $ticket): void
    {
        $activeLoans = LoanApplication::where('asset_id', $ticket->asset_id)
            ->whereIn('status', ['approved', 'active'])
            ->get();

        foreach ($activeLoans as $loan) {
            CrossModuleIntegration::create([
                'helpdesk_ticket_id' => $ticket->id,
                'asset_loan_id' => $loan->id,
                'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
                'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
                'integration_data' => [
                    'loan_application_id' => $loan->id,
                    'borrower_name' => $loan->applicant->name ?? $loan->applicant_name,
                    'loan_status' => $loan->status,
                ],
            ]);
        }
    }

    /**
     * Create maintenance ticket from asset return (cross-module integration)
     *
     * @param  array  $assetReturnData  Data from asset return
     * @return HelpdeskTicket Created maintenance ticket
     */
    public function createMaintenanceTicketFromAssetReturn(array $assetReturnData): HelpdeskTicket
    {
        DB::beginTransaction();

        try {
            $ticket = HelpdeskTicket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'user_id' => null, // System-generated ticket
                'subject' => "Asset Maintenance Required: {$assetReturnData['asset_name']}",
                'description' => "Automatic maintenance ticket created due to asset return condition.\n\n" .
                    "Asset: {$assetReturnData['asset_name']} ({$assetReturnData['asset_tag']})\n" .
                    "Return Condition: {$assetReturnData['return_condition']}\n" .
                    "Damage Description: {$assetReturnData['damage_description']}\n" .
                    "Returned By: {$assetReturnData['returned_by']}",
                'category_id' => $this->getMaintenanceCategoryId(),
                'priority' => $this->determineMaintenancePriority($assetReturnData['return_condition']),
                'damage_type' => $assetReturnData['return_condition'],
                'asset_id' => $assetReturnData['asset_id'],
                'status' => 'new',
            ]);

            // Generate ticket number
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->save();

            // Calculate SLA
            $ticket->calculateSLADueDates();

            // Create cross-module integration record
            CrossModuleIntegration::create([
                'helpdesk_ticket_id' => $ticket->id,
                'asset_loan_id' => $assetReturnData['loan_application_id'],
                'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
                'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
                'integration_data' => $assetReturnData,
                'processed_at' => now(),
            ]);

            // Add system comment
            $this->addSystemComment(
                $ticket,
                "Maintenance ticket automatically created from asset return (Loan ID: {$assetReturnData['loan_application_id']})"
            );

            // Auto-assign to maintenance team
            $this->autoAssignMaintenanceTicket($ticket);

            // Notify maintenance team
            $this->notifyMaintenanceTeam($ticket);

            DB::commit();

            return $ticket;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create maintenance ticket from asset return', [
                'error' => $e->getMessage(),
                'asset_return_data' => $assetReturnData,
            ]);

            throw $e;
        }
    }

    /**
     * Add internal comment to ticket
     */
    private function addInternalComment(HelpdeskTicket $ticket, string $comment, User $user): void
    {
        HelpdeskComment::create([
            'helpdesk_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'comment' => $comment,
            'is_internal' => true,
            'is_system_generated' => false,
        ]);
    }

    /**
     * Add system-generated comment
     */
    private function addSystemComment(HelpdeskTicket $ticket, string $comment): void
    {
        HelpdeskComment::create([
            'helpdesk_ticket_id' => $ticket->id,
            'user_id' => null,
            'comment' => $comment,
            'is_internal' => true,
            'is_system_generated' => true,
        ]);
    }

    /**
     * Handle file attachments
     */
    private function handleAttachments(HelpdeskTicket $ticket, array $attachments): void
    {
        foreach ($attachments as $file) {
            $path = $file->store('helpdesk-attachments', 'private');

            $ticket->attachments()->create([
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }

    /**
     * Generate temporary ticket number (will be replaced after save)
     */
    private function generateTicketNumber(): string
    {
        return 'HD' . date('Y') . 'TEMP' . uniqid();
    }

    /**
     * Determine ticket priority based on data
     */
    private function determinePriority(array $data): string
    {
        // Priority logic based on category, damage type, etc.
        if (isset($data['category_id'])) {
            $category = \App\Models\TicketCategory::find($data['category_id']);
            if ($category && $category->default_priority) {
                return $category->default_priority;
            }
        }

        return 'medium';
    }

    /**
     * Determine maintenance priority based on condition
     */
    private function determineMaintenancePriority(string $condition): string
    {
        return match ($condition) {
            'severely_damaged', 'broken' => 'high',
            'damaged', 'faulty' => 'medium',
            'minor_damage', 'wear_and_tear' => 'low',
            default => 'medium'
        };
    }

    /**
     * Get maintenance category ID
     */
    private function getMaintenanceCategoryId(): ?int
    {
        $category = \App\Models\TicketCategory::where('name', 'maintenance')->first();

        return $category?->id;
    }

    /**
     * Auto-assign ticket to appropriate division/user
     */
    private function autoAssignTicket(HelpdeskTicket $ticket): void
    {
        // Auto-assignment logic based on category, division, etc.
        // This can be enhanced based on business rules
    }

    /**
     * Auto-assign maintenance ticket to maintenance team
     */
    private function autoAssignMaintenanceTicket(HelpdeskTicket $ticket): void
    {
        // Find maintenance division/team and assign
        // This can be enhanced based on organizational structure
    }

    /**
     * Notify admins of new ticket
     */
    private function notifyAdminsOfNewTicket(HelpdeskTicket $ticket): void
    {
        $admins = User::whereIn('role', ['admin', 'superuser'])->get();

        Notification::send($admins, new HelpdeskTicketCreated($ticket));
    }

    /**
     * Send guest confirmation email
     */
    private function sendGuestConfirmationEmail(HelpdeskTicket $ticket): void
    {
        // Email will be sent via notification system
        // Implementation in Task 11
    }

    /**
     * Send authenticated user notifications
     */
    private function sendAuthenticatedNotifications(HelpdeskTicket $ticket): void
    {
        // Email will be sent via notification system
        // Implementation in Task 11
    }

    /**
     * Send ticket claimed notification
     */
    private function sendTicketClaimedNotification(HelpdeskTicket $ticket, User $user): void
    {
        $user->notify(new HelpdeskTicketClaimed($ticket));
    }

    /**
     * Notify maintenance team
     */
    private function notifyMaintenanceTeam(HelpdeskTicket $ticket): void
    {
        // Find maintenance team members and notify
        // Implementation in Task 11
    }
}
