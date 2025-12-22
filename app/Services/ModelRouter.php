<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use App\Models\BedrockUsageLog;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Pulse\Facades\Pulse;

use function Spatie\Activitylog\activity;

/**
 * Intelligent AI Model Router for True Hybrid Architecture.
 *
 * Routes AI requests between AWS Bedrock (cloud) and Ollama (local) based on:
 * - Prompt complexity and length
 * - Data classification and residency requirements
 * - User consent for cloud processing
 * - Cost optimization (82% savings vs Bedrock-only)
 *
 * trace: D03-SRS-AI-007, D03-SRS-AI-011, D03-SRS-AI-017 (Hybrid Processing, Smart Routing, Cost Optimization)
 * trace: D04-§6.4, D10-§7, D18-§5.4 (AI Architecture, Model Selection Strategy)
 *
 * @see docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md
 * @see docs/aws_bedrock/IMPLEMENTATION.md
 */
class ModelRouter
{
    // trace: D03-FR-013, D04-§6.4, D10-§7 (AI routing and fallbacks)

    /** Task constants expected by tests */
    public const TASK_FAQ_SIMPLE = 'faq_simple';

    public const TASK_DOCUMENT_ANALYSIS = 'document_analysis';

    public const TASK_AUTO_REPLY = 'auto_reply_generation';

    public const TASK_CODE_ANALYSIS = 'code_analysis';

    /** Model tier constants expected by tests */
    public const MODEL_HAIKU = 'haiku';

    public const MODEL_SONNET = 'sonnet';

    public const MODEL_OPUS = 'opus';

    /** Provider constants expected by tests */
    public const PROVIDER_BEDROCK = 'bedrock';

    public const PROVIDER_OLLAMA = 'ollama';

    public const PROVIDER_STATIC = 'static';

    private ?OllamaClientContract $ollamaClient = null;

    public function __construct(?OllamaClientContract $ollamaClient = null)
    {
        $this->ollamaClient = $ollamaClient;
    }

