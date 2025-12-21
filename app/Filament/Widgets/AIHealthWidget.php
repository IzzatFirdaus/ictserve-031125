<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\AIMetricsCollector;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * AI Health Widget
 *
 * Displays system status monitoring for Ollama and AWS Bedrock services
 * including health status, error rates, and service availability.
 *
 * trace: D18-§4.1 (AI Health Monitoring), R21 (Cloud Hybrid AI Dashboard Integration)
 * trace: D04-§6.4 (AI Architecture), D11-§8.1 (System Monitoring)
 *
 * @see docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md
 */
class AIHealthWidget extends BaseWidget
{
    protected ?string $pollingInterval = '1m';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * Widget metadata for registry system
     */
    public static function getWidgetMetadata(): array
    {
        return [
            'category' => 'header',
            'sort_order' => 17,
            'roles' => ['admin', 'superuser'],
            'refresh_rate' => 60, // 1 minute
            'cache_ttl' => 120,
        ];
    }

    /**
     * Check if user can access this widget
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole(['admin', 'superuser']);
    }

    /**
     * Get widget stats
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        if (! self::canView()) {
            return [];
        }

        try {
            $metricsCollector = app(AIMetricsCollector::class);
            $healthMetrics = $metricsCollector->getHealthMetrics();

            return [
                $this->createOverallHealthStat($healthMetrics),
                $this->createOllamaHealthStat($healthMetrics['ollama'] ?? []),
                $this->createBedrockHealthStat($healthMetrics['bedrock'] ?? []),
                $this->createLastCheckStat($healthMetrics),
            ];
        } catch (\Exception $e) {
            return $this->getErrorStats();
        }
    }

    /**
     * Create overall health status stat
     *
     * @param  array<string, mixed>  $healthMetrics
     */
    private function createOverallHealthStat(array $healthMetrics): Stat
    {
        $overallStatus = $healthMetrics['overall_status'] ?? 'unknown';

        $color = match ($overallStatus) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };

        $icon = match ($overallStatus) {
            'healthy' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical' => 'heroicon-o-x-circle',
            default => 'heroicon-o-question-mark-circle',
        };

        $statusText = $this->translateHealthStatus($overallStatus);
        $description = $this->getOverallHealthDescription($healthMetrics);

        return Stat::make('Status Kesihatan AI', $statusText)
            ->description($description)
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-health-stat overall-health',
                'data-status' => $overallStatus,
                'aria-label' => "Status kesihatan keseluruhan AI: {$statusText}",
            ]);
    }

    /**
     * Create Ollama health stat
     *
     * @param  array<string, mixed>  $ollamaHealth
     */
    private function createOllamaHealthStat(array $ollamaHealth): Stat
    {
        $status = $ollamaHealth['status'] ?? 'unknown';
        $errorRate = $ollamaHealth['error_rate_percent'] ?? 0;
        $lastRequest = $ollamaHealth['last_request'] ?? 'Tidak diketahui';

        $color = match ($status) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };

        $icon = match ($status) {
            'healthy' => 'heroicon-o-server',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical' => 'heroicon-o-server-stack',
            default => 'heroicon-o-question-mark-circle',
        };

        $statusText = $this->translateHealthStatus($status);

        return Stat::make('Ollama (Tempatan)', $statusText)
            ->description("Kadar ralat: {$errorRate}% | Permintaan terakhir: {$lastRequest}")
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-health-stat ollama-health',
                'data-status' => $status,
                'data-error-rate' => $errorRate,
                'aria-label' => "Kesihatan Ollama: {$statusText}, kadar ralat {$errorRate}%",
            ]);
    }

    /**
     * Create Bedrock health stat
     *
     * @param  array<string, mixed>  $bedrockHealth
     */
    private function createBedrockHealthStat(array $bedrockHealth): Stat
    {
        $status = $bedrockHealth['status'] ?? 'unknown';
        $errorRate = $bedrockHealth['error_rate_percent'] ?? 0;
        $lastRequest = $bedrockHealth['last_request'] ?? 'Tidak diketahui';

        $color = match ($status) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };

        $icon = match ($status) {
            'healthy' => 'heroicon-o-cloud',
            'warning' => 'heroicon-o-exclamation-triangle',
            'critical' => 'heroicon-o-cloud-arrow-down',
            default => 'heroicon-o-question-mark-circle',
        };

        $statusText = $this->translateHealthStatus($status);

        return Stat::make('Bedrock (Awan)', $statusText)
            ->description("Kadar ralat: {$errorRate}% | Permintaan terakhir: {$lastRequest}")
            ->descriptionIcon($icon)
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-health-stat bedrock-health',
                'data-status' => $status,
                'data-error-rate' => $errorRate,
                'aria-label' => "Kesihatan Bedrock: {$statusText}, kadar ralat {$errorRate}%",
            ]);
    }

    /**
     * Create last health check stat
     *
     * @param  array<string, mixed>  $healthMetrics
     */
    private function createLastCheckStat(array $healthMetrics): Stat
    {
        $lastCheck = $healthMetrics['last_health_check'] ?? null;

        if ($lastCheck) {
            $checkTime = \Carbon\Carbon::parse($lastCheck);
            $timeAgo = $checkTime->diffForHumans();
            $formattedTime = $checkTime->format('H:i:s');
        } else {
            $timeAgo = 'Tidak diketahui';
            $formattedTime = 'Tidak tersedia';
        }

        // Determine freshness color
        $minutesAgo = $lastCheck ? \Carbon\Carbon::parse($lastCheck)->diffInMinutes() : 999;
        $color = match (true) {
            $minutesAgo <= 2 => 'success',
            $minutesAgo <= 5 => 'info',
            $minutesAgo <= 10 => 'warning',
            default => 'danger',
        };

        return Stat::make('Pemeriksaan Terakhir', $timeAgo)
            ->description("Masa: {$formattedTime}")
            ->descriptionIcon('heroicon-o-clock')
            ->color($color)
            ->extraAttributes([
                'class' => 'ai-health-stat last-check',
                'data-last-check' => $lastCheck,
                'aria-label' => "Pemeriksaan kesihatan terakhir: {$timeAgo}",
            ]);
    }

    /**
     * Get error stats when health collection fails
     *
     * @return array<Stat>
     */
    private function getErrorStats(): array
    {
        return [
            Stat::make('Kesihatan AI', 'Tidak Tersedia')
                ->description('Gagal mengumpul data kesihatan sistem')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'ai-health-stat ai-error-stat',
                    'aria-label' => 'Data kesihatan AI tidak tersedia kerana ralat',
                ]),
        ];
    }

    /**
     * Translate health status to Bahasa Melayu
     */
    private function translateHealthStatus(string $status): string
    {
        return match ($status) {
            'healthy' => 'Sihat',
            'warning' => 'Amaran',
            'critical' => 'Kritikal',
            'unknown' => 'Tidak Diketahui',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get overall health description
     *
     * @param  array<string, mixed>  $healthMetrics
     */
    private function getOverallHealthDescription(array $healthMetrics): string
    {
        $ollamaStatus = $healthMetrics['ollama']['status'] ?? 'unknown';
        $bedrockStatus = $healthMetrics['bedrock']['status'] ?? 'unknown';
        $overallStatus = $healthMetrics['overall_status'] ?? 'unknown';

        return match ($overallStatus) {
            'healthy' => 'Semua perkhidmatan AI berfungsi dengan normal',
            'warning' => 'Beberapa perkhidmatan mengalami masalah kecil',
            'critical' => 'Perkhidmatan AI mengalami masalah serius',
            default => 'Status perkhidmatan AI tidak dapat ditentukan',
        };
    }
}
