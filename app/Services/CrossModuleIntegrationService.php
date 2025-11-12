<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\CrossModuleIntegration;
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
        // Get or create maintenance category
        $maintenanceCategory = \App\Models\TicketCategory::firstOrCreate(
            ['code' => 'MAINTENANCE'],
            [
                'code' => 'MAINTENANCE',
                'name_ms' => 'Penyelenggaraan Aset',
                'name_en' => 'Asset Maintenance',
                'description_ms' => 'Permintaan penyelenggaraan dan pembaikan aset ICT',
                'description_en' => 'Asset maintenance and repair requests',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 48,
                'is_active' => true,
            ]
        );

        $ticketData = [
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => "Asset Maintenance Required: {$asset->name} ({$asset->asset_tag})",
            'description' => $this->buildMaintenanceDescription($asset, $application, $damageData),
            'category_id' => $maintenanceCategory->id,
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
            'loan_application_id' => $loanApplication->id,
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
        return \App\Models\CrossModuleIntegration::where('loan_application_id', $loanId)
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
            ->whereIn('status', ['open', 'assigned', 'in_progress'])
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
            'open_tickets' => $tickets->whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
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
        $category = \App\Models\TicketCategory::where('code', 'MAINTENANCE')->first();

        return $category?->id;
    }

    /**
     * Sync asset status between helpdesk and loan systems
     *
     * @param  int  $assetId  Asset ID
     */
    public function syncAssetStatus(int $assetId): void
    {
        $asset = Asset::findOrFail($assetId);

        // Check if asset has open maintenance tickets
        $hasOpenTickets = $this->hasPendingMaintenanceTickets($assetId);

        if ($hasOpenTickets && $asset->status !== AssetStatus::MAINTENANCE) {
            $asset->update(['status' => AssetStatus::MAINTENANCE]);

            Log::info('Asset status synced to MAINTENANCE due to open tickets', [
                'asset_id' => $assetId,
                'asset_tag' => $asset->asset_tag,
            ]);
        } elseif (! $hasOpenTickets && $asset->status === AssetStatus::MAINTENANCE) {
            // Check if asset is currently loaned
            $isLoaned = $asset->loanItems()
                ->whereHas('loanApplication', function ($query) {
                    $query->whereIn('status', ['issued', 'in_use']);
                })
                ->exists();

            $newStatus = $isLoaned ? AssetStatus::LOANED : AssetStatus::AVAILABLE;
            $asset->update(['status' => $newStatus]);

            Log::info('Asset status synced after maintenance completion', [
                'asset_id' => $assetId,
                'asset_tag' => $asset->asset_tag,
                'new_status' => $newStatus->value,
            ]);
        }
    }

    /**
     * Schedule maintenance for asset
     *
     * @param  int  $assetId  Asset ID
     * @param  array  $maintenanceData  Maintenance details
     */
    public function scheduleMaintenance(int $assetId, array $maintenanceData): HelpdeskTicket
    {
        $asset = Asset::findOrFail($assetId);

        // Get or create maintenance category
        $maintenanceCategory = \App\Models\TicketCategory::firstOrCreate(
            ['code' => 'MAINTENANCE'],
            [
                'code' => 'MAINTENANCE',
                'name_ms' => 'Penyelenggaraan Aset',
                'name_en' => 'Asset Maintenance',
                'description_ms' => 'Permintaan penyelenggaraan dan pembaikan aset ICT',
                'description_en' => 'Asset maintenance and repair requests',
                'sla_response_hours' => 4,
                'sla_resolution_hours' => 48,
                'is_active' => true,
            ]
        );

        $ticketData = [
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => "Scheduled Maintenance: {$asset->name} ({$asset->asset_tag})",
            'description' => $maintenanceData['description'] ?? 'Scheduled preventive maintenance',
            'category_id' => $maintenanceCategory->id,
            'priority' => $maintenanceData['priority'] ?? 'medium',
            'status' => 'open',
            'asset_id' => $asset->id,
            'scheduled_date' => $maintenanceData['scheduled_date'] ?? now()->addDays(7),
            'guest_name' => 'System Administrator',
            'guest_email' => config('mail.from.address'),
        ];

        $ticket = HelpdeskTicket::create($ticketData);

        // Update asset next maintenance date
        $asset->update([
            'next_maintenance_date' => $maintenanceData['scheduled_date'] ?? now()->addDays(7),
        ]);

        Log::info('Maintenance scheduled for asset', [
            'ticket_number' => $ticket->ticket_number,
            'asset_tag' => $asset->asset_tag,
            'scheduled_date' => $ticket->scheduled_date,
        ]);

        return $ticket;
    }

    /**
     * Complete maintenance and update asset status
     *
     * @param  int  $ticketId  Helpdesk ticket ID
     * @param  array  $completionData  Completion details
     */
    public function completeMaintenanceTicket(int $ticketId, array $completionData): void
    {
        $ticket = HelpdeskTicket::findOrFail($ticketId);

        if (! $ticket->asset_id) {
            throw new \Exception('Ticket is not associated with an asset');
        }

        $asset = Asset::findOrFail($ticket->asset_id);

        // Update ticket status
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $completionData['resolution_notes'] ?? null,
        ]);

        // Update asset condition and maintenance date
        $asset->update([
            'condition' => $completionData['asset_condition'] ?? $asset->condition,
            'last_maintenance_date' => now(),
            'next_maintenance_date' => $completionData['next_maintenance_date'] ?? now()->addMonths(6),
        ]);

        // Sync asset status
        $this->syncAssetStatus($asset->id);

        Log::info('Maintenance ticket completed', [
            'ticket_number' => $ticket->ticket_number,
            'asset_tag' => $asset->asset_tag,
            'asset_condition' => $asset->condition->value,
        ]);
    }

    /**
     * Get integrated reporting data for asset lifecycle
     *
     * @param  int  $assetId  Asset ID
     * @return array Comprehensive asset lifecycle report
     */
    public function getAssetLifecycleReport(int $assetId): array
    {
        $asset = Asset::with([
            'loanItems.loanApplication',
            'helpdeskTickets',
        ])->findOrFail($assetId);

        $loanHistory = $asset->loanItems->map(function ($loanItem) {
            return [
                'type' => 'loan',
                'application_number' => $loanItem->loanApplication->application_number,
                'borrower' => $loanItem->loanApplication->applicant_name,
                'start_date' => $loanItem->loanApplication->loan_start_date,
                'end_date' => $loanItem->loanApplication->loan_end_date,
                'status' => $loanItem->loanApplication->status->label(),
                'condition_before' => $loanItem->condition_before ?? null,
                'condition_after' => $loanItem->condition_after ?? null,
            ];
        });

        $maintenanceHistory = $asset->helpdeskTickets->map(function ($ticket) {
            return [
                'type' => 'maintenance',
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'created_at' => $ticket->created_at,
                'resolved_at' => $ticket->resolved_at,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
            ];
        });

        $maintenanceStats = $this->getAssetMaintenanceStats($assetId);

        return [
            'asset' => [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'current_status' => $asset->status->label(),
                'current_condition' => $asset->condition->label(),
                'purchase_date' => $asset->purchase_date,
                'last_maintenance_date' => $asset->last_maintenance_date,
                'next_maintenance_date' => $asset->next_maintenance_date,
            ],
            'loan_history' => $loanHistory,
            'maintenance_history' => $maintenanceHistory,
            'maintenance_statistics' => $maintenanceStats,
            'total_loans' => $loanHistory->count(),
            'total_maintenance_tickets' => $maintenanceHistory->count(),
            'utilization_rate' => $this->calculateUtilizationRate($asset),
        ];
    }

    /**
     * Calculate asset utilization rate
     *
     * @param  Asset  $asset  Asset model
     * @return float Utilization rate percentage
     */
    private function calculateUtilizationRate(Asset $asset): float
    {
        $totalDays = $asset->purchase_date ? now()->diffInDays($asset->purchase_date) : 0;

        if ($totalDays === 0) {
            return 0.0;
        }

        $loanedDays = $asset->loanItems()
            ->whereHas('loanApplication', function ($query) {
                $query->whereIn('status', ['completed', 'returned']);
            })
            ->get()
            ->sum(function ($loanItem) {
                $start = $loanItem->loanApplication->loan_start_date;
                $end = $loanItem->loanApplication->loan_end_date;

                return $start && $end ? $start->diffInDays($end) : 0;
            });

        return round(($loanedDays / $totalDays) * 100, 2);
    }

    /**
     * Trigger automatic maintenance based on usage patterns
     *
     * @param  int  $assetId  Asset ID
     * @return HelpdeskTicket|null Maintenance ticket if triggered
     */
    public function triggerPreventiveMaintenance(int $assetId): ?HelpdeskTicket
    {
        $asset = Asset::findOrFail($assetId);

        // Check if maintenance is due
        $maintenanceDue = $asset->next_maintenance_date && $asset->next_maintenance_date->isPast();

        // Check usage-based triggers
        $loanCount = $asset->loanItems()->count();
        $usageThreshold = 10; // Trigger maintenance after 10 loans

        if ($maintenanceDue || $loanCount >= $usageThreshold) {
            return $this->scheduleMaintenance($assetId, [
                'description' => "Preventive maintenance triggered. Loan count: {$loanCount}",
                'priority' => 'medium',
                'scheduled_date' => now()->addDays(3),
            ]);
        }

        return null;
    }

    /**
     * Handle asset return with damage assessment and automatic ticket creation
     *
     * @param  LoanApplication  $application  Loan application
     * @param  array  $returnData  Return data including asset conditions
     *
     * @see D03-FR-016.1 Automatic ticket creation
     * @see D03-FR-003.5 Damage reporting
     */
    public function handleAssetReturn(LoanApplication $application, array $returnData): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            foreach ($application->loanItems as $loanItem) {
                $asset = $loanItem->asset;
                $assetReturnData = $returnData['assets'][$asset->id] ?? [];

                if (!isset($assetReturnData['condition'])) {
                    continue; // Skip assets without condition data
                }

                $returnCondition = \App\Enums\AssetCondition::from($assetReturnData['condition']);

                // Update asset condition
                $asset->update([
                    'condition' => $returnCondition,
                    'status' => $this->determineAssetStatus($returnCondition),
                    'last_maintenance_date' => in_array($returnCondition, [\App\Enums\AssetCondition::DAMAGED, \App\Enums\AssetCondition::POOR])
                        ? now()
                        : $asset->last_maintenance_date,
                ]);

                // Update loan item with return condition
                $loanItem->update([
                    'condition_after' => $returnCondition,
                    'accessories_returned' => $assetReturnData['accessories_returned'] ?? null,
                    'damage_report' => $assetReturnData['damage_report'] ?? null,
                ]);

                // Create helpdesk ticket for damaged assets
                if (in_array($returnCondition, [\App\Enums\AssetCondition::DAMAGED, \App\Enums\AssetCondition::POOR])) {
                    $ticket = $this->createMaintenanceTicket($asset, $application, $assetReturnData);

                    // Create cross-module integration record
                    \App\Models\CrossModuleIntegration::create([
                        'helpdesk_ticket_id' => $ticket->id,
                        'loan_application_id' => $application->id,
                        'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
                        'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
                        'integration_data' => [
                            'asset_id' => $asset->id,
                            'damage_report' => $assetReturnData['damage_report'] ?? null,
                            'condition_before' => $loanItem->condition_before?->value,
                            'condition_after' => $returnCondition->value,
                        ],
                        'processed_at' => now(),
                    ]);
                }

                // Log transaction
                \App\Models\LoanTransaction::create([
                    'loan_application_id' => $application->id,
                    'asset_id' => $asset->id,
                    'transaction_type' => 'return',
                    'processed_by' => auth()->id() ?? 1,
                    'processed_at' => now(),
                    'condition_before' => $loanItem->condition_before,
                    'condition_after' => $returnCondition,
                    'damage_report' => $assetReturnData['damage_report'] ?? null,
                    'notes' => $returnData['notes'] ?? null,
                ]);
            }

            // Update application status
            $application->update([
                'status' => \App\Enums\LoanStatus::RETURNED,
                'maintenance_required' => $application->loanItems->contains(function ($item) {
                    return in_array($item->condition_after, [\App\Enums\AssetCondition::DAMAGED, \App\Enums\AssetCondition::POOR]);
                }),
            ]);

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $e;
        }
    }

    /**
     * Determine asset status based on condition
     *
     * @param  \App\Enums\AssetCondition  $condition  Asset condition
     * @return \App\Enums\AssetStatus Asset status
     */
    private function determineAssetStatus(\App\Enums\AssetCondition $condition): \App\Enums\AssetStatus
    {
        return match ($condition) {
            \App\Enums\AssetCondition::EXCELLENT, \App\Enums\AssetCondition::GOOD, \App\Enums\AssetCondition::FAIR => AssetStatus::AVAILABLE,
            \App\Enums\AssetCondition::POOR, \App\Enums\AssetCondition::DAMAGED => AssetStatus::MAINTENANCE,
        };
    }

    /**
     * Unified search across loan applications and helpdesk tickets
     *
     * @param  string  $query  Search query
     * @return array Search results
     *
     * @see D03-FR-016.4 Unified search
     */
    public function unifiedSearch(string $query): array
    {
        $results = [
            'loan_applications' => [],
            'helpdesk_tickets' => [],
            'assets' => [],
        ];

        // Search loan applications
        $results['loan_applications'] = LoanApplication::where(function ($q) use ($query) {
            $q->where('application_number', 'like', "%{$query}%")
                ->orWhere('applicant_name', 'like', "%{$query}%")
                ->orWhere('applicant_email', 'like', "%{$query}%")
                ->orWhere('staff_id', 'like', "%{$query}%");
        })
            ->with(['loanItems.asset'])
            ->limit(10)
            ->get();

        // Search helpdesk tickets
        $results['helpdesk_tickets'] = HelpdeskTicket::where(function ($q) use ($query) {
            $q->where('ticket_number', 'like', "%{$query}%")
                ->orWhere('subject', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('guest_name', 'like', "%{$query}%")
                ->orWhere('guest_email', 'like', "%{$query}%");
        })
            ->with(['asset'])
            ->limit(10)
            ->get();

        // Search assets
        $results['assets'] = Asset::where(function ($q) use ($query) {
            $q->where('asset_tag', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->orWhere('brand', 'like', "%{$query}%")
                ->orWhere('model', 'like', "%{$query}%")
                ->orWhere('serial_number', 'like', "%{$query}%");
        })
            ->limit(10)
            ->get();

        return $results;
    }

    /**
     * Handle maintenance completion and update asset status
     *
     * @param  HelpdeskTicket  $ticket  Helpdesk ticket
     * @param  array  $completionData  Completion data
     *
     * @see D03-FR-016.5 Maintenance completion
     */
    public function handleMaintenanceCompletion(HelpdeskTicket $ticket, array $completionData): void
    {
        if (! $ticket->asset_id) {
            throw new \Exception('Ticket is not associated with an asset');
        }

        $asset = Asset::findOrFail($ticket->asset_id);

        // Update ticket status
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $completionData['resolution_notes'] ?? null,
        ]);

        // Update asset condition and maintenance dates
        $asset->update([
            'condition' => \App\Enums\AssetCondition::from($completionData['asset_condition']),
            'last_maintenance_date' => now(),
            'next_maintenance_date' => $completionData['next_maintenance_date'] ?? now()->addMonths(6),
        ]);

        // Sync asset status
        $this->syncAssetStatus($asset->id);

        Log::info('Maintenance completed', [
            'ticket_number' => $ticket->ticket_number,
            'asset_tag' => $asset->asset_tag,
            'new_condition' => $completionData['asset_condition'],
        ]);
    }

    /**
     * Get unified analytics across loan and helpdesk modules
     *
     * @return array Unified analytics data
     *
     * @see D03-FR-004.1 Unified dashboard
     * @see D03-FR-013.1 Analytics integration
     */
    public function getUnifiedAnalytics(): array
    {
        return [
            'loan_metrics' => [
                'total_applications' => LoanApplication::count(),
                'active_loans' => LoanApplication::whereIn('status', ['issued', 'in_use'])->count(),
                'pending_approvals' => LoanApplication::where('status', 'under_review')->count(),
                'overdue_loans' => LoanApplication::where('status', 'overdue')->count(),
                'completed_loans' => LoanApplication::where('status', 'completed')->count(),
            ],
            'helpdesk_metrics' => [
                'total_tickets' => HelpdeskTicket::count(),
                'open_tickets' => HelpdeskTicket::whereIn('status', ['new', 'assigned', 'in_progress'])->count(),
                'maintenance_tickets' => HelpdeskTicket::whereHas('category', function ($q) {
                    $q->where('name', 'maintenance');
                })->count(),
                'asset_related_tickets' => HelpdeskTicket::whereNotNull('asset_id')->count(),
                'resolved_tickets' => HelpdeskTicket::where('status', 'resolved')->count(),
            ],
            'asset_metrics' => [
                'total_assets' => Asset::count(),
                'available_assets' => Asset::where('status', AssetStatus::AVAILABLE)->count(),
                'loaned_assets' => Asset::where('status', AssetStatus::LOANED)->count(),
                'maintenance_assets' => Asset::where('status', AssetStatus::MAINTENANCE)->count(),
                'retired_assets' => Asset::where('status', AssetStatus::RETIRED)->count(),
            ],
            'integration_metrics' => [
                'cross_module_links' => \App\Models\CrossModuleIntegration::count(),
                'automated_tickets' => \App\Models\CrossModuleIntegration::where('integration_type', 'asset_damage_report')->count(),
                'asset_ticket_links' => \App\Models\CrossModuleIntegration::where('integration_type', 'asset_ticket_link')->count(),
            ],
        ];
    }

    /**
     * Create helpdesk ticket for damaged asset (wrapper for tests)
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @param  array  $damageDetails  Damage details
     * @return HelpdeskTicket Created ticket
     */
    public function createTicketForDamagedAsset(LoanApplication $loanApplication, array $damageDetails): HelpdeskTicket
    {
        // Get asset from loan application
        $asset = $loanApplication->loanItems()->first()?->asset;

        if (!$asset) {
            throw new \Exception('Loan application has no associated assets');
        }

        // Use existing maintenance ticket creation method
        $ticket = $this->createMaintenanceTicket($asset, $loanApplication, $damageDetails);

        // Create cross-module integration record
        CrossModuleIntegration::create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $loanApplication->id,
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
            'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
            'integration_data' => [
                'asset_id' => $asset->id,
                'damage_type' => $damageDetails['type'] ?? null,
                'severity' => $damageDetails['severity'] ?? null,
                'description' => $damageDetails['description'] ?? null,
            ],
            'processed_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Get related tickets for a loan application
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @return \Illuminate\Database\Eloquent\Collection Related tickets
     */
    public function getRelatedTickets(LoanApplication $loanApplication)
    {
        $integrations = CrossModuleIntegration::where('loan_application_id', $loanApplication->id)
            ->with('helpdeskTicket')
            ->get();

        return $integrations->pluck('helpdeskTicket')->filter();
    }

    /**
     * Get related loans for a helpdesk ticket
     *
     * @param  HelpdeskTicket  $ticket  Helpdesk ticket
     * @return \Illuminate\Database\Eloquent\Collection Related loan applications
     */
    public function getRelatedLoans(HelpdeskTicket $ticket)
    {
        $integrations = CrossModuleIntegration::where('helpdesk_ticket_id', $ticket->id)
            ->with('assetLoan')
            ->get();

        return $integrations->pluck('assetLoan')->filter();
    }

    /**
     * Bulk link tickets to loan application
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $tickets  Tickets to link
     * @param  LoanApplication  $loanApplication  Loan application
     * @return array Results with success and failed counts
     */
    public function bulkLinkTicketsToLoan($tickets, LoanApplication $loanApplication): array
    {
        $success = 0;
        $failed = 0;

        foreach ($tickets as $ticket) {
            try {
                $this->linkTicketToLoan($ticket, $loanApplication);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to link ticket to loan', [
                    'ticket_id' => $ticket->id,
                    'loan_id' => $loanApplication->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
        ];
    }

    /**
     * Get specific integration record between loan and ticket
     *
     * @param  LoanApplication  $loanApplication  Loan application
     * @param  HelpdeskTicket  $ticket  Helpdesk ticket
     * @return CrossModuleIntegration|null Integration record
     */
    public function getIntegration(LoanApplication $loanApplication, HelpdeskTicket $ticket): ?CrossModuleIntegration
    {
        return CrossModuleIntegration::where('loan_application_id', $loanApplication->id)
            ->where('helpdesk_ticket_id', $ticket->id)
            ->first();
    }

    /**
     * Get integration statistics
     *
     * @return array Statistics about cross-module integrations
     */
    public function getIntegrationStatistics(): array
    {
        return [
            'total_integrations' => CrossModuleIntegration::count(),
            'damage_reports' => CrossModuleIntegration::where('integration_type', CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT)->count(),
            'related_issues' => CrossModuleIntegration::where('integration_type', CrossModuleIntegration::TYPE_ASSET_TICKET_LINK)->count(),
            'processed' => CrossModuleIntegration::whereNotNull('processed_at')->count(),
            'pending' => CrossModuleIntegration::whereNull('processed_at')->count(),
        ];
    }
}
