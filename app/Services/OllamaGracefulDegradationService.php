<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemDegradationNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Perkhidmatan Degradasi Anggun Ollama untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menguruskan strategi degradasi berbilang peringkat:
 * - Tier 1: Operasi penuh dengan semua ciri
 * - Tier 2: Ciri dikurangkan (tiada embedding baru)
 * - Tier 3: Respons cache sahaja
 * - Tier 4: Mod kecemasan (mesej statik)
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 Technical Design Documentation v3.6.0, D16 Laravel Reverb
 *
 * @requirements 8.3
 */
class OllamaGracefulDegradationService
{
    public const TIER_FULL = 1;

    public const TIER_REDUCED = 2;

    public const TIER_CACHE_ONLY = 3;

    public const TIER_EMERGENCY = 4;

    private const TIER_CACHE_KEY = 'ollama:degradation:tier';

    private const HISTORY_CACHE_KEY = 'ollama:degradation:history';

    private const THRESHOLDS = [
        'cpu_high' => 80,
        'cpu_critical' => 95,
        'memory_high' => 90,
        'memory_critical' => 98,
        'response_time_high' => 5000,
        'response_time_critical' => 10000,
        'error_rate_high' => 10,
        'error_rate_critical' => 25,
    ];

    private array $config;

    public function __construct(
        private readonly OllamaCacheService $cacheService,
        private readonly OllamaModelOptimizationService $modelService
    ) {
        $this->config = config('ollama.degradation', []);
    }

    /**
     * Dapatkan tier degradasi semasa
     * Get current degradation tier
     */
    public function getCurrentTier(): int
    {
        return (int) Cache::get(self::TIER_CACHE_KEY, self::TIER_FULL);
    }

