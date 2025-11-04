<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Notifications\TicketStatusUpdatedNotification;
use App\Services\SLATrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Helpdesk Ticket Observer
 *
 * Handles ticket lifecycle events and triggers notifications.
 *
 * @trace Requirements 2.2, 10.1, 22.3
 */
class HelpdeskTicketObserver
{
    public function __construct(
        private SLATrackingService $slaService
    ) {}

    /**
     * Handle the HelpdeskTicket "created" event.
     */
    public function created(HelpdeskTicket $ticket): void
    {
        // Generate ticket number if not set
        if (! $ticket->ticket_number) {
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->saveQuietly();
        }

        // Calculate SLA due dates
        $this->slaService->calculateSLADueDates($ticket);

        // Handle asset-ticket linking if asset_id is present
        if ($ticket->asset_id) {
            $this->handleAssetTicketLinking($ticket);
        }

        Log::info('Ticket created', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'submitter' => $ticket->getSubmitterIdentifier(),
            'asset_linked' => $ticket->asset_id !== null,
        ]);
    }

    /**
     * Handle the HelpdeskTicket "updated" event.
     */
    public function updated(HelpdeskTicket $ticket): void
    {
        // Check if status changed
        if ($ticket->isDirty('status')) {
            $oldStatus = $ticket->getOriginal('status');
            $newStatus = $ticket->status;

            // Skip notification if old status is null (initial creation)
            if ($oldStatus === null) {
                return;
            }

            // Send notification to submitter
            try {
                if ($ticket->isGuestSubmission()) {
                    // Send to guest email
                    Notification::route('mail', $ticket->guest_email)
                        ->notify(new TicketStatusUpdatedNotification($ticket, $oldStatus, $newStatus));
                } elseif ($ticket->user) {
                    // Send to authenticated user
                    $ticket->user->notify(new TicketStatusUpdatedNotification($ticket, $oldStatus, $newStatus));
                }

                Log::info('Status update notification sent', [
                    'ticket_id' => $ticket->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send status update notification', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Mark as responded if status changed to in_progress
        if ($ticket->isDirty('status') && $ticket->status === 'in_progress') {
            $this->slaService->markAsResponded($ticket);
        }
    }

    /**
     * Handle the HelpdeskTicket "deleting" event.
     */
    public function deleting(HelpdeskTicket $ticket): void
    {
        Log::info('Ticket deleting', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }

    /**
     * Handle asset-ticket linking when ticket is created with asset_id
     *
     * @see Requirement 2.2 Automatic asset-ticket linking
     */
    private function handleAssetTicketLinking(HelpdeskTicket $ticket): void
    {
        try {
            // Find active loan applications for this asset
            $activeLoanApplications = LoanApplication::where('asset_id', $ticket->asset_id)
                ->whereIn('status', ['approved', 'issued', 'overdue'])
                ->get();

            foreach ($activeLoanApplications as $loanApplication) {
                // Create cross-module integration record
                $integration = CrossModuleIntegration::create([
                    'helpdesk_ticket_id' => $ticket->id,
                    'asset_loan_id' => $loanApplication->id,
                    'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
                    'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
                    'integration_data' => [
                        'asset_id' => $ticket->asset_id,
                        'asset_tag' => $ticket->relatedAsset?->asset_tag,
                        'ticket_category' => $ticket->category?->name,
                        'loan_status' => $loanApplication->status->value,
                        'loan_application_number' => $loanApplication->application_number,
                        'linked_at' => now()->toIso8601String(),
                    ],
                    'processed_at' => now(),
                ]);

                Log::info('Asset-ticket linking created', [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'loan_application_id' => $loanApplication->id,
                    'application_number' => $loanApplication->application_number,
                    'integration_id' => $integration->id,
                ]);
            }

            // Update asset maintenance_tickets_count
            if ($ticket->relatedAsset) {
                $ticket->relatedAsset->increment('maintenance_tickets_count');

                Log::info('Asset maintenance ticket count updated', [
                    'asset_id' => $ticket->asset_id,
                    'asset_tag' => $ticket->relatedAsset->asset_tag,
                    'new_count' => $ticket->relatedAsset->maintenance_tickets_count,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create asset-ticket linking', [
                'ticket_id' => $ticket->id,
                'asset_id' => $ticket->asset_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Don't throw exception to prevent ticket creation failure
            // The linking can be done manually if automatic linking fails
        }
    }
}
