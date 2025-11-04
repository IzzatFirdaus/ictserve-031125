<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\AssetStatus;
use App\Events\AssetReturnedDamaged;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Create Maintenance Ticket For Damaged Asset Listener
 *
 * Automatically creates a maintenance ticket when an asset is returned with damage.
 * Implements cross-module integration between asset loan and helpdesk systems.
 *
 * @see D03-FR-016.2 Cross-module integration
 * @see D03-FR-018.3 Asset lifecycle tracking
 * @see Requirement 2.3 Automatic maintenance ticket creation within 5 seconds
 * @see Requirement 8.4 Cross-module event notifications
 */
class CreateMaintenanceTicketForDamagedAsset implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array<int, int>
     */
    public $backoff = [10, 30, 60]; // Exponential backoff: 10s, 30s, 60s

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create the event listener.
     */
    public function __construct(
        private NotificationService $notificationService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AssetReturnedDamaged $event): void
    {
        try {
            DB::beginTransaction();

            $transaction = $event->transaction;
            $asset = $event->asset;
            $loanApplication = $transaction->loanApplication;

            // Get maintenance category
            $maintenanceCategory = TicketCategory::where('name', 'maintenance')
                ->orWhere('slug', 'maintenance')
                ->first();

            if (! $maintenanceCategory) {
                Log::error('Maintenance category not found for automatic ticket creation');
                DB::rollBack();

                return;
            }

            // Create maintenance ticket
            $ticket = HelpdeskTicket::create([
                'ticket_number' => 'TEMP-'.uniqid(), // Temporary, will be replaced
                'subject' => "Asset Maintenance Required: {$asset->name} ({$asset->asset_tag})",
                'description' => $this->buildMaintenanceDescription($asset, $loanApplication, $transaction),
                'category_id' => $maintenanceCategory->id,
                'priority' => $this->determinePriority($asset, $transaction),
                'status' => 'open',
                'asset_id' => $asset->id,
                // Use guest fields from loan application for tracking
                'guest_name' => $loanApplication->applicant_name,
                'guest_email' => $loanApplication->applicant_email,
                'guest_phone' => $loanApplication->applicant_phone ?? null,
                'guest_staff_id' => $loanApplication->staff_id ?? null,
                'guest_grade' => null, // Not available in loan application
                'guest_division' => null, // Not available in loan application
                'user_id' => null, // System-generated ticket
                'damage_type' => $transaction->damage_report ?? 'Asset returned with damage',
            ]);

            // Generate proper ticket number
            $ticket->ticket_number = $ticket->generateTicketNumber();
            $ticket->save();

            // Calculate SLA due dates
            if ($ticket->category) {
                $ticket->calculateSLADueDates();
            }

            // Update asset status and maintenance count
            $asset->update([
                'status' => AssetStatus::MAINTENANCE,
                'condition' => $transaction->condition_after,
                'maintenance_tickets_count' => $asset->maintenance_tickets_count + 1,
                'last_maintenance_date' => now(),
            ]);

            // Create cross-module integration record
            $integration = CrossModuleIntegration::create([
                'helpdesk_ticket_id' => $ticket->id,
                'loan_application_id' => $loanApplication->id,
                'integration_type' => CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST,
                'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
                'integration_data' => [
                    'asset_id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'transaction_id' => $transaction->id,
                    'condition_before' => $transaction->condition_before?->value,
                    'condition_after' => $transaction->condition_after?->value,
                    'damage_report' => $transaction->damage_report,
                    'loan_application_number' => $loanApplication->application_number,
                    'created_at' => now()->toIso8601String(),
                ],
                'processed_at' => now(),
            ]);

            DB::commit();

            // Send notifications to maintenance team
            $this->notificationService->sendMaintenanceNotification($ticket, $asset, $loanApplication);

            Log::info('Automatic maintenance ticket created for damaged asset', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'asset_id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'loan_application_id' => $loanApplication->id,
                'application_number' => $loanApplication->application_number,
                'integration_id' => $integration->id,
                'processing_time_ms' => round((microtime(true) - LARAVEL_START) * 1000, 2),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create automatic maintenance ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $event->asset->id ?? null,
                'transaction_id' => $event->transaction->id ?? null,
            ]);

            // Re-throw to trigger queue retry mechanism
            throw $e;
        }
    }

    /**
     * Build comprehensive maintenance ticket description
     */
    private function buildMaintenanceDescription($asset, $loanApplication, $transaction): string
    {
        $description = "Asset returned from loan application requires maintenance due to damage or condition change.\n\n";

        $description .= "**Asset Details:**\n";
        $description .= "- Asset Tag: {$asset->asset_tag}\n";
        $description .= "- Name: {$asset->name}\n";
        $description .= "- Brand/Model: {$asset->brand} {$asset->model}\n";
        if ($asset->serial_number) {
            $description .= "- Serial Number: {$asset->serial_number}\n";
        }
        $description .= "- Previous Condition: {$transaction->condition_before?->label()}\n";
        $description .= "- Current Condition: {$transaction->condition_after?->label()}\n\n";

        $description .= "**Damage Report:**\n";
        $description .= $transaction->damage_report ?? 'No detailed damage report provided.';
        $description .= "\n\n";

        $description .= "**Loan Details:**\n";
        $description .= "- Application Number: {$loanApplication->application_number}\n";
        $description .= "- Borrower: {$loanApplication->applicant_name}\n";
        $description .= "- Email: {$loanApplication->applicant_email}\n";
        if ($loanApplication->applicant_phone) {
            $description .= "- Phone: {$loanApplication->applicant_phone}\n";
        }
        $description .= "- Loan Period: {$loanApplication->loan_start_date->format('Y-m-d')} to {$loanApplication->loan_end_date->format('Y-m-d')}\n";
        $description .= "- Return Date: {$transaction->processed_at->format('Y-m-d H:i:s')}\n\n";

        if ($transaction->notes) {
            $description .= "**Additional Notes:**\n";
            $description .= $transaction->notes."\n\n";
        }

        $description .= "**Action Required:**\n";
        $description .= "Please inspect the asset and perform necessary maintenance or repairs before returning to inventory.\n";

        return $description;
    }

    /**
     * Determine ticket priority based on asset condition and damage severity
     */
    private function determinePriority($asset, $transaction): string
    {
        // Critical priority for severely damaged assets
        if ($transaction->condition_after?->value === 'damaged') {
            return 'critical';
        }

        // High priority for poor condition or valuable assets
        if ($transaction->condition_after?->value === 'poor' || $asset->current_value > 5000) {
            return 'high';
        }

        // Medium priority for fair condition
        if ($transaction->condition_after?->value === 'fair') {
            return 'medium';
        }

        // Default to normal priority
        return 'normal';
    }

    /**
     * Handle a job failure.
     */
    public function failed(AssetReturnedDamaged $event, \Throwable $exception): void
    {
        Log::error('Maintenance ticket creation job failed permanently', [
            'asset_id' => $event->asset->id ?? null,
            'transaction_id' => $event->transaction->id ?? null,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // TODO: Send alert to system administrators about failed automatic ticket creation
    }
}
