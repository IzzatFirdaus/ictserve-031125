<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use App\Models\DataResidencyLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Klien Ollama untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menyediakan antara muka untuk berkomunikasi dengan
 * pelayan Ollama LLM. Termasuk caching, retry logic, dan pemantauan prestasi.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0
 *
 * @requirements 6.1, 7.3, 8.1, 8.4
 */
class OllamaClient implements OllamaClientContract
{
    /**
     * Konfigurasi klien
     */
    private array $config;

    /**
     * Statistik prestasi
     */
    private array $stats = [
        'total_requests' => 0,
        'total_response_time' => 0.0,
        'cache_hits' => 0,
        'cache_misses' => 0,
        'errors' => 0,
        'last_health_check' => null,
    ];

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->config = config('ollama');
        $this->loadStatsFromCache();
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function generate(array $payload): array
    {
        $startTime = microtime(true);

        try {
            // Validasi payload
            $this->validateGeneratePayload($payload);

            // Tetapkan nilai lalai - PENTING: stream mesti false untuk respons JSON tunggal
            $payload = [
                'model' => $this->config['model'],
                'system' => $this->config['default_prompt'],
                'temperature' => $this->config['performance']['temperature'] ?? 0.7,
                'top_p' => $this->config['performance']['top_p'] ?? 0.9,
                'num_predict' => $this->config['performance']['max_tokens'] ?? 2048,
                'keep_alive' => $this->config['performance']['keep_alive'] ?? '5m',
                'stream' => false, // KRITIKAL: Mesti false untuk respons JSON tunggal
                ...$payload,
            ];

            // Periksa cache terlebih dahulu
            $cacheKey = $this->generateCacheKey('generate', $payload);
            $cachedResponse = $this->getCachedResponse($cacheKey);

            if ($cachedResponse !== null) {
                $this->stats['cache_hits']++;
                $this->updateStats($startTime);

                return $cachedResponse;
            }

            $this->stats['cache_misses']++;

            // Hantar permintaan ke Ollama
            $response = $this->makeRequest('/api/generate', $payload);

            // Cache respons jika berjaya
            if (isset($response['response'])) {
                $ttl = $this->getCacheTtl('generate');
                $this->cacheResponse($cacheKey, $response, $ttl);
            }

            $this->updateStats($startTime);

            // Dispatch event for AIServiceMetrics Pulse recorder
            // trace: D03-SRS-AI-019, D18-§6.1
            try {
                event(new \App\Events\AIRequestCompleted(
                    service: 'ollama',
                    responseTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                    modelId: $payload['model'] ?? $this->config['model'],
                    queryType: $payload['query_type'] ?? 'faq_specific',
                ));
            } catch (\Throwable $dispatchError) {
                // Jangan gagalkan permintaan jika event dispatch bermasalah.
            }

            // PKS 4.2 Data Sovereignty - Log data residency
            $this->logDataResidency(
                operation: 'generate',
                dataClassification: $payload['data_classification'] ?? 'PUBLIC',
                modelId: $payload['model'] ?? $this->config['model'],
                responseTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                inputTokens: $response['prompt_eval_count'] ?? null,
                outputTokens: $response['eval_count'] ?? null,
            );

            return $response;
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->updateStats($startTime);

            // Dispatch failure event for AIServiceMetrics Pulse recorder
            // trace: D03-SRS-AI-019, D18-§6.1
            try {
                event(new \App\Events\AIRequestFailed(
                    service: 'ollama',
                    responseTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                    errorMessage: $e->getMessage(),
                    modelId: $payload['model'] ?? $this->config['model'],
                    queryType: $payload['query_type'] ?? null,
                ));
            } catch (\Throwable $dispatchError) {
                // Jangan gagalkan permintaan jika event dispatch bermasalah.
            }

            Log::error('Ollama generate error', [
                'error' => $e->getMessage(),
                'payload' => $this->sanitizePayload($payload),
                'response_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @return array<string, mixed>
     */
    public function embeddings(string $text, ?string $model = null): array
    {
        $startTime = microtime(true);

        try {
            // Validasi input
            if (empty(trim($text))) {
                throw new InvalidArgumentException('Teks tidak boleh kosong');
            }

            if (\strlen($text) > 8192) {
                throw new InvalidArgumentException('Teks terlalu panjang (maksimum 8192 aksara)');
            }

            // Gunakan model embedding khusus (bukan model chat)
            $embeddingModel = $model ?? $this->config['embedding_model'] ?? 'nomic-embed-text';

            $payload = [
                'model' => $embeddingModel,
                'input' => $text, // API /api/embed menggunakan 'input' bukan 'prompt'
            ];

            // Periksa cache
            $cacheKey = $this->generateCacheKey('embeddings', $payload);
            $cachedResponse = $this->getCachedResponse($cacheKey);

            if ($cachedResponse !== null) {
                $this->stats['cache_hits']++;
                $this->updateStats($startTime);

                return $cachedResponse;
            }

            $this->stats['cache_misses']++;

            // Hantar permintaan ke endpoint /api/embed (bukan /api/embeddings)
            $response = $this->makeRequest('/api/embed', $payload);

            // Normalize respons - API mengembalikan 'embeddings' (array of arrays)
            // Kita perlu 'embedding' (single array) untuk keserasian
            if (isset($response['embeddings']) && \is_array($response['embeddings']) && ! empty($response['embeddings'])) {
                $response['embedding'] = $response['embeddings'][0];
            }

            // Cache respons
            if (isset($response['embedding'])) {
                $ttl = $this->getCacheTtl('embeddings');
                $this->cacheResponse($cacheKey, $response, $ttl);
            }

            $this->updateStats($startTime);

            return $response;
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->updateStats($startTime);

            Log::error('Ollama embeddings error', [
                'error' => $e->getMessage(),
                'text_length' => \strlen($text),
                'model' => $model,
                'response_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function chat(array $messages, array $options = []): array
    {
        $startTime = microtime(true);

        try {
            // Validasi mesej
            $this->validateChatMessages($messages);

            $payload = [
                'model' => $this->config['model'],
                'messages' => $messages,
                'temperature' => $this->config['performance']['temperature'] ?? 0.7,
                'num_predict' => $this->config['performance']['max_tokens'] ?? 2048,
                'stream' => false, // KRITIKAL: Mesti false untuk respons JSON tunggal
                ...$options,
            ];

            // Chat biasanya tidak di-cache kerana kontekstual
            $response = $this->makeRequest('/api/chat', $payload);

            $this->updateStats($startTime);

            // PKS 4.2 Data Sovereignty - Log data residency for chat
            $this->logDataResidency(
                operation: 'chat',
                dataClassification: $options['data_classification'] ?? 'PUBLIC',
                modelId: $payload['model'] ?? $this->config['model'],
                responseTimeMs: (int) ((microtime(true) - $startTime) * 1000),
                inputTokens: $response['prompt_eval_count'] ?? null,
                outputTokens: $response['eval_count'] ?? null,
            );

            return $response;
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->updateStats($startTime);

            Log::error('Ollama chat error', [
                'error' => $e->getMessage(),
                'message_count' => count($messages),
                'response_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @return array<string, mixed>
     */
    public function models(): array
    {
        $startTime = microtime(true);

        try {
            // Cache senarai model untuk 1 jam
            $cacheKey = 'ollama:models';
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse !== null) {
                $this->stats['cache_hits']++;
                $this->updateStats($startTime);

                return $cachedResponse;
            }

            $this->stats['cache_misses']++;

            $response = $this->makeRequest('/api/tags');

            // Cache untuk 1 jam
            Cache::put($cacheKey, $response, 3600);

            $this->updateStats($startTime);

            return $response;
        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->updateStats($startTime);

            Log::error('Ollama models error', [
                'error' => $e->getMessage(),
                'response_time' => microtime(true) - $startTime,
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function healthCheck(): bool
    {
        try {
            $startTime = microtime(true);

            // Periksa cache health check terlebih dahulu
            $cacheKey = $this->config['cache']['keys']['health_check'];
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult !== null) {
                return $cachedResult === 'healthy';
            }

            // Lakukan health check sebenar
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->get($this->config['url'].'/api/tags');

            $isHealthy = $response->successful();
            $responseTime = microtime(true) - $startTime;

            // Cache hasil untuk 1 minit
            Cache::put($cacheKey, $isHealthy ? 'healthy' : 'unhealthy', 60);

            $this->stats['last_health_check'] = now()->toISOString();

            Log::info('Ollama health check', [
                'healthy' => $isHealthy,
                'response_time' => $responseTime,
                'status_code' => $response->status(),
            ]);

            return $isHealthy;
        } catch (\Exception $e) {
            Log::warning('Ollama health check failed', [
                'error' => $e->getMessage(),
            ]);

            // Cache hasil negatif untuk 30 saat sahaja
            Cache::put($this->config['cache']['keys']['health_check'], 'unhealthy', 30);

            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCachedResponse(string $cacheKey): ?array
    {
        if (! $this->config['cache']['enabled']) {
            return null;
        }

        return Cache::get($cacheKey);
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @param  array<string, mixed>  $response
     */
    public function cacheResponse(string $cacheKey, array $response, int $ttl): void
    {
        if (! $this->config['cache']['enabled']) {
            return;
        }

        Cache::put($cacheKey, $response, $ttl);
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @param  array<string, mixed>  $tags
     */
    public function clearCache(string|array $tags): bool
    {
        try {
            if (is_string($tags)) {
                $tags = [$tags];
            }

            foreach ($tags as $tag) {
                Cache::tags($tag)->flush();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to clear cache', [
                'tags' => $tags,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * {@inheritDoc}
     */

    /**
     * @return array<string, mixed>
     */
    public function getPerformanceStats(): array
    {
        $stats = $this->stats;

        // Kira kadar hit cache
        $totalCacheRequests = $stats['cache_hits'] + $stats['cache_misses'];
        $stats['cache_hit_rate'] = $totalCacheRequests > 0
            ? $stats['cache_hits'] / $totalCacheRequests
            : 0.0;

        // Kira masa respons purata
        $stats['average_response_time'] = $stats['total_requests'] > 0
            ? $stats['total_response_time'] / $stats['total_requests']
            : 0.0;

        // Kira kadar ralat
        $stats['error_rate'] = $stats['total_requests'] > 0
            ? $stats['errors'] / $stats['total_requests']
            : 0.0;

        return $stats;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(string $key, mixed $value): void
    {
        data_set($this->config, $key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key);
    }

    /**
     * Membuat permintaan HTTP ke pelayan Ollama
     */

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function makeRequest(string $endpoint, array $payload = []): array
    {
        $url = $this->config['url'].$endpoint;
        $timeout = $this->config['connection']['timeout'];
        $connectTimeout = $this->config['connection']['connect_timeout'];

        $attempts = 0;
        $maxAttempts = $this->config['connection']['retry_attempts'];
        $retryDelay = $this->config['connection']['retry_delay'];

        while ($attempts < $maxAttempts) {
            try {
                $response = Http::timeout($timeout)
                    ->connectTimeout($connectTimeout)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    if ($jsonResponse === null) {
                        throw new \RuntimeException('Invalid JSON response from Ollama server');
                    }

                    return $jsonResponse;
                }

                // Jika bukan ralat sementara, jangan retry
                if (! $this->isRetryableError($response->status())) {
                    throw new RequestException($response);
                }
            } catch (ConnectionException $e) {
                // Sambungan gagal, boleh retry
            } catch (RequestException $e) {
                // Ralat permintaan, periksa jika boleh retry
                if (! $this->isRetryableError($e->response?->status() ?? 0)) {
                    throw $e;
                }
            }

            $attempts++;

            if ($attempts < $maxAttempts) {
                // Exponential backoff: 1s, 2s, 4s
                $delay = $retryDelay * (2 ** ($attempts - 1));
                usleep($delay * 1000); // Convert to microseconds

                Log::info('Retrying Ollama request', [
                    'attempt' => $attempts,
                    'delay_ms' => $delay,
                    'endpoint' => $endpoint,
                ]);
            }
        }

        throw new ConnectionException('Gagal menyambung ke pelayan Ollama selepas '.$maxAttempts.' percubaan');
    }

    /**
     * Periksa jika ralat boleh di-retry
     */
    private function isRetryableError(int $statusCode): bool
    {
        return in_array($statusCode, [0, 408, 429, 500, 502, 503, 504]);
    }

    /**
     * Validasi payload untuk generate
     */

    /**
     * @param  array<string, mixed>  $payload
     */
    private function validateGeneratePayload(array $payload): void
    {
        if (empty($payload['prompt'])) {
            throw new InvalidArgumentException('Prompt diperlukan untuk generate');
        }

        if (isset($payload['temperature']) && ($payload['temperature'] < 0 || $payload['temperature'] > 1)) {
            throw new InvalidArgumentException('Temperature mesti antara 0.0 dan 1.0');
        }

        if (isset($payload['top_p']) && ($payload['top_p'] < 0 || $payload['top_p'] > 1)) {
            throw new InvalidArgumentException('Top_p mesti antara 0.0 dan 1.0');
        }
    }

    /**
     * Validasi mesej chat
     */

    /**
     * @param  array<string, mixed>  $messages
     */
    private function validateChatMessages(array $messages): void
    {
        if (empty($messages)) {
            throw new InvalidArgumentException('Mesej diperlukan untuk chat');
        }

        foreach ($messages as $message) {
            if (! isset($message['role']) || ! isset($message['content'])) {
                throw new InvalidArgumentException('Setiap mesej mesti mempunyai role dan content');
            }

            if (! in_array($message['role'], ['system', 'user', 'assistant'])) {
                throw new InvalidArgumentException('Role mesej mesti system, user, atau assistant');
            }
        }
    }

    /**
     * Jana kunci cache
     */

    /**
     * @param  array<string, mixed>  $payload
     */
    private function generateCacheKey(string $operation, array $payload): string
    {
        $hash = md5(serialize($payload));

        return match ($operation) {
            'generate' => $this->config['cache']['keys']['faq_query'] ?? "ollama:generate:{$hash}",
            'embeddings' => str_replace('{hash}', $hash, $this->config['cache']['keys']['embedding'] ?? "ollama:embedding:{$hash}"),
            default => "ollama:{$operation}:{$hash}",
        };
    }

    /**
     * Dapatkan TTL cache berdasarkan operasi
     */
    private function getCacheTtl(string $operation): int
    {
        return match ($operation) {
            'generate' => $this->config['cache']['ttl']['faq_queries'] ?? 3600,
            'embeddings' => $this->config['cache']['ttl']['embeddings'] ?? 86400,
            'common' => $this->config['cache']['ttl']['common_queries'] ?? 7200,
            default => 3600,
        };
    }

    /**
     * Sanitasi payload untuk logging
     */

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sanitizePayload(array $payload): array
    {
        // Redaksi maklumat sensitif untuk logging
        $sanitized = $payload;

        if (isset($sanitized['prompt'])) {
            // Redaksi PII dalam prompt
            $sanitized['prompt'] = $this->redactPii($sanitized['prompt']);
        }

        return $sanitized;
    }

    /**
     * Redaksi PII dari teks
     */
    private function redactPii(string $text): string
    {
        // Redaksi nombor IC Malaysia
        $text = preg_replace('/\d{6}-\d{2}-\d{4}/', '[REDACTED_IC]', $text);

        // Redaksi nombor telefon
        $text = preg_replace('/\+?60\d{9,10}/', '[REDACTED_PHONE]', $text);

        // Redaksi alamat e-mel
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[REDACTED_EMAIL]', $text);

        return $text;
    }

    /**
     * Kemaskini statistik prestasi
     */
    private function updateStats(float $startTime): void
    {
        $responseTime = microtime(true) - $startTime;

        $this->stats['total_requests']++;
        $this->stats['total_response_time'] += $responseTime;

        // Simpan statistik ke cache setiap 10 permintaan
        if ($this->stats['total_requests'] % 10 === 0) {
            $this->saveStatsToCache();
        }
    }

    /**
     * Simpan statistik ke cache
     */
    private function saveStatsToCache(): void
    {
        Cache::put('ollama:performance_stats', $this->stats, 3600);
    }

    /**
     * Muatkan statistik dari cache
     */
    private function loadStatsFromCache(): void
    {
        $cachedStats = Cache::get('ollama:performance_stats');

        if ($cachedStats !== null) {
            $this->stats = array_merge($this->stats, $cachedStats);
        }
    }

    /**
     * PKS 4.2 Data Sovereignty - Log data residency for compliance
     *
     * All Ollama operations are local processing, ensuring sensitive
     * data never leaves the local infrastructure.
     *
     * @trace Requirements 26.2, 26.4
     */
    private function logDataResidency(
        string $operation,
        string $dataClassification,
        string $modelId,
        int $responseTimeMs,
        ?int $inputTokens = null,
        ?int $outputTokens = null,
    ): void {
        try {
            DataResidencyLog::logOperation([
                'user_id' => auth()->id(),
                'service' => 'ollama',
                'operation' => $operation,
                'data_classification' => $dataClassification,
                'processing_location' => 'local-server',
                'is_local_processing' => true,
                'is_compliant' => true, // Ollama is always compliant (local)
                'model_id' => $modelId,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'response_time_ms' => $responseTimeMs,
                'metadata' => [
                    'server_url' => $this->config['url'] ?? 'http://localhost:11434',
                    'pks_compliance' => 'PKS 4.2',
                ],
                'compliance_notes' => 'Pemprosesan tempatan - data tidak meninggalkan infrastruktur',
            ]);
        } catch (\Throwable $e) {
            // Don't fail the request if logging fails
            Log::warning('Failed to log data residency', [
                'error' => $e->getMessage(),
                'operation' => $operation,
            ]);
        }
    }
}
