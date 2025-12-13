<?php

declare(strict_types=1);

namespace App\Livewire\Ollama;

use App\Models\MessageLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * AI Dashboard Component
 *
 * Dashboard AI untuk pengguna authenticated dengan sejarah perbualan
 * dan statistik penggunaan. Mematuhi D00-D17 v3.6.0.
 *
 * @version 3.6.0
 *
 * @see D00_SYSTEM_OVERVIEW.md (True Hybrid Architecture)
 *
 * @requirements 1.7, 4.1, 4.5
 */
#[Layout('layouts.app')]
class AIDashboard extends Component
{
    use WithPagination;

    /**
     * Selected time period for statistics
     */
    public string $timePeriod = '7d';

    /**
     * Show detailed logs
     */
    public bool $showDetailedLogs = false;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Ensure user is authenticated
        if (! Auth::check()) {
            abort(403, 'Akses ditolak. Sila log masuk terlebih dahulu.');
        }
    }

    /**
     * Change time period for statistics
     */
    public function changeTimePeriod(string $period): void
    {
        $this->timePeriod = $period;
        $this->resetPage();
    }

    /**
     * Toggle detailed logs view
     */
    public function toggleDetailedLogs(): void
    {
        $this->showDetailedLogs = ! $this->showDetailedLogs;
    }

    /**
     * Export user data (PDPA compliance)
     */
    public function exportUserData(): void
    {
        $this->dispatch('redirect', url: route('staff.data-rights.export'));
    }

    /**
     * Get AI usage statistics for current user
     */
    #[Computed]
    public function aiUsageStats(): array
    {
        $userId = Auth::id();
        $days = match ($this->timePeriod) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 7,
        };

        $startDate = now()->subDays($days);

        $stats = MessageLog::where('user_id', $userId)
            ->where('processed_at', '>=', $startDate)
            ->selectRaw('
                operation_type,
                COUNT(*) as total_queries,
                AVG(JSON_EXTRACT(metadata, "$.processing_time")) as avg_processing_time,
                AVG(JSON_EXTRACT(metadata, "$.confidence")) as avg_confidence
            ')
            ->groupBy('operation_type')
            ->get();

        $totalQueries = $stats->sum('total_queries');
        $avgProcessingTime = $stats->avg('avg_processing_time') ?? 0;
        $avgConfidence = $stats->avg('avg_confidence') ?? 0;

        return [
            'total_queries' => $totalQueries,
            'avg_processing_time' => round($avgProcessingTime, 2),
            'avg_confidence' => round($avgConfidence * 100, 1), // Convert to percentage
            'by_operation' => $stats->keyBy('operation_type')->toArray(),
            'period_days' => $days,
        ];
    }

    /**
     * Get recent AI interactions
     */
    #[Computed]
    public function recentInteractions()
    {
        return MessageLog::where('user_id', Auth::id())
            ->orderBy('processed_at', 'desc')
            ->paginate(10);
    }

    /**
     * Get conversation history summary
     */
    #[Computed]
    public function conversationSummary(): array
    {
        $userId = Auth::id();

        // Get FAQ queries from last 30 days
        $faqQueries = MessageLog::where('user_id', $userId)
            ->where('operation_type', 'faq_query')
            ->where('processed_at', '>=', now()->subDays(30))
            ->orderBy('processed_at', 'desc')
            ->limit(5)
            ->get();

        // Get document analysis from last 30 days
        $documentAnalysis = MessageLog::where('user_id', $userId)
            ->where('operation_type', 'document_analysis')
            ->where('processed_at', '>=', now()->subDays(30))
            ->orderBy('processed_at', 'desc')
            ->limit(3)
            ->get();

        return [
            'recent_faq_queries' => $faqQueries,
            'recent_document_analysis' => $documentAnalysis,
            'total_interactions_30d' => MessageLog::where('user_id', $userId)
                ->where('processed_at', '>=', now()->subDays(30))
                ->count(),
        ];
    }

    /**
     * Get AI performance metrics for user
     */
    #[Computed]
    public function performanceMetrics(): array
    {
        $userId = Auth::id();

        $metrics = MessageLog::where('user_id', $userId)
            ->where('processed_at', '>=', now()->subDays(7))
            ->selectRaw('
                AVG(JSON_EXTRACT(metadata, "$.processing_time")) as avg_response_time,
                MAX(JSON_EXTRACT(metadata, "$.processing_time")) as max_response_time,
                MIN(JSON_EXTRACT(metadata, "$.processing_time")) as min_response_time,
                COUNT(CASE WHEN JSON_EXTRACT(metadata, "$.processing_time") > 5 THEN 1 END) as slow_queries,
                COUNT(*) as total_queries
            ')
            ->first();

        $slowQueryPercentage = $metrics->total_queries > 0
            ? ($metrics->slow_queries / $metrics->total_queries) * 100
            : 0;

        return [
            'avg_response_time' => round($metrics->avg_response_time ?? 0, 2),
            'max_response_time' => round($metrics->max_response_time ?? 0, 2),
            'min_response_time' => round($metrics->min_response_time ?? 0, 2),
            'slow_query_percentage' => round($slowQueryPercentage, 1),
            'total_queries' => $metrics->total_queries ?? 0,
        ];
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        return view('livewire.ollama.ai-dashboard');
    }
}