    /**
     * Tentukan laluan untuk penjanaan teks (Bedrock/Ollama) berdasarkan prompt.
     *
     * @param  array{session_id?: string|null, user_id?: int|null, is_malaysia_resident?: bool|null, operation_type?: string|null, data_classification?: string|null, has_cloud_consent?: bool|null}  $context
     * @return array{provider: 'bedrock'|'ollama', model_id?: string|null, model_key?: string|null, reason: string}
     */
    

/**
 * @return array<string, mixed>
 */
public function routeTextGeneration(string $prompt, array $context = []): array
    {
        $configService = app(BedrockRoutingConfigurationService::class);
        $bedrockConfig = $configService->getConfiguration();

        $routing = is_array($bedrockConfig['routing'] ?? null) ? $bedrockConfig['routing'] : [];
        $maxPromptChars = (int) ($routing['max_prompt_chars'] ?? 10000);

        if ($maxPromptChars > 0 && $this->safeStrlen($prompt) > $maxPromptChars) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'reason' => 'Prompt terlalu panjang. Guna pemprosesan tempatan.',
            ];
        }

        $bedrockEnabled = (bool) ($bedrockConfig['enabled'] ?? false);
        if (! $bedrockEnabled) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'model_key' => null,
                'reason' => 'Bedrock tidak diaktifkan.',
            ];
        }

        // Pengelasan data (asas) untuk pematuhan (Phase 13.7).
        try {
            /** @var \App\Services\DataClassificationService $classifier */
            $classifier = app(DataClassificationService::class);
            $decision = $classifier->classify($prompt, $context);

            $requiresConsent = ($decision['requires_consent'] ?? false) === true;
            $hasConsent = $this->hasCloudConsent($context);

            if (($decision['should_block'] ?? false) === true) {
                return [
                    'provider' => 'ollama',
                    'model_id' => null,
                    'model_key' => null,
                    'reason' => 'Permintaan mengandungi data terhad. Pemprosesan AI disekat.',
                ];
            }

            if (($decision['allow_cloud'] ?? false) === false && (! $requiresConsent || ! $hasConsent)) {
                return [
                    'provider' => 'ollama',
                    'model_id' => null,
                    'model_key' => null,
                    'reason' => $requiresConsent
                        ? 'Data dalaman memerlukan persetujuan eksplisit sebelum dihantar ke cloud. Guna pemprosesan tempatan.'
                        : 'Polisi pengelasan data melarang pemprosesan cloud. Guna pemprosesan tempatan.',
                ];
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan routing jika klasifikasi bermasalah.
        }

        $enforceResidency = (bool) ($bedrockConfig['enforce_malaysia_residency'] ?? false);
        if ($enforceResidency && (($context['is_malaysia_resident'] ?? null) === false)) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'model_key' => null,
                'reason' => 'Residensi Malaysia tidak disahkan. Guna pemprosesan tempatan.',
            ];
        }

        if ((bool) ($bedrockConfig['prevent_cloud_pii'] ?? true) && $this->containsPii($prompt)) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'model_key' => null,
                'reason' => 'PII dikesan. Elakkan penghantaran ke cloud.',
            ];
        }

        // Penguatkuasaan bajet kos (Phase 13.8).
        if ($this->isBudgetHardStopped($bedrockConfig)) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'model_key' => null,
                'reason' => 'Bajet Bedrock untuk bulan ini telah dicapai. Guna pemprosesan tempatan.',
            ];
        }

        $modelKey = $this->getCachedModelKey($prompt, $bedrockConfig, $context);

        if ($this->isRateLimited($modelKey, $context, $bedrockConfig)) {
            return [
                'provider' => 'ollama',
                'model_id' => null,
                'model_key' => null,
                'reason' => 'Had kadar Bedrock dicapai. Guna Ollama sebagai fallback.',
            ];
        }

        $modelId = $this->resolveBedrockModelId($modelKey, $bedrockConfig);

        try {
            Pulse::record(type: 'ai_routing_provider', key: 'bedrock', value: 1)->count();
            Pulse::record(type: 'ai_routing_model', key: $modelKey, value: 1)->count();
        } catch (\Throwable $e) {
            // Jangan gagalkan permintaan jika Pulse bermasalah.
        }

        try {
            activity('ai-routing')
                ->withProperties([
                    'provider' => 'bedrock',
                    'model_key' => $modelKey,
                    'model_id' => $modelId,
                    'reason' => "Bedrock dipilih (model: {$modelKey}).",
                    'prompt_length' => $this->safeStrlen($prompt),
                    'prompt_hash' => sha1($prompt),
                    'session_id' => $context['session_id'] ?? null,
                    'user_id' => $context['user_id'] ?? null,
                ])
                ->log('Keputusan penghalaan model');
        } catch (\Throwable $e) {
            // Jangan gagalkan permintaan jika audit trail bermasalah.
        }

        Log::info('Keputusan ModelRouter', [
            'provider' => 'bedrock',
            'model_key' => $modelKey,
            'model_id' => $modelId,
            'session_id' => $context['session_id'] ?? null,
            'user_id' => $context['user_id'] ?? null,
        ]);

        return [
            'provider' => 'bedrock',
            'model_id' => $modelId,
            'model_key' => $modelKey,
            'reason' => "Bedrock dipilih (model: {$modelKey}).",
        ];
    }

    /**
     * Select provider and model tier by task with config-aware fallbacks and caching.
     *
     * @param  array{query?: string, force_model_tier?: string|null}  $context
     * @return array{provider: string, model_tier: string|null, model_id?: string|null, reason: string, cost_estimate: float}
     */
    

/**
 * @return array<string, mixed>
 */
