<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;

/**
 * Cross-Module Integration Service
 *
 * Manages integration between asset loan and helpdesk modules for maintenance workflows.
 *
 * @see D03-FR-016.1 Cross-module integration
 * @see D03-FR-003.5 Automatic maintenance ticket creation
 * @see D04 §6.2 Cross-module integration service
 */
class CrossModuleIntegrationService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Create maintenance ticket for damaged asset
     */
    public function createMaintenanceTicket(
        Asset $asset,
        LoanApplication $application,
        array $damageData
    ): HelpdeskTicket {
        $ticketData = [
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => "Asset Maintenance Required: {$asset->name} ({$asset->asset_tag})",
            'description' => $this->buildMaintenanceDescription($asset, $application, $damageData),
            'category' => 'maintenance',
            'priority' => 'high',
            'status' => 'open',
            'asset_id' => $asset->id,
            'related_loan_application_id' => $application->id,
            // Use guest fields from loan application
            'guest_name' => $application->applicant_name,
            'guest_email' => $application->applicant_email,
            'guest_phone' => $application->applicant_phone,
            'staff_id' => $application->staff_id,
            'division_id' => $application->division_id,
        ];

        $ticket = HelpdeskTicket::create($ticketData);

        // Update asset status
        $asset->update([
            'status' => AssetStatus::MAINTENANCE,
            'maintenance_tickets_count' => $asset->maintenance_tickets_count + 1,
        ]);

        // Link ticket to loan application
        $application->update([
            'maintenance_required' => true,
            'related_helpdesk_tickets' => array_merge(
                $application->related_helpdesk_tickets ?? [],
                [$ticket->id]
            ),
        ]);

        // Send notification to maintenance team
        $this->notificationService->sendMaintenanceNotification($ticket, $asset, $application);

        Log::info('Maintenance ticket created for damaged asset', [
            'ticket_number' => $ticket->ticket_number,
            'asset_tag' => $asset->asset_tag,
            'application_number' => $application->application_number,
        ]);

        return $ticket;
    }

    /**
     * Build maintenance ticket description
     */
    private function buildMaintenanceDescription(
        Asset $asset,
        LoanApplication $application,
        array $damageData
    ): string {
        $description = "Asset returned from loan application {$application->application_number} requires maintenance.\n\n";
        $description .= "**Asset Details:**\n";
        $description .= "- Asset Tag: {$asset->asset_tag}\n";
        $description .= "- Name: {$asset->name}\n";
        $description .= "- Brand/Model: {$asset->brand} {$asset->model}\n";
        $description .= "- Current Condition: {$asset->condition->label()}\n\n";

        $description .= "**Damage Report:**\n";
        $description .= $damageData['damage_report'] ?? 'No detailed damage report provided.';
        $description .= "\n\n";

        $description .= "**Loan Details:**\n";
        $description .= "- Application Number: {$application->application_number}\n";
        $description .= "- Borrower: {$application->applicant_name}\n";
        $description .= "- Loan Period: {$application->loan_start_date->format('Y-m-d')} to {$application->loan_end_date->format('Y-m-d')}\n";

        return $description;
    }

    /**
     * Generate helpdesk ticket number
     */
    private function generateTicketNumber(): string
    {
        $year = now()->year;
        $sequence = HelpdeskTicket::whereYear('created_at', $year)->count() + 1;

        return sprintf('HD%s%06d', $year, $sequence);
    }

    /**
     * Get unified asset history (loans + helpdesk tickets)
     */
    public function getUnifiedAssetHistory(int $assetId): array
    {
        $asset = Asset::with([
            'loanItems.loanApplication',
            'helpdeskTickets',
        ])->findOrFail($assetId);

        $history = [];

        // Add loan history
        foreach ($asset->loanItems as $loanItem) {
            $history[] = [
                'type' => 'loan',
                'date' => $loanItem->loanApplication->created_at,
                'reference' => $loanItem->loanApplication->application_number,
                'description' => "Loaned to {$loanItem->loanApplication->applicant_name}",
                'status' => $loanItem->loanApplication->status->label(),
            ];
        }

        // Add helpdesk ticket history
        foreach ($asset->helpdeskTickets as $ticket) {
            $history[] = [
                'type' => 'maintenance',
                'date' => $ticket->created_at,
                'reference' => $ticket->ticket_number,
                'description' => $ticket->subject,
                'status' => $ticket->status,
            ];
        }

        // Sort by date descending
        usort($history, fn ($a, $b) => $b['date'] <=> $a['date']);

        return $history;
    }

    /**
     * Link helpdesk ticket to existing loan application
     */
    public function linkTicketToLoan(HelpdeskTicket $ticket, LoanApplication $loanApplication): \App\Models\CrossModuleIntegration
    {
        $integration = \App\Models\CrossModuleIntegration::create([
            'helpdesk_ticket_id' => $ticket->id,
            'asset_loan_id' => $loanApplication->id,
            'integration_type' => \App\Models\CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'trigger_event' => \App\Models\CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
            'integration_data' => [
                'asset_id' => $ticket->asset_id,
                'ticket_category' => $ticket->category->name ?? null,
                'loan_status' => $loanApplication->status,
                'linked_at' => now()->toIso8601String(),
            ],
            'processed_at' => now(),
        ]);

        Log::info('Helpdesk ticket linked to loan application', [
            'ticket_number' => $ticket->ticket_number,
            'application_number' => $loanApplication->application_number,
            'integration_id' => $integration->id,
        ]);

        return $integration;
    }

    /**
     * Get all cross-module integrations for a ticket
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTicketIntegrations(int $ticketId)
    {
        return \App\Models\CrossModuleIntegration::where('helpdesk_ticket_id', $ticketId)
            ->with(['assetLoan'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all cross-module integrations for a loan application
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoanIntegrations(int $loanId)
    {
        return \App\Models\CrossModuleIntegration::where('asset_loan_id', $loanId)
            ->with(['helpdeskTicket'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if asset has pending maintenance tickets
     */
    public function hasPendingMaintenanceTickets(int $assetId): bool
    {
        return HelpdeskTicket::where('asset_id', $assetId)
            ->whereIn('status', ['new', 'assigned', 'in_progress'])
            ->where('category_id', $this->getMaintenanceCategoryId())
            ->exists();
    }

    /**
     * Get maintenance statistics for asset
     */
    public function getAssetMaintenanceStats(int $assetId): array
    {
        $tickets = HelpdeskTicket::where('asset_id', $assetId)
            ->where('category_id', $this->getMaintenanceCategoryId())
            ->get();

        return [
            'total_tickets' => $tickets->count(),
            'open_tickets' => $tickets->whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
            'resolved_tickets' => $tickets->where('status', 'resolved')->count(),
            'closed_tickets' => $tickets->where('status', 'closed')->count(),
            'average_resolution_time' => $this->calculateAverageResolutionTime($tickets),
            'last_maintenance_date' => $tickets->max('created_at'),
        ];
    }

    /**
     * Calculate average resolution time for tickets
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $tickets
     * @return float|null Hours
     */
    private function calculateAverageResolutionTime($tickets): ?float
    {
        $resolvedTickets = $tickets->whereNotNull('resolved_at');

        if ($resolvedTickets->isEmpty()) {
            return null;
        }

        $totalHours = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return round($totalHours / $resolvedTickets->count(), 2);
    }

    /**
     * Get maintenance category ID
     */
    private function getMaintenanceCategoryId(): ?int
    {
        $category = \App\Models\TicketCategory::where('name', 'maintenance')->first();

        return $category?->id;
    }
}
