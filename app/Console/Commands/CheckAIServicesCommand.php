<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AIHealthChecker;
use Illuminate\Console\Command;

/**
 * Check AI Services Command
 *
 * Displays health status of AI services (Ollama and Bedrock).
 * Can force refresh cached health data.
 *
 * @see D03-FR-019 AI service health monitoring
 * @see Requirements 19.1, 20.1
 */
class CheckAIServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-ai-services
                            {--refresh : Muat semula cache status kesihatan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memaparkan status kesihatan perkhidmatan AI (Ollama dan Bedrock)';

    public function __construct(
        private readonly AIHealthChecker $aiHealthChecker
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $refresh = $this->option('refresh');

        $this->info('🤖 Memeriksa status perkhidmatan AI...');
        $this->newLine();

        if ($refresh) {
            $this->aiHealthChecker->forceRefresh();
            $this->info('🔄 Cache dikosongkan, memeriksa semula...');
            $this->newLine();
        }

        // Get health status
        $overallHealth = $this->aiHealthChecker->getOverallHealth();

        // Display Ollama status
        $this->displayServiceStatus('Ollama', $overallHealth['ollama'] ?? []);

        // Display Bedrock status
        $this->displayServiceStatus('AWS Bedrock', $overallHealth['bedrock'] ?? []);

        // Display overall status
        $this->newLine();
        $overallStatus = $overallHealth['overall_status'] ?? 'unknown';
        $overallEmoji = $this->getStatusEmoji($overallStatus);
        $overallLabel = $this->getStatusLabel($overallStatus);

        $this->info("📊 Status Keseluruhan: {$overallEmoji} {$overallLabel}");
        $this->info("🕐 Semakan Terakhir: {$overallHealth['last_check']}");

        // Return appropriate exit code
        return match ($overallStatus) {
            'healthy' => self::SUCCESS,
            'warning' => self::SUCCESS,
            'critical' => self::FAILURE,
            default => self::SUCCESS,
        };
    }

    /**
     * Display status for a single service.
     *
     * @param  array<string, mixed>  $health
     */
    private function displayServiceStatus(string $serviceName, array $health): void
    {
        $status = $health['status'] ?? 'unknown';
        $emoji = $this->getStatusEmoji($status);
        $label = $this->getStatusLabel($status);
        $message = $health['message'] ?? 'Tiada maklumat';

        $this->line('┌─────────────────────────────────────────────────────────────┐');
        $this->line("│ {$emoji} {$serviceName}");
        $this->line('├─────────────────────────────────────────────────────────────┤');
        $this->line("│ Status: {$label}");
        $this->line("│ Mesej: {$message}");

        // Display additional metrics if available
        if (isset($health['models_available'])) {
            $this->line("│ Model Tersedia: {$health['models_available']}");
        }

        if (isset($health['response_time_ms'])) {
            $responseTime = round($health['response_time_ms'], 2);
            $this->line("│ Masa Respons: {$responseTime}ms");
        }

        if (isset($health['region'])) {
            $this->line("│ Rantau AWS: {$health['region']}");
        }

        if (isset($health['error_code'])) {
            $this->line("│ Kod Ralat: {$health['error_code']}");
        }

        $this->line("│ Semakan: {$health['last_check']}");
        $this->line('└─────────────────────────────────────────────────────────────┘');
        $this->newLine();
    }

    /**
     * Get emoji for status.
     */
    private function getStatusEmoji(string $status): string
    {
        return match ($status) {
            'healthy' => '✅',
            'warning' => '⚠️',
            'critical' => '❌',
            'not_configured' => '⚙️',
            default => '❓',
        };
    }

    /**
     * Get label for status in Bahasa Melayu.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'healthy' => 'Sihat',
            'warning' => 'Amaran',
            'critical' => 'Kritikal',
            'not_configured' => 'Tidak Dikonfigurasi',
            default => 'Tidak Diketahui',
        };
    }
}
