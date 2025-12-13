<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\Facades\CauserResolver;

/**
 * Perkhidmatan Audit AI untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menyediakan integrasi dengan Dual Audit System
 * untuk operasi AI. Menggunakan owen-it untuk compliance audit
 * dan spatie untuk operational logging.
 *
 * @version 3.6.0
 * @author Pasukan Pembangunan BPM MOTAC
 * @compliance D09 Database Documentation v3.6.0 (Dual Audit System)
 * @requirements 4.1, 4.2, 4.6, 6.5
 */
class AiAuditService
{
    /**
     * Log operasi AI untuk audit compliance
     *
     * @param string $operation Jenis operasi (faq_query, document_analysis, auto_reply_generation)
     * @param array $context Konteks operasi (input, output, metadata)
     * @param string|null $requestId X-Request-ID untuk kebolehkesanan
     */
    public function logAiOperation(string $operation, array $context, ?string $requestId = null): void
    {
        $requestId = $requestId ?? (string) Str::uuid();

        // Sanitasi PII dari konteks
        $sanitizedContext = $this->sanitizeContext($context);

        // Log menggunakan Spatie Activity Log (operational logging)
        activity()
            ->withProperties([
                'operation_type' => $operation,
                'request_id' => $requestId,
                'context' => $sanitizedContext,
                'timestamp' => now()->toISOString(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
            ])
            ->log("AI operation: {$operation}");

        // Log untuk Laravel Log (structured logging)
        Log::info('AI operation logged', [
            'operation_type' => $operation,
            'request_id' => $requestId,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'sanitized_context' => $sanitizedContext,
        ]);
    }

    /**
     * Log ralat AI untuk audit dan debugging
     *
     * @param string $operation Jenis operasi yang gagal
     * @param \Throwable $exception Pengecualian yang berlaku
     * @param array $context Konteks tambahan
     * @param string|null $requestId X-Request-ID untuk kebolehkesanan
     */
    public function logAiError(string $operation, \Throwable $exception, array $context = [], ?string $requestId = null): void
    {
        $requestId = $requestId ?? (string) Str::uuid();

        // Sanitasi konteks
        $sanitizedContext = $this->sanitizeContext($context);

        // Log menggunakan Spatie Activity Log
        activity()
            ->withProperties([
                'operation_type' => $operation,
                'request_id' => $requestId,
                'error_type' => get_class($exception),
                'error_message' => $exception->getMessage(),
                'error_code' => $exception->getCode(),
                'context' => $sanitizedContext,
                'stack_trace' => $exception->getTraceAsString(),
            ])
            ->log("AI operation error: {$operation}");

        // Log ralat untuk Laravel Log
        Log::error('AI operation failed', [
            'operation_type' => $operation,
            'request_id' => $requestId,
            'user_id' => auth()->id(),
            'error' => [
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'context' => $sanitizedContext,
        ]);
    }

    /**
     * Log prestasi AI untuk pemantauan
     *
     * @param string $operation Jenis operasi
     * @param float $responseTime Masa respons dalam saat
     * @param array $metrics Metrik tambahan (cache hit/miss, token usage, dll)
     * @param string|null $requestId X-Request-ID untuk kebolehkesanan
     */
    public function logAiPerformance(string $operation, float $responseTime, array $metrics = [], ?string $requestId = null): void
    {
        $requestId = $requestId ?? (string) Str::uuid();

        // Log prestasi menggunakan Spatie Activity Log
        activity()
            ->withProperties([
                'operation_type' => $operation,
                'request_id' => $requestId,
                'performance' => [
                    'response_time' => $responseTime,
                    'response_time_ms' => round($responseTime * 1000, 2),
                    'is_slow' => $responseTime > 2.0, // Slow threshold dari config
                ],
                'metrics' => $metrics,
                'timestamp' => now()->toISOString(),
            ])
            ->log("AI performance: {$operation}");

        // Log untuk Laravel Log jika perlahan
        if ($responseTime > 2.0) {
            Log::warning('Slow AI operation detected', [
                'operation_type' => $operation,
                'request_id' => $requestId,
                'response_time' => $responseTime,
                'metrics' => $metrics,
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Log kelulusan auto-reply untuk audit compliance
     *
     * @param int $draftId ID draft auto-reply
     * @param string $action Tindakan (approved, rejected)
     * @param int|null $approverId ID pengguna yang meluluskan
     * @param string|null $reason Sebab kelulusan/penolakan
     * @param string|null $requestId X-Request-ID untuk kebolehkesanan
     */
    public function logAutoReplyApproval(int $draftId, string $action, ?int $approverId = null, ?string $reason = null, ?string $requestId = null): void
    {
        $requestId = $requestId ?? (string) Str::uuid();

        // Log menggunakan Spatie Activity Log
        activity()
            ->causedBy($approverId)
            ->withProperties([
                'operation_type' => 'auto_reply_approval',
                'request_id' => $requestId,
                'draft_id' => $draftId,
                'action' => $action,
                'reason' => $reason,
                'approver_id' => $approverId,
                'timestamp' => now()->toISOString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Auto-reply {$action}: draft #{$draftId}");

        // Log untuk Laravel Log
        Log::info('Auto-reply approval action', [
            'operation_type' => 'auto_reply_approval',
            'request_id' => $requestId,
            'draft_id' => $draftId,
            'action' => $action,
            'reason' => $reason,
            'approver_id' => $approverId,
        ]);
    }

    /**
     * Sanitasi konteks untuk menghilangkan PII
     *
     * @param array $context Konteks asal
     * @return array Konteks yang telah disanitasi
     */
    private function sanitizeContext(array $context): array
    {
        $sanitized = $context;

        // Sanitasi input text jika ada
        if (isset($sanitized['input'])) {
            $sanitized['input'] = $this->redactPii($sanitized['input']);
        }

        // Sanitasi output text jika ada
        if (isset($sanitized['output'])) {
            $sanitized['output'] = $this->redactPii($sanitized['output']);
        }

        // Sanitasi prompt jika ada
        if (isset($sanitized['prompt'])) {
            $sanitized['prompt'] = $this->redactPii($sanitized['prompt']);
        }

        // Sanitasi metadata yang mungkin mengandungi PII
        if (isset($sanitized['metadata']) && is_array($sanitized['metadata'])) {
            foreach ($sanitized['metadata'] as $key => $value) {
                if (is_string($value)) {
                    $sanitized['metadata'][$key] = $this->redactPii($value);
                }
            }
        }

        return $sanitized;
    }

    /**
     * Redaksi PII dari teks
     *
     * @param string $text Teks asal
     * @return string Teks yang telah diredaksi
     */
    private function redactPii(string $text): string
    {
        // Redaksi nombor IC Malaysia (format: 123456-12-1234)
        $text = preg_replace('/\d{6}-\d{2}-\d{4}/', '[REDACTED_IC]', $text);

        // Redaksi nombor telefon Malaysia
        $text = preg_replace('/\+?60\d{9,10}/', '[REDACTED_PHONE]', $text);

        // Redaksi alamat e-mel
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[REDACTED_EMAIL]', $text);

        // Redaksi nombor kad kredit (format umum)
        $text = preg_replace('/\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}/', '[REDACTED_CARD]', $text);

        // Redaksi nombor passport Malaysia (format: A12345678)
        $text = preg_replace('/[A-Z]\d{8}/', '[REDACTED_PASSPORT]', $text);

        return $text;
    }

    /**
     * Dapatkan statistik audit AI
     *
     * @param int $days Bilangan hari untuk statistik (lalai: 7)
     * @return array Statistik audit
     */
    public function getAuditStats(int $days = 7): array
    {
        $startDate = now()->subDays($days);

        // Dapatkan statistik dari activity log
        $activities = \Spatie\Activitylog\Models\Activity::where('created_at', '>=', $startDate)
            ->where('description', 'like', 'AI operation%')
            ->get();

        $stats = [
            'total_operations' => $activities->count(),
            'operations_by_type' => [],
            'error_count' => 0,
            'performance_issues' => 0,
            'date_range' => [
                'start' => $startDate->toDateString(),
                'end' => now()->toDateString(),
            ],
        ];

        foreach ($activities as $activity) {
            $properties = $activity->properties ?? [];
            $operationType = $properties['operation_type'] ?? 'unknown';

            // Kira operasi mengikut jenis
            if (!isset($stats['operations_by_type'][$operationType])) {
                $stats['operations_by_type'][$operationType] = 0;
            }
            $stats['operations_by_type'][$operationType]++;

            // Kira ralat
            if (str_contains($activity->description, 'error')) {
                $stats['error_count']++;
            }

            // Kira masalah prestasi
            if (isset($properties['performance']['is_slow']) && $properties['performance']['is_slow']) {
                $stats['performance_issues']++;
            }
        }

        return $stats;
    }
}