    /**
     * Tetapkan tier degradasi
     * Set degradation tier
     */
    public function setTier(int $tier, string $reason = ''): bool
    {
        $previousTier = $this->getCurrentTier();

        if ($tier < self::TIER_FULL || $tier > self::TIER_EMERGENCY) {
            return false;
        }

        Cache::put(self::TIER_CACHE_KEY, $tier, 3600);

        $this->recordTierChange($previousTier, $tier, $reason);

        if ($tier > $previousTier) {
            $this->notifyAdmins($tier, $reason);
        }

        Log::warning('Ollama degradation tier changed', [
            'previous_tier' => $previousTier,
            'new_tier' => $tier,
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * Semak dan kemas kini tier berdasarkan metrik semasa
     * Check and update tier based on current metrics
     */
    

/**
 * @return array<string, mixed>
 */
public function evaluateAndAdjustTier(): array
    {
        $metrics = $this->collectMetrics();
        $recommendedTier = $this->calculateRecommendedTier($metrics);
        $currentTier = $this->getCurrentTier();

        $result = [
            'current_tier' => $currentTier,
            'recommended_tier' => $recommendedTier,
            'metrics' => $metrics,
            'action_taken' => 'none',
        ];

        if ($recommendedTier !== $currentTier) {
            $reason = $this->generateTierChangeReason($metrics, $recommendedTier);
            $this->setTier($recommendedTier, $reason);
            $result['action_taken'] = $recommendedTier > $currentTier ? 'degraded' : 'recovered';
        }

        return $result;
    }

    /**
     * Kumpul metrik untuk penilaian
     * Collect metrics for evaluation
     */
    

/**
 * @return array<string, mixed>
 */
protected function collectMetrics(): array
    {
        $modelHealth = $this->modelService->getModelHealth();
        $cacheStats = $this->cacheService->getStats();
        $performanceMetrics = $this->modelService->getPerformanceMetrics();

        return [
            'cpu_percent' => $this->getCpuUsage(),
            'memory_percent' => $this->getMemoryUsage(),
            'response_time_avg' => $performanceMetrics['current']['avg'] ?? 0,
            'error_rate' => $this->getErrorRate(),
            'cache_hit_rate' => $cacheStats['hit_rate'] ?? 0,
            'server_available' => $modelHealth['server_available'] ?? false,
            'model_warmed_up' => $modelHealth['warmed_up'] ?? false,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Kira tier yang disyorkan berdasarkan metrik
     * Calculate recommended tier based on metrics
     */
    protected function calculateRecommendedTier(array $metrics): int
    {
        if (! $metrics['server_available']) {
            return self::TIER_EMERGENCY;
        }

        if (
            $metrics['cpu_percent'] >= self::THRESHOLDS['cpu_critical'] ||
            $metrics['memory_percent'] >= self::THRESHOLDS['memory_critical'] ||
            $metrics['error_rate'] >= self::THRESHOLDS['error_rate_critical']
        ) {
            return self::TIER_CACHE_ONLY;
        }

        if (
            $metrics['cpu_percent'] >= self::THRESHOLDS['cpu_high'] ||
            $metrics['memory_percent'] >= self::THRESHOLDS['memory_high'] ||
            $metrics['response_time_avg'] >= self::THRESHOLDS['response_time_high'] ||
            $metrics['error_rate'] >= self::THRESHOLDS['error_rate_high']
        ) {
            return self::TIER_REDUCED;
        }

        return self::TIER_FULL;
    }

    /**
     * Jana sebab perubahan tier
     * Generate tier change reason
     */
    protected function generateTierChangeReason(array $metrics, int $newTier): string
    {
        $reasons = [];

        if (! $metrics['server_available']) {
            $reasons[] = 'Pelayan Ollama tidak tersedia';
        }

        if ($metrics['cpu_percent'] >= self::THRESHOLDS['cpu_high']) {
            $reasons[] = sprintf('Penggunaan CPU tinggi: %.1f%%', $metrics['cpu_percent']);
        }

        if ($metrics['memory_percent'] >= self::THRESHOLDS['memory_high']) {
            $reasons[] = sprintf('Penggunaan memori tinggi: %.1f%%', $metrics['memory_percent']);
        }

        if ($metrics['response_time_avg'] >= self::THRESHOLDS['response_time_high']) {
            $reasons[] = sprintf('Masa respons tinggi: %.0fms', $metrics['response_time_avg']);
        }

        if ($metrics['error_rate'] >= self::THRESHOLDS['error_rate_high']) {
            $reasons[] = sprintf('Kadar ralat tinggi: %.1f%%', $metrics['error_rate']);
        }

        if (empty($reasons) && $newTier === self::TIER_FULL) {
            $reasons[] = 'Sistem telah pulih ke operasi normal';
        }

        return implode('; ', $reasons);
    }

    /**
     * Dapatkan respons berdasarkan tier semasa
     * Get response based on current tier
     */
    

/**
 * @return array<string, mixed>
 */
public function getResponse(string $query, callable $fullResponseCallback): array
    {
        $tier = $this->getCurrentTier();

        switch ($tier) {
            case self::TIER_FULL:
                return $this->handleFullTier($query, $fullResponseCallback);

            case self::TIER_REDUCED:
                return $this->handleReducedTier($query, $fullResponseCallback);

            case self::TIER_CACHE_ONLY:
                return $this->handleCacheOnlyTier($query);

            case self::TIER_EMERGENCY:
            default:
                return $this->handleEmergencyTier();
        }
    }

    /**
     * Kendalikan tier penuh
     * Handle full tier
     */
    

/**
 * @return array<string, mixed>
 */
protected function handleFullTier(string $query, callable $callback): array
    {
        try {
            return $callback($query);
        } catch (\Exception $e) {
            Log::error('Full tier response failed, falling back', ['error' => $e->getMessage()]);

            return $this->handleReducedTier($query, $callback);
        }
    }

    /**
     * Kendalikan tier dikurangkan
     * Handle reduced tier
     */
    

/**
 * @return array<string, mixed>
 */
protected function handleReducedTier(string $query, callable $callback): array
    {
        $cached = $this->cacheService->getFaqResponse($query);

        if ($cached !== null) {
            $cached['degradation_tier'] = self::TIER_REDUCED;
            $cached['from_cache'] = true;

            return $cached;
        }

        try {
            $response = $callback($query);
            $response['degradation_tier'] = self::TIER_REDUCED;

            return $response;
        } catch (\Exception $e) {
            Log::error('Reduced tier response failed', ['error' => $e->getMessage()]);

            return $this->handleCacheOnlyTier($query);
        }
    }

    /**
     * Kendalikan tier cache sahaja
     * Handle cache only tier
     */
    

/**
 * @return array<string, mixed>
 */
protected function handleCacheOnlyTier(string $query): array
    {
        $cached = $this->cacheService->getFaqResponse($query);

        if ($cached !== null) {
            $cached['degradation_tier'] = self::TIER_CACHE_ONLY;
            $cached['from_cache'] = true;

            return $cached;
        }

        return [
            'answer' => 'Maaf, sistem sedang mengalami beban tinggi. Sila cuba sebentar lagi atau hubungi sokongan ICT.',
            'confidence' => 0.0,
            'degradation_tier' => self::TIER_CACHE_ONLY,
            'from_cache' => false,
            'fallback' => true,
        ];
    }

    /**
     * Kendalikan tier kecemasan
     * Handle emergency tier
     */
    

/**
 * @return array<string, mixed>
 */
protected function handleEmergencyTier(): array
    {
        return [
            'answer' => 'Sistem AI sedang tidak tersedia buat sementara waktu. Sila hubungi Bahagian Pengurusan Maklumat (BPM) untuk bantuan segera di talian 03-8891 7000.',
            'confidence' => 0.0,
            'degradation_tier' => self::TIER_EMERGENCY,
            'from_cache' => false,
            'emergency' => true,
        ];
    }

    /**
     * Rekod perubahan tier
     * Record tier change
     */
    protected function recordTierChange(int $previousTier, int $newTier, string $reason): void
    {
        $history = Cache::get(self::HISTORY_CACHE_KEY, []);

        $history[] = [
            'previous_tier' => $previousTier,
            'new_tier' => $newTier,
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
        ];

        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }

        Cache::put(self::HISTORY_CACHE_KEY, $history, 86400);
    }

    /**
     * Maklumkan pentadbir tentang perubahan degradasi
     * Notify admins about degradation change
     */
    protected function notifyAdmins(int $tier, string $reason): void
    {
        try {
            $admins = User::role(['admin', 'superuser'])->get();

            if ($admins->isEmpty()) {
                Log::warning('No admin users found for degradation notification');

                return;
            }

            Notification::send($admins, new SystemDegradationNotification($tier, $reason));

            Log::info('Degradation notification sent to admins', [
                'tier' => $tier,
                'admin_count' => $admins->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send degradation notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Dapatkan sejarah perubahan tier
     * Get tier change history
     */
    

/**
 * @return array<string, mixed>
 */
public function getTierHistory(): array
    {
        return Cache::get(self::HISTORY_CACHE_KEY, []);
    }

    /**
     * Dapatkan penerangan tier
     * Get tier description
     */
    

/**
 * @return array<string, mixed>
 */
public function getTierDescription(int $tier): array
    {
        $descriptions = [
            self::TIER_FULL => [
                'name' => 'Operasi Penuh',
                'name_en' => 'Full Operation',
                'description' => 'Semua ciri AI tersedia dan berfungsi sepenuhnya.',
                'features' => ['FAQ Bot', 'Analisis Dokumen', 'Auto-Reply', 'Embedding Baru'],
            ],
            self::TIER_REDUCED => [
                'name' => 'Operasi Dikurangkan',
                'name_en' => 'Reduced Operation',
                'description' => 'Ciri asas tersedia, embedding baru ditangguhkan.',
                'features' => ['FAQ Bot', 'Analisis Dokumen (Cache)', 'Auto-Reply'],
            ],
            self::TIER_CACHE_ONLY => [
                'name' => 'Cache Sahaja',
                'name_en' => 'Cache Only',
                'description' => 'Hanya respons yang telah dicache tersedia.',
                'features' => ['FAQ Bot (Cache)', 'Respons Statik'],
            ],
            self::TIER_EMERGENCY => [
                'name' => 'Mod Kecemasan',
                'name_en' => 'Emergency Mode',
                'description' => 'Sistem AI tidak tersedia. Hubungi sokongan.',
                'features' => ['Mesej Kecemasan'],
            ],
        ];

        return $descriptions[$tier] ?? $descriptions[self::TIER_EMERGENCY];
    }

    /**
     * Dapatkan penggunaan CPU
     * Get CPU usage
     */
    protected function getCpuUsage(): float
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return 0.0;
        }

        $load = sys_getloadavg();

        return $load ? round($load[0] * 100 / max(1, (int) shell_exec('nproc')), 2) : 0.0;
    }

    /**
     * Dapatkan penggunaan memori
     * Get memory usage
     */
    protected function getMemoryUsage(): float
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsed = memory_get_usage(true);

        $limit = $this->convertToBytes($memoryLimit);

        return $limit > 0 ? round(($memoryUsed / $limit) * 100, 2) : 0.0;
    }

    /**
     * Dapatkan kadar ralat
     * Get error rate
     */
    protected function getErrorRate(): float
    {
        $errors = (int) Cache::get('ollama:error_count', 0);
        $total = (int) Cache::get('ollama:request_count', 1);

        return $total > 0 ? round(($errors / $total) * 100, 2) : 0.0;
    }

    /**
     * Tukar rentetan memori kepada bytes
     * Convert memory string to bytes
     */
    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $numericValue = (int) $value;

        switch ($last) {
            case 'g':
                $numericValue *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $numericValue *= 1024 * 1024;
                break;
            case 'k':
                $numericValue *= 1024;
                break;
        }

        return $numericValue;
    }

    /**
     * Reset tier ke operasi penuh
     * Reset tier to full operation
     */
    public function resetToFullOperation(): bool
    {
        return $this->setTier(self::TIER_FULL, 'Manual reset to full operation');
    }
}
