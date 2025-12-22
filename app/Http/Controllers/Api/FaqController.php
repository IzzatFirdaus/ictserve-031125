<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaqQueryRequest;
use App\Services\RagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Pengawal API untuk FAQ Bot AI
 *
 * Menyediakan endpoint untuk pertanyaan FAQ berkuasa AI dengan
 * sokongan hybrid (tetamu + authenticated) dalam sistem ICTServe v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0, D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 1.1, 1.4, 7.1, 8.4
 */
class FaqController extends Controller
{
    /**
     * Perkhidmatan RAG untuk pemprosesan pertanyaan
     */
    private RagService $ragService;

    /**
     * Konstruktor
     */
    public function __construct(RagService $ragService)
    {
        $this->ragService = $ragService;
    }

    /**
     * Proses pertanyaan FAQ menggunakan AI
     *
     * @param  FaqQueryRequest  $request  Permintaan yang telah disahkan
     * @return JsonResponse Respons JSON dengan jawapan AI
     */
    public function query(FaqQueryRequest $request): JsonResponse
    {
        $startTime = microtime(true);
        $requestId = $request->header('X-Request-ID', Str::uuid()->toString());

        try {
            $query = $request->validated('query');
            $sessionId = $request->validated('session_id') ?? $request->session()->getId();
            $email = $request->validated('email');

            // Dapatkan user_id jika authenticated (nullable untuk tetamu)
            $userId = $request->user()?->id;

            // Periksa cache untuk respons yang sama
            $cacheKey = $this->generateCacheKey($query, $userId);
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse && ! $request->boolean('force_refresh')) {
                Log::debug('FAQ query served from cache', [
                    'request_id' => $requestId,
                    'cache_key' => $cacheKey,
                ]);

                return $this->successResponse(
                    array_merge($cachedResponse, [
                        'cached' => true,
                        'request_id' => $requestId,
                    ])
                );
            }

            // Proses pertanyaan menggunakan RAG
            $result = $this->ragService->processQuery(
                $query,
                $sessionId,
                $userId,
                $email
            );

            // Cache respons jika berjaya
            if ($result['success']) {
                Cache::put($cacheKey, $result, config('ollama.cache.ttl', 3600));
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('FAQ query processed', [
                'request_id' => $requestId,
                'user_id' => $userId,
                'query_length' => strlen($query),
                'confidence' => $result['confidence'] ?? 0,
                'processing_time' => $processingTime,
                'cached' => false,
            ]);

            return $this->successResponse(array_merge($result, [
                'request_id' => $requestId,
                'processing_time' => round($processingTime, 3),
            ]));
        } catch (\Exception $e) {
            Log::error('FAQ query failed', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'processing_time' => microtime(true) - $startTime,
            ]);

            return $this->errorResponse(
                'Maaf, berlaku ralat semasa memproses pertanyaan anda. Sila cuba lagi.',
                500,
                $requestId
            );
        }
    }

    /**
     * Dapatkan sejarah perbualan untuk sesi semasa
     */
    public function history(\Illuminate\Http\Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id') ?? $request->session()->getId();
        $userId = $request->user()?->id;

        try {
            $history = $this->ragService->getConversationHistory($sessionId, $userId);

            return $this->successResponse([
                'session_id' => $sessionId,
                'history' => $history,
                'count' => count($history),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get conversation history', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Gagal mendapatkan sejarah perbualan.',
                500
            );
        }
    }

    /**
     * Claim perbualan tetamu untuk pengguna authenticated
     */
    public function claimConversation(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = $request->user();

        if (! $user) {
            return $this->errorResponse(
                'Anda perlu log masuk untuk claim perbualan.',
                401
            );
        }

        try {
            $success = $this->ragService->claimGuestConversation(
                $request->input('session_id'),
                $user->id,
                $request->input('email')
            );

            if ($success) {
                return $this->successResponse([
                    'message' => 'Perbualan berjaya dipautkan ke akaun anda.',
                    'claimed' => true,
                ]);
            }

            return $this->errorResponse(
                'Perbualan tidak dijumpai atau telah dipautkan.',
                404
            );
        } catch (\Exception $e) {
            Log::error('Failed to claim conversation', [
                'user_id' => $user->id,
                'session_id' => $request->input('session_id'),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Gagal memautkan perbualan.',
                500
            );
        }
    }

    /**
     * Jana kunci cache untuk pertanyaan
     */
    private function generateCacheKey(string $query, ?int $userId): string
    {
        $hash = hash('sha256', strtolower(trim($query)));
        $userPart = $userId ? "user:{$userId}" : 'guest';

        return "faq:query:{$userPart}:{$hash}";
    }

    /**
     * Format respons berjaya
     */
    

/**
 * @param array<string, mixed> $data
 */
private function successResponse(array $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Format respons ralat dalam Bahasa Melayu
     */
    private function errorResponse(string $message, int $status, ?string $requestId = null): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $status,
            ],
        ];

        if ($requestId) {
            $response['request_id'] = $requestId;
        }

        return response()->json($response, $status);
    }
}
