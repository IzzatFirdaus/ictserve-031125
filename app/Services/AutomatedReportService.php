<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\Reports\ScheduledReportMail;
use App\Models\ReportSchedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AutomatedReportService
{
    public function __construct(
        private ReportBuilderService $reportBuilderService
    ) {}

    /**
     * Process all due report schedules
     */
    public function processDueReports(): array
    {
        $results = [
            'processed' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $dueSchedules = ReportSchedule::due()->get();

        foreach ($dueSchedules as $schedule) {
            try {
                $this->generateAndSendReport($schedule);
                $schedule->markAsExecuted();
                $results['processed']++;

                Log::info('Scheduled report generated successfully', [
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->name,
                    'module' => $schedule->module,
                ]);
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->name,
                    'error' => $e->getMessage(),
                ];

                Log::error('Failed to generate scheduled report', [
                    'schedule_id' => $schedule->id,
                    'schedule_name' => $schedule->name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Generate and send a specific report
     */
    public function generateAndSendReport(ReportSchedule $schedule): void
    {
        // Generate report using ReportBuilderService
        $reportData = $this->reportBuilderService->generateReport(
            $schedule->module,
            $schedule->filters ?? [],
            $schedule->format
        );

        // Create filename with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$schedule->name}_{$timestamp}.{$schedule->format}";

        // Store report file temporarily
        $filePath = "reports/scheduled/{$filename}";
        Storage::disk('local')->put($filePath, $reportData['content']);

        // Send email with report attachment
        foreach ($schedule->recipients as $recipient) {
            Mail::to($recipient)->send(
                new ScheduledReportMail($schedule, $filePath, $reportData['metadata'])
            );
        }

        // Clean up temporary file after 24 hours
        $this->scheduleFileCleanup($filePath);
    }

    /**
     * Generate system usage statistics for unified reports
     */
    public function generateSystemUsageStats(): array
    {
        return [
            'helpdesk_stats' => [
                'total_tickets' => \App\Models\HelpdeskTicket::count(),
                'open_tickets' => \App\Models\HelpdeskTicket::where('status', 'open')->count(),
                'resolved_this_month' => \App\Models\HelpdeskTicket::where('status', 'resolved')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
                'avg_resolution_time' => $this->calculateAverageResolutionTime(),
            ],
            'loan_stats' => [
                'total_applications' => \App\Models\LoanApplication::count(),
                'active_loans' => \App\Models\LoanApplication::where('status', 'issued')->count(),
                'overdue_returns' => \App\Models\LoanApplication::where('status', 'issued')
                    ->where('return_date', '<', now())
                    ->count(),
                'utilization_rate' => $this->calculateAssetUtilizationRate(),
            ],
            'asset_stats' => [
                'total_assets' => \App\Models\Asset::count(),
                'available_assets' => \App\Models\Asset::where('status', 'available')->count(),
                'maintenance_assets' => \App\Models\Asset::where('status', 'maintenance')->count(),
                'most_requested' => $this->getMostRequestedAssets(5),
            ],
            'sla_compliance' => [
                'helpdesk_sla' => $this->calculateHelpdeskSLACompliance(),
                'loan_approval_sla' => $this->calculateLoanApprovalSLACompliance(),
            ],
        ];
    }

    /**
     * Calculate average resolution time for helpdesk tickets
     */
    private function calculateAverageResolutionTime(): float
    {
        $resolvedTickets = \App\Models\HelpdeskTicket::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return 0;
        }

        $totalHours = $resolvedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        return round($totalHours / $resolvedTickets->count(), 2);
    }

    /**
     * Calculate asset utilization rate
     */
    private function calculateAssetUtilizationRate(): float
    {
        $totalAssets = \App\Models\Asset::where('status', '!=', 'retired')->count();
        $loanedAssets = \App\Models\Asset::where('status', 'on_loan')->count();

        return $totalAssets > 0 ? round(($loanedAssets / $totalAssets) * 100, 2) : 0;
    }

    /**
     * Get most requested assets
     */
    private function getMostRequestedAssets(int $limit): array
    {
        return \App\Models\Asset::withCount('loanApplications')
            ->orderBy('loan_applications_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($asset) {
                return [
                    'name' => $asset->name,
                    'asset_code' => $asset->asset_code,
                    'request_count' => $asset->loan_applications_count,
                ];
            })
            ->toArray();
    }

    /**
     * Calculate helpdesk SLA compliance
     */
    private function calculateHelpdeskSLACompliance(): float
    {
        $totalTickets = \App\Models\HelpdeskTicket::whereNotNull('sla_deadline')->count();
        $compliantTickets = \App\Models\HelpdeskTicket::whereNotNull('sla_deadline')
            ->where(function ($query) {
                $query->where('status', 'resolved')
                    ->whereColumn('resolved_at', '<=', 'sla_deadline');
            })
            ->count();

        return $totalTickets > 0 ? round(($compliantTickets / $totalTickets) * 100, 2) : 100;
    }

    /**
     * Calculate loan approval SLA compliance
     */
    private function calculateLoanApprovalSLACompliance(): float
    {
        $totalApplications = \App\Models\LoanApplication::whereIn('status', ['approved', 'rejected'])->count();
        $compliantApplications = \App\Models\LoanApplication::whereIn('status', ['approved', 'rejected'])
            ->where('updated_at', '<=', \DB::raw('DATE_ADD(created_at, INTERVAL 48 HOUR)'))
            ->count();

        return $totalApplications > 0 ? round(($compliantApplications / $totalApplications) * 100, 2) : 100;
    }

    /**
     * Schedule file cleanup after 24 hours
     */
    private function scheduleFileCleanup(string $filePath): void
    {
        // This would typically use a job queue in production
        // For now, we'll just log the cleanup requirement
        Log::info('Report file scheduled for cleanup', [
            'file_path' => $filePath,
            'cleanup_at' => now()->addDay(),
        ]);
    }

    /**
     * Get overdue analysis data
     */
    public function getOverdueAnalysis(): array
    {
        return [
            'overdue_tickets' => \App\Models\HelpdeskTicket::where('sla_deadline', '<', now())
                ->where('status', '!=', 'resolved')
                ->count(),
            'overdue_loans' => \App\Models\LoanApplication::where('status', 'issued')
                ->where('return_date', '<', now())
                ->count(),
            'pending_approvals' => \App\Models\LoanApplication::where('status', 'pending_approval')
                ->where('created_at', '<', now()->subHours(48))
                ->count(),
        ];
    }
}