public function selectModel(string $task, array $context = []): array
    {
        $query = is_string($context['query'] ?? null) ? (string) $context['query'] : '';
        $bedrockConfig = config('bedrock', []);

        // Force override if provided and valid
        $force = $context['force_model_tier'] ?? null;
        if (is_string($force) && in_array($force, [self::MODEL_HAIKU, self::MODEL_SONNET, self::MODEL_OPUS], true)) {
            // Provider decision still respects bedrock enabled flag & rate limits
            $provider = ((bool) ($bedrockConfig['enabled'] ?? false)) ? self::PROVIDER_BEDROCK : self::PROVIDER_OLLAMA;
            // If bedrock disabled and Ollama unhealthy → static
            if ($provider === self::PROVIDER_OLLAMA && ! $this->isOllamaHealthy()) {
                $provider = self::PROVIDER_STATIC;
            }

            $cost = $this->estimateRequestCost($force, ['query' => $query]);

            return [
                'provider' => $provider,
                'model_tier' => $force,
                'model_id' => $provider === self::PROVIDER_BEDROCK ? $this->resolveBedrockModelId($force, $bedrockConfig) : null,
                'reason' => "Model tier dipaksa kepada {$force}.",
                'cost_estimate' => $cost,
            ];
        }

        // Map task → initial tier suggestion
        $suggestedTier = match ($task) {
            self::TASK_FAQ_SIMPLE => self::MODEL_HAIKU,
            self::TASK_DOCUMENT_ANALYSIS => self::MODEL_SONNET,
            self::TASK_AUTO_REPLY, self::TASK_CODE_ANALYSIS => self::MODEL_OPUS,
            default => self::MODEL_HAIKU,
        };

        // Bedrock on if enabled; otherwise fallback to Ollama/static
        $bedrockEnabled = (bool) ($bedrockConfig['enabled'] ?? false);
        if ($bedrockEnabled) {
            // Choose best model key given query using cached routing
            $modelKey = $this->getCachedModelKey($query !== '' ? $query : $suggestedTier, $bedrockConfig, [
                'operation_type' => $task,
            ]);

            // Rate limit check
            if ($this->isRateLimited($modelKey, ['session_id' => null, 'user_id' => null], $bedrockConfig)) {
                // fallback to Ollama
                if ($this->isOllamaHealthy()) {
                    $cost = $this->estimateRequestCost($suggestedTier, ['query' => $query]);

                    return [
                        'provider' => self::PROVIDER_OLLAMA,
                        'model_tier' => $suggestedTier,
                        'model_id' => null,
                        'reason' => 'Had kadar Bedrock dicapai. Guna Ollama tempatan sebagai fallback.',
                        'cost_estimate' => $cost,
                    ];
                }

                return [
                    'provider' => self::PROVIDER_STATIC,
                    'model_tier' => null,
                    'model_id' => null,
                    'reason' => 'Semua penyedia tidak tersedia. Guna respons statik.',
                    'cost_estimate' => 0.0,
                ];
            }

            $cost = $this->estimateRequestCost($modelKey, ['query' => $query]);

            return [
                'provider' => self::PROVIDER_BEDROCK,
                'model_tier' => $modelKey,
                'model_id' => $this->resolveBedrockModelId($modelKey, $bedrockConfig),
                'reason' => "Bedrock dipilih dengan model {$modelKey}.",
                'cost_estimate' => $cost,
            ];
        }

        // Bedrock disabled → Ollama if healthy, else static
        if ($this->isOllamaHealthy()) {
            $cost = $this->estimateRequestCost($suggestedTier, ['query' => $query]);

            return [
                'provider' => self::PROVIDER_OLLAMA,
                'model_tier' => $suggestedTier,
                'model_id' => null,
                'reason' => 'Bedrock tidak diaktifkan. Guna Ollama tempatan.',
                'cost_estimate' => $cost,
            ];
        }

        return [
            'provider' => self::PROVIDER_STATIC,
            'model_tier' => null,
            'model_id' => null,
            'reason' => 'Semua penyedia AI tidak tersedia. Guna respons fallback statik.',
            'cost_estimate' => 0.0,
        ];
    }

    /**
     * Simple query complexity analysis: simple | medium | complex
     *
     * @param  array{query?: string}  $context
     */
    public function analyzeComplexity(array $context): string
    {
        $q = is_string($context['query'] ?? null) ? (string) $context['query'] : '';
        if ($q === '') {
            return 'simple';
        }

        $text = $this->safeStrtolower($q);
        $len = $this->safeStrlen($q);
        $words = str_word_count($text);

        // Count distinct technical terms
        $techTerms = [
            'server', 'database', 'authentication', 'error', 'api', 'http',
            'json', 'token', 'php', 'sql', 'encrypt', 'hash', 'regex',
            'class', 'function', 'variable', 'firewall', 'ssl', 'tls',
            'permission', 'access', 'configuration', 'debug', 'troubleshoot',
            'network', 'security', 'policy', 'restart', 'update',
        ];

        $termCount = 0;
        foreach ($techTerms as $term) {
            if (str_contains($text, $term)) {
                $termCount++;
            }
        }

        $hasCodeFence = str_contains($text, '```');

        // Complex: code + long, or 6+ tech terms, or very long
        if ($hasCodeFence || $len >= 1500 || $words >= 180 || $termCount >= 6) {
            return 'complex';
        }

        // Medium: 3-5 tech terms, or moderate length
        if ($termCount >= 3 || $len >= 400 || $words >= 60) {
            return 'medium';
        }

        return 'simple';
    }

    /**
     * Localized static fallback response for simple FAQ.
     */
    public function getStaticFallbackResponse(string $task): string
    {
        if ($task === self::TASK_FAQ_SIMPLE) {
            return 'Maaf, sistem AI tidak tersedia buat masa ini. Sila hubungi BPM untuk bantuan lanjut.';
        }

        return 'Maaf, sistem AI tidak tersedia. BPM akan membantu anda.';
    }

    /**
     * Rough cost estimate in USD for a request on a model tier.
     *
     * @param  array{query?: string, max_tokens?: int}  $context
     */
    public function estimateRequestCost(string $modelTier, array $context): float
    {
        $pricing = [
            self::MODEL_HAIKU => ['prompt' => 0.00025, 'completion' => 0.0005],
            self::MODEL_SONNET => ['prompt' => 0.00075, 'completion' => 0.0015],
            self::MODEL_OPUS => ['prompt' => 0.003, 'completion' => 0.006],
        ];

        $tier = $pricing[$modelTier] ?? $pricing[self::MODEL_HAIKU];
        $query = is_string($context['query'] ?? null) ? (string) $context['query'] : '';
        $promptTokens = max(1, (int) round(($this->safeStrlen($query)) / 4)); // crude char→token estimate
        $completionTokens = max(0, (int) ($context['max_tokens'] ?? 0));

        $cost = ($promptTokens * $tier['prompt']) + ($completionTokens * $tier['completion']);

        return round($cost, 6);
    }

    /**
     * Return basic routing statistics from Bedrock usage logs.
     *
     * @return array{total_requests:int, successful_requests:int, success_rate:float, by_model:array}
     */
    

