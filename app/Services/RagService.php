<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use App\Models\Faq;
use App\Models\DocumentChunk;
use App\Models\GuestConversation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Perkhidmatan RAG (Retrieval-Augmented Generation) untuk ICTServe v3.6.0
 *
 * Melaksanakan pipeline RAG untuk respons AI yang kontekstual dengan
 * sokongan True Hybrid Architecture dan pengurusan konteks perbualan.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D10 Source Code Documentation v3.6.0
 * @requirements 1.1, 1.2, 1.3, 1.7, 2.2
 */
class RagService
{
    /**
     * Konfigurasi perkhidmatan
     */
    private array $config;

    /**
     * Klien Ollama untuk AI operations
     */
    private OllamaClientContract $ollamaClient;

    /**
     * Perkhidmatan embedding untuk vector operations
     */
    private EmbeddingService $embeddingService;

    /**
     * Konstruktor
     */
    public function __construct(
        OllamaClientContract $ollamaClient,
        EmbeddingService $embeddingService
    ) {
        $this->ollamaClient = $ollamaClient;
        $this->embeddingService = $embeddingService;
        $this->config = config('ollama.rag', [
            'similarity_threshold' => 0.3,
            'max_results' => 5,
            'conversation_timeout' => 1800, // 30 minit
            'max_conversation_turns' => 5,
            'fallback_enabled' => true,
        ]);
    }
    /**
     * Proses query pengguna dengan RAG pipeline
     *
     * @param string $query Pertanyaan pengguna
     * @param string|null $sessionId ID sesi untuk konteks perbualan
     * @param int|null $userId ID pengguna (nullable untuk tetamu)
     * @param string|null $email Email tetamu untuk claiming feature
     * @return array Respons yang mengandungi jawapan, sumber, dan metadata
     */
    public function processQuery(
        string $query,
        ?string $sessionId = null,
        ?int $userId = null,
        ?string $email = null
    ): array {
        $startTime = microtime(true);
        $requestId = Str::uuid()->toString();

        try {
            // 1. Sanitasi input dan PII detection
            $sanitizedQuery = $this->sanitizeInput($query);

            // 2. Dapatkan konteks perbualan jika ada
            $conversationContext = $this->getConversationContext($sessionId, $userId);

            // 3. Jana embedding untuk query
            $queryEmbedding = $this->embeddingService->generateEmbedding($sanitizedQuery);

            // 4. Cari konteks yang berkaitan (FAQ + Documents)
            $relevantContext = $this->retrieveRelevantContext($queryEmbedding, $sanitizedQuery);

            // 5. Bina prompt dengan konteks
            $prompt = $this->constructPrompt($sanitizedQuery, $relevantContext, $conversationContext);

            // 6. Jana respons menggunakan LLM
            $response = $this->generateResponse($prompt);

            // 7. Post-process respons
            $processedResponse = $this->postProcessResponse($response, $relevantContext);

            // 8. Simpan konteks perbualan
            $this->saveConversationContext($sessionId, $userId, $email, $sanitizedQuery, $processedResponse);

            // 9. Log operasi untuk audit
            $this->logOperation($requestId, $sanitizedQuery, $processedResponse, $userId, microtime(true) - $startTime);

            return [
                'success' => true,
                'answer' => $processedResponse['answer'],
                'sources' => $processedResponse['sources'],
                'confidence' => $processedResponse['confidence'],
                'conversation_id' => $sessionId,
                'request_id' => $requestId,
                'processing_time' => microtime(true) - $startTime,
            ];

        } catch (\Exception $e) {
            Log::error('RAG processing error', [
                'request_id' => $requestId,
                'query' => $sanitizedQuery ?? $query,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'processing_time' => microtime(true) - $startTime,
            ]);

            // Fallback response
            return $this->getFallbackResponse($query, $requestId);
        }
    }
    /**
     * Sanitasi input dan deteksi PII
     */
    private function sanitizeInput(string $input): string
    {
        // Trim dan bersihkan input
        $sanitized = trim($input);

        // Redaksi PII untuk logging (tidak mengubah input sebenar)
        $this->detectAndLogPii($sanitized);

        return $sanitized;
    }

