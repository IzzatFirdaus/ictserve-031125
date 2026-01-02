<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Jobs\RestartAIServiceJob;
use App\Services\AIMetricsCollector;
use Filament\Notifications\Notification;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

use function Spatie\Activitylog\activity;

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
 * @see Requirements 19.6
 */
class AIHealthWidget extends BaseWidget
{
    protected ?string $pollingInterval = '1m';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /**
     * @var array<string>
     */
    protected $listeners = ['refreshAIHealth' => '$refresh'];

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
     * Check if manual restart is allowed
     */
    public function canRestartServices(): bool
    {
        $user = Auth::user();

        // Only superusers can restart services
        if (! $user || ! $user->hasRole('superuser')) {
            return false;
        }

        // Check feature flag
        return (bool) config('ollama.admin.allow_manual_restart', false);
    }

    /**
     * Restart AI services
     */
    public function restartAIServices(string $service = 'all'): void
    {
        if (! $this->canRestartServices()) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak mempunyai kebenaran untuk memulakan semula perkhidmatan AI.')
                ->danger()
                ->send();

            return;
        }

        $user = Auth::user();

        // Dispatch the restart job
        RestartAIServiceJob::dispatch($service, $user?->id);

        // Create audit log entry
        $this->logRestartAction($service, $user?->id);

        Notification::make()
            ->title('Permintaan Dimulakan')
            ->body("Permintaan untuk memulakan semula perkhidmatan AI ({$this->translateService($service)}) telah dihantar.")
            ->success()
            ->send();

        // Refresh the widget
        $this->dispatch('refreshAIHealth');
    }

    /**
     * Log the restart action for audit purposes
     */
    private function logRestartAction(string $service, ?int $userId): void
    {
        activity()
            ->causedBy($userId)
            ->withProperties([
                'operation_type' => 'ai_service_restart',
                'service' => $service,
                'timestamp' => now()->toIso8601String(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("AI service restart requested: {$service}");
    }

    /**
     * Translate service name to Bahasa Melayu
     */
    private function translateService(string $service): string
    {
        return match ($service) {
            'ollama' => 'Ollama (Tempatan)',
            'bedrock' => 'Bedrock (Awan)',
            'all' => 'Semua Perkhidmatan',
            default => $service,
        };
    }

    /**
     * Get header actions for the widget
     *
     * @return array<\Filament\Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        if (! $this->canRestartServices()) {
            return [];
        }

        return [
            \Filament\Actions\Action::make('restartAll')
                ->label('Mulakan Semula Semua')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Mulakan Semula Perkhidmatan AI')
                ->modalDescription('Adakah anda pasti mahu memulakan semula semua perkhidmatan AI? Ini akan menyegarkan semula cache kesihatan dan melakukan pemeriksaan kesihatan baharu.')
                ->modalSubmitActionLabel('Ya, Mulakan Semula')
                ->action(fn () => $this->restartAIServices('all')),

            \Filament\Actions\Action::make('restartOllama')
                ->label('Mulakan Semula Ollama')
                ->icon('heroicon-o-server')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Mulakan Semula Ollama')
                ->modalDescription('Adakah anda pasti mahu memulakan semula perkhidmatan Ollama?')
                ->modalSubmitActionLabel('Ya, Mulakan Semula')
                ->action(fn () => $this->restartAIServices('ollama')),

            \Filament\Actions\Action::make('restartBedrock')
                ->label('Mulakan Semula Bedrock')
                ->icon('heroicon-o-cloud')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Mulakan Semula Bedrock')
                ->modalDescription('Adakah anda pasti mahu memulakan semula perkhidmatan Bedrock?')
                ->modalSubmitActionLabel('Ya, Mulakan Semula')
                ->action(fn () => $this->restartAIServices('bedrock')),
        ];
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

    /**
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

    /**
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

    /**
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

    /**
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

    /**
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