/**
 * @return array<string, mixed>
 */
public function getRoutingStatistics(): array
    {
        try {
            $total = (int) BedrockUsageLog::count();
            $successful = (int) BedrockUsageLog::where('success', true)->count();
            $rate = $total > 0 ? (float) round(($successful / $total) * 100, 2) : 0.0;

            // Group by model
            $byModel = BedrockUsageLog::selectRaw('model_id, COUNT(*) as count')
                ->groupBy('model_id')
                ->pluck('count', 'model_id')
                ->toArray();

            return [
                'total_requests' => $total,
                'successful_requests' => $successful,
                'success_rate' => $rate,
                'by_model' => $byModel,
            ];
        } catch (\Throwable $e) {
            return [
                'total_requests' => 0,
                'successful_requests' => 0,
                'success_rate' => 0.0,
                'by_model' => [],
            ];
        }
    }

    /**
     * Clear cached routing decisions.
     */
    public function clearRoutingCache(): bool
    {
        $store = Cache::getStore();
        if ($store instanceof TaggableStore) {
            Cache::tags(['model_router', 'routing_decisions'])->flush();

            return true;
        }

        // Best-effort basic keys cleanup
        Cache::forget('model_router:stats:total_requests');
        Cache::forget('model_router:stats:successful_requests');

        return true;
    }

    /**
     * Expose summarized routing configuration.
     *
     * @return array{bedrock_enabled:bool, rate_limits:array, models:array, model_costs:array, task_types:array}
     */
    