    /**
     * Deteksi dan log PII untuk audit compliance
     */
    private function detectAndLogPii(string $text): void
    {
        $piiPatterns = [
            'ic' => '/\d{6}-\d{2}-\d{4}/',
            'phone' => '/\+?60\d{9,10}/',
            'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
        ];

        foreach ($piiPatterns as $type => $pattern) {
            if (preg_match($pattern, $text)) {
                Log::warning('PII detected in user query', [
                    'type' => $type,
                    'timestamp' => now()->toISOString(),
                ]);
            }
        }
    }

    /**
     * Dapatkan konteks perbualan dari cache atau database
     */
    private function getConversationContext(?string $sessionId, ?int $userId): array
    {
        if (!$sessionId) {
            return [];
        }

        $cacheKey = "conversation_context:{$sessionId}";
        $context = Cache::get($cacheKey);

        if ($context !== null) {
            return $context;
        }

        // Cari dalam database untuk authenticated users
        if ($userId) {
            $conversation = GuestConversation::where('session_id', $sessionId)
                ->where('claimed_by_user_id', $userId)
                ->where('expires_at', '>', now())
                ->first();

            if ($conversation) {
                return $conversation->conversation_history ?? [];
            }
        }

        return [];
    }
    /**
     * Cari konteks yang berkaitan dari FAQ dan dokumen
     */
    private function retrieveRelevantContext(array $queryEmbedding, string $query): array
    {
        $relevantSources = [];

        // 1. Cari FAQ yang berkaitan
        $relevantFaqs = $this->searchRelevantFaqs($queryEmbedding, $query);
        $relevantSources = array_merge($relevantSources, $relevantFaqs);

        // 2. Cari document chunks yang berkaitan
        $relevantChunks = $this->searchRelevantDocuments($queryEmbedding);
        $relevantSources = array_merge($relevantSources, $relevantChunks);

        // 3. Susun mengikut skor persamaan
        usort($relevantSources, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // 4. Ambil top results sahaja
        return array_slice($relevantSources, 0, $this->config['max_results']);
    }

    /**
     * Cari FAQ yang berkaitan
     */
    private function searchRelevantFaqs(array $queryEmbedding, string $query): array
    {
        $faqs = [];

        // Cari menggunakan full-text search terlebih dahulu
        $faqResults = Faq::search($query)
            ->withMinScore($this->config['similarity_threshold'])
            ->limit(10)
            ->get();

        foreach ($faqResults as $faq) {
            // Kira semantic similarity jika ada embedding
            $similarity = $this->calculateTextSimilarity($query, $faq->question . ' ' . $faq->answer);

            if ($similarity >= $this->config['similarity_threshold']) {
                $faqs[] = [
                    'type' => 'faq',
                    'id' => $faq->id,
                    'content' => $faq->answer,
                    'source' => $faq->question,
                    'similarity' => $similarity,
                    'metadata' => [
                        'tags' => $faq->tags,
                        'created_by' => $faq->created_by,
                    ],
                ];
            }
        }

        return $faqs;
    }

    /**
     * Cari document chunks yang berkaitan
     */
    private function searchRelevantDocuments(array $queryEmbedding): array
    {
        $chunks = [];

        // Cari chunks dengan embedding
        $documentChunks = DocumentChunk::withEmbedding()
            ->with('document')
            ->get();

        foreach ($documentChunks as $chunk) {
            $similarity = $chunk->cosineSimilarity($queryEmbedding);

            if ($similarity >= $this->config['similarity_threshold']) {
                $chunks[] = [
                    'type' => 'document',
                    'id' => $chunk->id,
                    'content' => $chunk->chunk_text,
                    'source' => $chunk->document->filename,
                    'similarity' => $similarity,
                    'metadata' => [
                        'document_id' => $chunk->document_id,
                        'chunk_index' => $chunk->chunk_index,
                        'uploaded_by' => $chunk->document->uploaded_by,
                    ],
                ];
            }
        }

        return $chunks;
    }
    /**
     * Kira persamaan teks menggunakan simple similarity
     */
    private function calculateTextSimilarity(string $text1, string $text2): float
    {
        // Simple similarity calculation - boleh dipertingkatkan dengan embedding
        $words1 = str_word_count(strtolower($text1), 1);
        $words2 = str_word_count(strtolower($text2), 1);

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        return count($union) > 0 ? count($intersection) / count($union) : 0.0;
    }

    /**
     * Bina prompt dengan konteks dan sejarah perbualan
     */
    private function constructPrompt(string $query, array $context, array $conversationHistory): string
    {
        $systemPrompt = "Anda adalah pembantu AI untuk sistem ICTServe MOTAC. " .
                       "Jawab dalam Bahasa Melayu sahaja. " .
                       "Gunakan konteks yang diberikan untuk memberikan jawapan yang tepat dan membantu.";

        $contextText = '';
        if (!empty($context)) {
            $contextText = "\n\nKonteks yang berkaitan:\n";
            foreach ($context as $item) {
                $contextText .= "- {$item['source']}: {$item['content']}\n";
            }
        }

        $conversationText = '';
        if (!empty($conversationHistory)) {
            $conversationText = "\n\nSejarah perbualan:\n";
            $recentHistory = array_slice($conversationHistory, -$this->config['max_conversation_turns']);
            foreach ($recentHistory as $turn) {
                $conversationText .= "Pengguna: {$turn['query']}\n";
                $conversationText .= "Pembantu: {$turn['response']}\n\n";
            }
        }

        return $systemPrompt . $contextText . $conversationText . "\n\nPertanyaan: {$query}\n\nJawapan:";
    }

    /**
     * Jana respons menggunakan LLM
     */
    private function generateResponse(string $prompt): array
    {
        $payload = [
            'prompt' => $prompt,
            'temperature' => 0.7,
            'max_tokens' => 2048,
            'top_p' => 0.9,
        ];

        return $this->ollamaClient->generate($payload);
    }

    /**
     * Post-process respons dan tambah metadata
     */
    private function postProcessResponse(array $response, array $context): array
    {
        $answer = $response['response'] ?? '';

        // Bersihkan respons
        $answer = trim($answer);

        // Kira confidence berdasarkan kualiti konteks
        $confidence = $this->calculateConfidence($context);

        // Format sumber
        $sources = array_map(function ($item) {
            return [
                'type' => $item['type'],
                'source' => $item['source'],
                'similarity' => round($item['similarity'], 3),
            ];
        }, $context);

        return [
            'answer' => $answer,
            'sources' => $sources,
            'confidence' => $confidence,
        ];
    }
    /**
     * Kira confidence score berdasarkan kualiti konteks
     */
    private function calculateConfidence(array $context): float
    {
        if (empty($context)) {
            return 0.0;
        }

        $totalSimilarity = array_sum(array_column($context, 'similarity'));
        $avgSimilarity = $totalSimilarity / count($context);

        // Normalize ke 0-1 range
        return min(1.0, $avgSimilarity * 2);
    }

    /**
     * Simpan konteks perbualan untuk follow-up questions
     */
    private function saveConversationContext(
        ?string $sessionId,
        ?int $userId,
        ?string $email,
        string $query,
        array $response
    ): void {
        if (!$sessionId) {
            return;
        }

        $conversationTurn = [
            'query' => $query,
            'response' => $response['answer'],
            'timestamp' => now()->toISOString(),
            'confidence' => $response['confidence'],
        ];

        // Simpan ke cache untuk akses pantas
        $cacheKey = "conversation_context:{$sessionId}";
        $existingContext = Cache::get($cacheKey, []);
        $existingContext[] = $conversationTurn;

        // Keep only recent turns
        $existingContext = array_slice($existingContext, -$this->config['max_conversation_turns']);

        Cache::put($cacheKey, $existingContext, $this->config['conversation_timeout']);

        // Simpan ke database untuk guest conversations (claiming feature)
        if (!$userId && $email) {
            $this->saveGuestConversation($sessionId, $email, $existingContext);
        }
    }

    /**
     * Simpan guest conversation untuk claiming feature
     */
    private function saveGuestConversation(string $sessionId, string $email, array $context): void
    {
        GuestConversation::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'email' => $email,
                'conversation_history' => $context,
                'expires_at' => now()->addSeconds($this->config['conversation_timeout']),
            ]
        );
    }

    /**
     * Log operasi untuk audit trail
     */
    private function logOperation(
        string $requestId,
        string $query,
        array $response,
        ?int $userId,
        float $processingTime
    ): void {
        // Sanitasi query untuk logging
        $sanitizedQuery = $this->redactPiiForLogging($query);

        \App\Models\MessageLog::create([
            'request_id' => $requestId,
            'operation_type' => 'faq_query',
            'user_id' => $userId,
            'sanitized_input' => $sanitizedQuery,
            'response_summary' => Str::limit($response['answer'], 200),
            'metadata' => [
                'confidence' => $response['confidence'],
                'sources_count' => count($response['sources']),
                'processing_time' => $processingTime,
                'model_used' => config('ollama.model'),
            ],
            'hash' => hash('sha256', $requestId . $sanitizedQuery . $response['answer']),
            'processed_at' => now(),
        ]);
    }
    /**
     * Redaksi PII untuk logging
     */
    private function redactPiiForLogging(string $text): string
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
     * Dapatkan fallback response jika AI gagal
     */
    private function getFallbackResponse(string $query, string $requestId): array
    {
        if (!$this->config['fallback_enabled']) {
            return [
                'success' => false,
                'error' => 'Perkhidmatan AI tidak tersedia pada masa ini.',
                'request_id' => $requestId,
            ];
        }

        return [
            'success' => true,
            'answer' => 'Maaf, saya tidak dapat memberikan jawapan yang tepat untuk pertanyaan anda pada masa ini. ' .
                       'Sila hubungi pasukan sokongan ICT untuk bantuan lanjut atau cipta tiket helpdesk.',
            'sources' => [],
            'confidence' => 0.0,
            'is_fallback' => true,
            'request_id' => $requestId,
            'suggestion' => 'Anda boleh cuba bertanya dengan cara yang berbeza atau hubungi sokongan teknikal.',
        ];
    }

    /**
     * Claim guest conversation untuk authenticated user
     */
    public function claimGuestConversation(string $sessionId, int $userId, string $email): bool
    {
        try {
            $conversation = GuestConversation::where('session_id', $sessionId)
                ->where('email', $email)
                ->whereNull('claimed_by_user_id')
                ->first();

            if ($conversation) {
                $conversation->update([
                    'claimed_by_user_id' => $userId,
                    'claimed_at' => now(),
                ]);

                // Transfer conversation context to user's cache
                $cacheKey = "conversation_context:{$sessionId}";
                Cache::put($cacheKey, $conversation->conversation_history, $this->config['conversation_timeout']);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to claim guest conversation', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Dapatkan sejarah perbualan untuk pengguna
     */
    public function getConversationHistory(string $sessionId, ?int $userId = null): array
    {
        $cacheKey = "conversation_context:{$sessionId}";
        $context = Cache::get($cacheKey, []);

        if (empty($context) && $userId) {
            // Cari dalam database
            $conversation = GuestConversation::where('session_id', $sessionId)
                ->where('claimed_by_user_id', $userId)
                ->first();

            if ($conversation) {
                $context = $conversation->conversation_history ?? [];
            }
        }

        return $context;
    }
}
