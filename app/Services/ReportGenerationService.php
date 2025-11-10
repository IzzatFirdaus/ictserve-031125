<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\LoanApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Report Generation Service
 *
 * Handles automated report generation for loan and asset analytics.
 *
 * @trace D03-FR-013.2 (Automated Report Generation)
 */
class ReportGenerationService
{
    /**
     * Generate loan statistics for specified period.
     *
     * @param string $period Period for statistics ('daily', 'weekly', 'monthly')
     * @return array Statistics including period, counts, and approval rate
     */
    public function generateLoanStatistics(string $period = 'monthly'): array
    {
        $startDate = match ($period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfMonth(),
        };

        $total = LoanApplication::where('created_at', '>=', $startDate)->count();
        $approved = LoanApplication::where('created_at', '>=', $startDate)
            ->where('status', 'approved')->count();
        $rejected = LoanApplication::where('created_at', '>=', $startDate)
            ->where('status', 'rejected')->count();
        $pending = LoanApplication::where('created_at', '>=', $startDate)
            ->whereIn('status', ['submitted', 'under_review'])->count();

        return [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => now()->toDateString(),
            'total_applications' => $total,
            'approved_count' => $approved,
            'rejected_count' => $rejected,
            'pending_count' => $pending,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0.0,
        ];
    }
    public function generateLoanStatisticsReport(string $period = 'monthly'): array
    {
        $startDate = match ($period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->startOfMonth(),
        };

        return [
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => now()->toDateString(),
            'total_applications' => LoanApplication::where('created_at', '>=', $startDate)->count(),
            'approved_applications' => LoanApplication::where('created_at', '>=', $startDate)
                ->where('status', 'approved')->count(),
            'rejected_applications' => LoanApplication::where('created_at', '>=', $startDate)
                ->where('status', 'rejected')->count(),
            'pending_applications' => LoanApplication::where('created_at', '>=', $startDate)
                ->whereIn('status', ['submitted', 'under_review'])->count(),
            'average_approval_time' => $this->calculateAverageApprovalTime($startDate),
        ];
    }

    public function generateAssetUtilizationReport(): array
    {
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'available')->count();
        $loanedAssets = Asset::where('status', 'loaned')->count();
        $maintenanceAssets = Asset::where('status', 'maintenance')->count();

        return [
            'total_assets' => $totalAssets,
            'available_assets' => $availableAssets,
            'loaned_assets' => $loanedAssets,
            'maintenance_assets' => $maintenanceAssets,
            'utilization_rate' => $totalAssets > 0 ? round(($loanedAssets / $totalAssets) * 100, 2) : 0,
            'availability_rate' => $totalAssets > 0 ? round(($availableAssets / $totalAssets) * 100, 2) : 0,
            'top_loaned_assets' => $this->getTopLoanedAssets(),
        ];
    }

    public function generateOverdueReport(): Collection
    {
        return LoanApplication::query()
            ->where('status', 'in_use')
            ->where('loan_end_date', '<', now())
            ->with(['user', 'division', 'loanItems.asset'])
            ->get()
            ->map(fn($app) => [
                'application_number' => $app->application_number,
                'applicant_name' => $app->applicant_name,
                'division' => $app->division?->name,
                'loan_end_date' => $app->loan_end_date?->toDateString(),
                'days_overdue' => now()->diffInDays($app->loan_end_date, false),
                'total_items' => $app->loanItems->count(),
            ]);
    }

    protected function calculateAverageApprovalTime(\DateTimeInterface $startDate): ?float
    {
        $approvedApplications = LoanApplication::query()
            ->where('created_at', '>=', $startDate)
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->get();

        if ($approvedApplications->isEmpty()) {
            return null;
        }

        $totalHours = $approvedApplications->sum(function ($app) {
            return $app->created_at->diffInHours($app->approved_at);
        });

        return round($totalHours / $approvedApplications->count(), 2);
    }

    protected function getTopLoanedAssets(int $limit = 10): Collection
    {
        return Asset::query()
            ->withCount('loanItems')
            ->orderByDesc('loan_items_count')
            ->limit($limit)
            ->get()
            ->map(fn($asset) => [
                'name' => $asset->name,
                'asset_tag' => $asset->asset_tag,
                'loan_count' => $asset->loan_items_count,
            ]);
    }
}