/**
 * @return array<string, mixed>
 */
public function getRoutingConfig(): array
    {
        $cfg = config('bedrock', []);

        $modelCosts = [
            self::MODEL_HAIKU => ['prompt' => 0.00025, 'completion' => 0.0005],
            self::MODEL_SONNET => ['prompt' => 0.00075, 'completion' => 0.0015],
            self::MODEL_OPUS => ['prompt' => 0.003, 'completion' => 0.006],
        ];

        $taskTypes = [
            self::TASK_FAQ_SIMPLE => self::MODEL_HAIKU,
            self::TASK_DOCUMENT_ANALYSIS => self::MODEL_SONNET,
            self::TASK_AUTO_REPLY => self::MODEL_OPUS,
            self::TASK_CODE_ANALYSIS => self::MODEL_OPUS,
        ];

        return [
            'bedrock_enabled' => (bool) ($cfg['enabled'] ?? false),
            'rate_limits' => is_array($cfg['rate_limits'] ?? null) ? $cfg['rate_limits'] : [],
            'models' => is_array($cfg['models'] ?? null) ? $cfg['models'] : [],
            'model_costs' => $modelCosts,
            'task_types' => $taskTypes,
        ];
    }

    private function isOllamaHealthy(): bool
    {
        try {
            // Use injected client if available (tests), otherwise resolve from container
            $client = $this->ollamaClient ?? app(OllamaClientContract::class);

            return $client->healthCheck();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param  array{has_cloud_consent?: bool|null}  $context
     */
    private function hasCloudConsent(array $context): bool
    {
        return ($context['has_cloud_consent'] ?? false) === true;
    }

    /**
     * @param  array<string, mixed>  $bedrockConfig
     */
    private function isBudgetHardStopped(array $bedrockConfig): bool
    {
        $budgets = is_array($bedrockConfig['budgets'] ?? null) ? $bedrockConfig['budgets'] : [];

        $enabled = (bool) ($budgets['enabled'] ?? false);
        $hardStop = (bool) ($budgets['hard_stop'] ?? false);
        $monthlyBudget = (float) ($budgets['monthly_budget_usd'] ?? 0);

        if (! $enabled || ! $hardStop || $monthlyBudget <= 0) {
            return false;
        }

        try {
            $spent = (float) BedrockUsageLog::query()
                ->whereNotNull('cost_estimate')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('cost_estimate');

            return $spent >= $monthlyBudget;
        } catch (\Throwable $e) {
            // Jika DB bermasalah/jadual belum wujud, jangan hard stop.
            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $bedrockConfig
     */
    private function resolveBedrockModelId(string $modelKey, array $bedrockConfig): string
    {
        $models = is_array($bedrockConfig['models'] ?? null) ? $bedrockConfig['models'] : [];

        $candidate = $models[$modelKey] ?? null;
        if (is_string($candidate) && $candidate !== '') {
            return $candidate;
        }

        return (string) ($bedrockConfig['model_id'] ?? config('bedrock.model_id'));
    }

    /**
     * @param  array<string, mixed>  $bedrockConfig
     * @param  array<string, mixed>  $context
     */
    private function chooseBedrockModelKey(string $prompt, array $bedrockConfig = [], array $context = []): string
    {
        $text = $this->safeStrtolower($prompt);
        $length = $this->safeStrlen($prompt);

        $routing = is_array($bedrockConfig['routing'] ?? null) ? $bedrockConfig['routing'] : [];
        $simpleFaqMaxWords = (int) ($routing['simple_faq_max_words'] ?? 50);
        $wordCount = str_word_count($text);

        $operationType = $context['operation_type'] ?? null;
        if (is_string($operationType)) {
            if ($operationType === 'auto_reply_generation') {
                return 'opus';
            }

            if ($operationType === 'document_analysis') {
                return 'sonnet';
            }

            if ($operationType === 'faq_query' && $wordCount > 0 && $wordCount <= $simpleFaqMaxWords) {
                return 'haiku';
            }
        }

        // Opus: kandungan formal/kompleks.
        if (
            str_contains($text, 'surat rasmi')
            || str_contains($text, 'memo')
            || str_contains($text, 'minit mesyuarat')
            || str_contains($text, 'draf')
            || str_contains($text, 'polisi')
            || str_contains($text, 'kertas kerja')
            || $length >= 2500
        ) {
            return 'opus';
        }

        // Sonnet: analisis/sintesis.
        if (
            str_contains($text, 'analisis')
            || str_contains($text, 'banding')
            || str_contains($text, 'cadangkan')
            || str_contains($text, 'strategi')
            || $length >= 900
        ) {
            return 'sonnet';
        }

        // Haiku: jawapan ringkas/FAQ.
        return 'haiku';
    }

    /**
     * @param  array<string, mixed>  $bedrockConfig
     */
    private function getCachedModelKey(string $prompt, array $bedrockConfig, array $context = []): string
    {
        $routing = is_array($bedrockConfig['routing'] ?? null) ? $bedrockConfig['routing'] : [];
        $ttl = (int) ($routing['cache_ttl_seconds'] ?? 3600);
        if ($ttl <= 0) {
            return $this->chooseBedrockModelKey($prompt, $bedrockConfig, $context);
        }

        $operationType = is_string($context['operation_type'] ?? null) ? (string) $context['operation_type'] : 'default';
        $key = 'bedrock:routing:'.sha1($prompt.'|'.$operationType);

        $store = Cache::getStore();
        if ($store instanceof TaggableStore) {
            return (string) Cache::tags(['bedrock_routing'])->remember($key, $ttl, function () use ($prompt, $bedrockConfig, $context): string {
                return $this->chooseBedrockModelKey($prompt, $bedrockConfig, $context);
            });
        }

        return (string) Cache::remember($key, $ttl, function () use ($prompt, $bedrockConfig, $context): string {
            return $this->chooseBedrockModelKey($prompt, $bedrockConfig, $context);
        });
    }

    /**
     * Had kadar ringkas menggunakan RateLimiter.
     *
     * @param  array{session_id?: string|null, user_id?: int|null}  $context
     */
    private function isRateLimited(string $modelKey, array $context, array $bedrockConfig): bool
    {
        $rateLimits = is_array($bedrockConfig['rate_limits'] ?? null) ? $bedrockConfig['rate_limits'] : [];

        $enabled = (bool) ($rateLimits['enabled'] ?? true);
        if (! $enabled) {
            return false;
        }

        $maxAttempts = (int) ($rateLimits['max_attempts_per_minute'] ?? 30);

        $models = is_array($rateLimits['models'] ?? null) ? $rateLimits['models'] : [];
        $modelLimit = $models[$modelKey]['requests_per_minute'] ?? null;
        if (is_int($modelLimit) && $modelLimit > 0) {
            $maxAttempts = $modelLimit;
        }

        if ($maxAttempts <= 0) {
            return false;
        }

        $bucket = $context['user_id'] ?? $context['session_id'] ?? 'global';
        $key = "bedrock:invoke:{$modelKey}:{$bucket}";

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return true;
        }

        RateLimiter::hit($key, 60);

        return false;
    }

    private function safeStrlen(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return (int) mb_strlen($value);
        }

        return strlen($value);
    }

    private function safeStrtolower(string $value): string
    {
        if (function_exists('mb_strtolower')) {
            return (string) mb_strtolower($value);
        }

        return strtolower($value);
    }

    private function containsPii(string $text): bool
    {
        try {
            /** @var \App\Services\PIIDetectionService $pii */
            $pii = app(PIIDetectionService::class);
            $result = $pii->detectPII($text);

            return (bool) ($result['has_pii'] ?? false);
        } catch (\Throwable $e) {
            // Fallback regex ringkas.
            $patterns = [
                '/\d{6}-\d{2}-\d{4}/',
                '/\+?60\d{9,10}/',
                '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $text) === 1) {
                    return true;
                }
            }

            return false;
        }
    }
}
