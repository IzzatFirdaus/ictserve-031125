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
     *
     * @param Asset $asset
     * @param LoanApplication $application
     * @param array $damageData
     * @return HelpdeskTicket
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
     *
     * @param Asset $asset
     * @param LoanApplication $application
     * @param array $damageData
     * @return string
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
     *
     * @return string
     */
    private function generateTicketNumber(): string
    {
        $year = now()->year;
        $sequence = HelpdeskTicket::whereYear('created_at', $year)->count() + 1;

        return sprintf('HD%s%06d', $year, $sequence);
    }

    /**
     * Get unified asset history (loans + helpdesk tickets)
     *
     * @param int $assetId
     * @return array
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
        usort($history, fn($a, $b) => $b['date'] <=> $a['date']);

        return $history;
    }
}
