<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk API Ollama AI
 *
 * Menyediakan logging permintaan, propagasi X-Request-ID,
 * dan sanitasi input untuk semua endpoint AI.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D10 Source Code Documentation v3.6.0, D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 4.1, 4.3, 7.3
 */
class OllamaApiMiddleware
{
    /**
     * Corak PII untuk sanitasi logging
     */
    private array $piiPatterns = [
        'ic' => '/\d{6}-\d{2}-\d{4}/',
        'phone' => '/\+?60\d{9,10}/',
        'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jana atau propagasi X-Request-ID
        $requestId = $request->header('X-Request-ID', Str::uuid()->toString());
        $request->headers->set('X-Request-ID', $requestId);

        // Log permintaan masuk (dengan input yang disanitasi)
        $this->logRequest($request, $requestId);

        $startTime = microtime(true);

        try {
            $response = $next($request);

            // Tambah X-Request-ID ke respons
            $response->headers->set('X-Request-ID', $requestId);

            // Log respons
            $this->logResponse($request, $response, $requestId, microtime(true) - $startTime);

            return $response;
        } catch (\Throwable $e) {
            // Log ralat dan kembalikan respons ralat standard
            $this->logError($request, $e, $requestId, microtime(true) - $startTime);

            return $this->errorResponse($e, $requestId);
        }
    }

    /**
     * Log permintaan masuk dengan input yang disanitasi
     */
    private function logRequest(Request $request, string $requestId): void
    {
        $sanitizedInput = $this->sanitizeForLogging($request->all());

        Log::info('Ollama API request received', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
            'user_agent' => Str::limit($request->userAgent() ?? '', 100),
            'input_keys' => array_keys($sanitizedInput),
        ]);
    }

    /**
     * Log respons keluar
     */
    private function logResponse(Request $request, Response $response, string $requestId, float $duration): void
    {
        Log::info('Ollama API response sent', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => round($duration * 1000, 2),
        ]);
    }

    /**
     * Log ralat
     */
    private function logError(Request $request, \Throwable $e, string $requestId, float $duration): void
    {
        Log::error('Ollama API error', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'user_id' => $request->user()?->id,
            'error' => $e->getMessage(),
            'exception' => get_class($e),
            'duration_ms' => round($duration * 1000, 2),
        ]);
    }

    /**
     * Sanitasi data untuk logging (redaksi PII)
     */
    

/**
 * @param array<string, mixed> $data
 */
private function sanitizeForLogging(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeForLogging($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->redactPii($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Redaksi PII dari teks
     */
    private function redactPii(string $text): string
    {
        foreach ($this->piiPatterns as $type => $pattern) {
            $replacement = match ($type) {
                'ic' => '[REDACTED_IC]',
                'phone' => '[REDACTED_PHONE]',
                'email' => '[REDACTED_EMAIL]',
                default => '[REDACTED]',
            };
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        return $text;
    }

    /**
     * Jana respons ralat standard dalam Bahasa Melayu
     */
    private function errorResponse(\Throwable $e, string $requestId): Response
    {
        $statusCode = $this->getStatusCode($e);
        $message = $this->getErrorMessage($e, $statusCode);

        return response()->json([
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $statusCode,
            ],
            'request_id' => $requestId,
        ], $statusCode);
    }

    /**
     * Dapatkan kod status HTTP berdasarkan jenis pengecualian
     */
    private function getStatusCode(\Throwable $e): int
    {
        return match (true) {
            $e instanceof \Illuminate\Validation\ValidationException => 422,
            $e instanceof \Illuminate\Auth\AuthenticationException => 401,
            $e instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
            $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
            $e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException => 429,
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException => $e->getStatusCode(),
            default => 500,
        };
    }

    /**
     * Dapatkan mesej ralat dalam Bahasa Melayu
     */
    private function getErrorMessage(\Throwable $e, int $statusCode): string
    {
        // Mesej ralat standard dalam Bahasa Melayu (D15 v3.6.0)
        return match ($statusCode) {
            400 => 'Permintaan tidak sah. Sila semak data yang dihantar.',
            401 => 'Pengesahan diperlukan. Sila log masuk untuk meneruskan.',
            403 => 'Akses ditolak. Anda tidak mempunyai kebenaran untuk tindakan ini.',
            404 => 'Sumber tidak dijumpai.',
            422 => 'Data yang dihantar tidak sah. Sila semak dan cuba lagi.',
            429 => 'Terlalu banyak permintaan. Sila tunggu sebentar dan cuba lagi.',
            500 => 'Ralat pelayan dalaman. Sila hubungi pentadbir sistem.',
            502 => 'Perkhidmatan tidak tersedia buat sementara waktu.',
            503 => 'Perkhidmatan sedang diselenggara. Sila cuba lagi kemudian.',
            default => 'Ralat tidak dijangka berlaku. Sila cuba lagi.',
        };
    }
}
