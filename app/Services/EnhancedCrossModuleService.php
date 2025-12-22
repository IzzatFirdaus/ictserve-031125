<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\CrossModuleIntegration;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Enhanced Cross-Module Integration Service
 *
 * Provides enhanced integration between helpdesk and asset loan modules
 * with real-time synchronization, intelligent routing, and predictive analytics.
 *
 * Features:
 * - Automatic ticket creation for damaged assets within 5 seconds
 * - Real-time asset-ticket linking with relationship tracking
 * - Unified search with advanced filtering and real-time indexing
 * - Cross-module reporting with predictive analytics
 * - Real-time synchronization with conflict resolution
 *
 * @see D03-FR-016 Cross-module integration requirements
 * @see D04 §6.2 Cross-module integration service
 * @see D12 §2 Real-time features with Laravel Reverb
 *
 * @requirements 9.4, 10.1, 10.2, 14.3
 *
 * @version 3.6.0
 */
class EnhancedCrossModuleService
{
    private const CACHE_TTL = 300;

    public function __construct(
        private CrossModuleIntegrationService $baseService,
        private UnifiedAnalyticsService $analyticsService
    ) {}

    /**
     * Create automatic maintenance ticket for damaged asset
     * Must complete within 5 seconds per requirements
     */
    

/**
 * @param array<string, mixed> $damageData
 */
public function createAutomaticMaintenanceTicket(
        Asset $asset,
        LoanApplication $application,
        array $damageData
    ): HelpdeskTicket {
        $startTime = microtime(true);

        try {
            DB::beginTransaction();

            // Create ticket using base service
            $ticket = $this->baseService->createMaintenanceTicket($asset, $application, $damageData);

            // Create integration record
            CrossModuleIntegration::create([
                'helpdesk_ticket_id' => $ticket->id,
                'loan_application_id' => $application->id,
                'integration_type' => CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT,
                'trigger_event' => CrossModuleIntegration::EVENT_ASSET_RETURNED_DAMAGED,
                'integration_data' => [
                    'asset_id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'damage_report' => $damageData['damage_report'] ?? null,
                    'processing_time_ms' => (microtime(true) - $startTime) * 1000,
                    'automated' => true,
                ],
                'processed_at' => now(),
            ]);

            DB::commit();

            // Note: Real-time broadcasting handled by ticket creation observer

            // Clear related caches
            $this->clearIntegrationCaches($asset->id, $application->id);

            $processingTime = (microtime(true) - $startTime) * 1000;
            Log::info('Automatic maintenance ticket created', [
                'ticket_number' => $ticket->ticket_number,
                'asset_tag' => $asset->asset_tag,
                'processing_time_ms' => $processingTime,
                'within_sla' => $processingTime < 5000,
            ]);

            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create automatic maintenance ticket', [
                'asset_id' => $asset->id,
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Link asset to helpdesk ticket with comprehensive relationship tracking
     */
    public function linkAssetToTicket(Asset $asset, HelpdeskTicket $ticket): CrossModuleIntegration
    {
        // Check for existing link
        $existingLink = CrossModuleIntegration::where('helpdesk_ticket_id', $ticket->id)
            ->whereJsonContains('integration_data->asset_id', $asset->id)
            ->first();

        if ($existingLink) {
            return $existingLink;
        }

        // Find related loan application if any
        $relatedLoan = $asset->loanItems()
            ->whereHas('loanApplication', fn ($q) => $q->whereIn('status', ['issued', 'in_use']))
            ->first()
            ?->loanApplication;

        $integration = CrossModuleIntegration::create([
            'helpdesk_ticket_id' => $ticket->id,
            'loan_application_id' => $relatedLoan?->id,
            'integration_type' => CrossModuleIntegration::TYPE_ASSET_TICKET_LINK,
            'trigger_event' => CrossModuleIntegration::EVENT_TICKET_ASSET_SELECTED,
            'integration_data' => [
                'asset_id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'asset_status' => $asset->status->value,
                'ticket_category' => $ticket->category?->name_ms,
                'linked_at' => now()->toIso8601String(),
            ],
            'processed_at' => now(),
        ]);

        // Update ticket with asset reference
        $ticket->update(['asset_id' => $asset->id]);

        // Clear caches
        $this->clearIntegrationCaches($asset->id);

        return $integration;
    }

    /**
     * Unified search across both modules with advanced filtering
     *
     * @return array<string, mixed>
     */
    

/**
 * @param array<string, mixed> $filters
 */
public function unifiedSearch(string $query, array $filters = []): array
    {
        $cacheKey = 'unified_search:'.md5($query.serialize($filters));

        return Cache::remember($cacheKey, 60, function () use ($query, $filters) {
            $results = $this->baseService->unifiedSearch($query);

            // Apply additional filters
            if (! empty($filters['status'])) {
                $results['loan_applications'] = $results['loan_applications']
                    ->filter(fn ($app) => $app->status->value === $filters['status']);
                $results['helpdesk_tickets'] = $results['helpdesk_tickets']
                    ->filter(fn ($ticket) => $ticket->status === $filters['status']);
            }

            if (! empty($filters['date_from'])) {
                $dateFrom = \Carbon\Carbon::parse($filters['date_from']);
                $results['loan_applications'] = $results['loan_applications']
                    ->filter(fn ($app) => $app->created_at >= $dateFrom);
                $results['helpdesk_tickets'] = $results['helpdesk_tickets']
                    ->filter(fn ($ticket) => $ticket->created_at >= $dateFrom);
            }

            // Add relevance scoring
            $results['total_count'] = count($results['loan_applications']) +
                count($results['helpdesk_tickets']) +
                count($results['assets']);

            return $results;
        });
    }

    /**
     * Get cross-module reporting with predictive analytics
     *
     * @return array<string, mixed>
     */
    public function getCrossModuleReport(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $cacheKey = $this->buildCacheKey('cross_module_report', $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($startDate, $endDate) {
            $baseMetrics = $this->analyticsService->getDashboardMetrics($startDate, $endDate);
            $integrationMetrics = $this->getIntegrationMetrics($startDate, $endDate);
            $predictions = $this->getPredictiveInsights();

            return [
                'summary' => $baseMetrics['summary'],
                'helpdesk' => $baseMetrics['helpdesk'],
                'loans' => $baseMetrics['loans'],
                'assets' => $baseMetrics['assets'],
                'integration' => $integrationMetrics,
                'predictions' => $predictions,
                'generated_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Get integration-specific metrics
     *
     * @return array<string, mixed>
     */
    private function getIntegrationMetrics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $query = CrossModuleIntegration::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $integrations = $query->get();

        return [
            'total_integrations' => $integrations->count(),
            'damage_reports' => $integrations->where('integration_type', CrossModuleIntegration::TYPE_ASSET_DAMAGE_REPORT)->count(),
            'maintenance_requests' => $integrations->where('integration_type', CrossModuleIntegration::TYPE_MAINTENANCE_REQUEST)->count(),
            'asset_ticket_links' => $integrations->where('integration_type', CrossModuleIntegration::TYPE_ASSET_TICKET_LINK)->count(),
            'avg_processing_time_ms' => $integrations->avg(fn ($i) => $i->integration_data['processing_time_ms'] ?? 0) ?? 0,
            'automated_percentage' => $integrations->count() > 0
                ? round(($integrations->filter(fn ($i) => $i->integration_data['automated'] ?? false)->count() / $integrations->count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Get predictive insights based on historical data
     *
     * @return array<string, mixed>
     */
    private function getPredictiveInsights(): array
    {
        // Calculate maintenance prediction based on asset usage patterns
        $assetsNeedingMaintenance = Asset::where('next_maintenance_date', '<=', now()->addDays(30))
            ->count();

        // Calculate loan demand prediction based on historical patterns
        $avgMonthlyLoans = LoanApplication::where('created_at', '>=', now()->subMonths(6))
            ->count() / 6;

        // Calculate ticket volume prediction
        $avgMonthlyTickets = HelpdeskTicket::where('created_at', '>=', now()->subMonths(6))
            ->count() / 6;

        return [
            'maintenance_due_30_days' => $assetsNeedingMaintenance,
            'predicted_monthly_loans' => round($avgMonthlyLoans),
            'predicted_monthly_tickets' => round($avgMonthlyTickets),
            'high_risk_assets' => $this->getHighRiskAssets(),
            'recommendations' => $this->generateRecommendations($assetsNeedingMaintenance, $avgMonthlyLoans),
        ];
    }

    /**
     * Get high-risk assets that may need attention
     *
     * @return array<int, array<string, mixed>>
     */
    private function getHighRiskAssets(): array
    {
        return Asset::where(function ($query) {
            $query->where('maintenance_tickets_count', '>=', 3)
                ->orWhere('next_maintenance_date', '<', now())
                ->orWhereIn('condition', ['poor', 'damaged']);
        })
            ->limit(10)
            ->get()
            ->map(fn ($asset) => [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'name' => $asset->name,
                'condition' => $asset->condition->value ?? $asset->condition,
                'maintenance_count' => $asset->maintenance_tickets_count,
                'risk_level' => $this->calculateRiskLevel($asset),
            ])
            ->toArray();
    }

    /**
     * Calculate risk level for an asset
     */
    private function calculateRiskLevel(Asset $asset): string
    {
        $score = 0;

        if ($asset->maintenance_tickets_count >= 5) {
            $score += 3;
        } elseif ($asset->maintenance_tickets_count >= 3) {
            $score += 2;
        }

        if ($asset->next_maintenance_date && $asset->next_maintenance_date < now()) {
            $score += 2;
        }

        $condition = $asset->condition->value ?? $asset->condition;
        if (in_array($condition, ['damaged', 'poor'])) {
            $score += 3;
        }

        return match (true) {
            $score >= 6 => 'critical',
            $score >= 4 => 'high',
            $score >= 2 => 'medium',
            default => 'low',
        };
    }

    /**
     * Generate recommendations based on metrics
     *
     * @return array<int, string>
     */
    private function generateRecommendations(int $maintenanceDue, float $avgLoans): array
    {
        $recommendations = [];

        if ($maintenanceDue > 10) {
            $recommendations[] = "Terdapat {$maintenanceDue} aset memerlukan penyelenggaraan dalam 30 hari akan datang. Sila jadualkan penyelenggaraan.";
        }

        if ($avgLoans > 50) {
            $recommendations[] = 'Permintaan pinjaman tinggi. Pertimbangkan untuk menambah inventori aset.';
        }

        $overdueLoans = LoanApplication::where('loan_end_date', '<', now()->toDateString())
            ->whereIn('status', ['issued', 'in_use'])
            ->count();

        if ($overdueLoans > 5) {
            $recommendations[] = "Terdapat {$overdueLoans} pinjaman tertunggak. Sila hantar peringatan kepada peminjam.";
        }

        return $recommendations;
    }

    /**
     * Clear integration-related caches
     */
    private function clearIntegrationCaches(?int $assetId = null, ?int $applicationId = null): void
    {
        Cache::forget('cross_module_report');
        Cache::forget('unified_analytics:dashboard_metrics');

        if ($assetId) {
            Cache::forget("asset_history_{$assetId}");
            Cache::forget("asset_maintenance_stats_{$assetId}");
        }

        if ($applicationId) {
            Cache::forget("loan_integrations_{$applicationId}");
        }
    }

    /**
     * Build cache key with date parameters
     */
    private function buildCacheKey(string $prefix, ?\DateTime $startDate = null, ?\DateTime $endDate = null): string
    {
        $key = "enhanced_integration:{$prefix}";

        if ($startDate) {
            $key .= ':start_'.$startDate->format('Y-m-d');
        }
        if ($endDate) {
            $key .= ':end_'.$endDate->format('Y-m-d');
        }

        return $key;
    }
}
