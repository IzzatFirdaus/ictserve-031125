<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Event broadcast when AI system error occurs
 *
 * Digunakan untuk memberitahu admin/superuser secara segera apabila
 * ralat sistem AI berlaku. Menggunakan ShouldBroadcastNow untuk
 * penghantaran segera tanpa queue.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 11.1, 11.2 - Real-time AI error notifications
 */
class AIErrorOccurred implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $errorType;

    public string $requestId;

    public string $message;

    public string $severity;

    public array $context;

    public string $occurredAt;

    /**
     * Create a new event instance.
     *
     * @param  string  $errorType  Jenis ralat: 'connection', 'timeout', 'model_unavailable', 'processing', 'validation'
     * @param  string  $message  Mesej ralat dalam Bahasa Melayu
     * @param  string  $severity  Tahap keterukan: 'low', 'medium', 'high', 'critical'
     * @param  array  $context  Konteks tambahan (sanitized)
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        string $errorType,
        string $message,
        string $severity = 'medium',
        array $context = [],
        ?string $requestId = null
    ) {
        $this->errorType = $errorType;
        $this->message = $message;
        $this->severity = $severity;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->context = $this->sanitizeContext($context);
        $this->occurredAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    /**
     * Create error event from exception
     */
    public static function fromException(
        \Throwable $exception,
        string $operationType = 'unknown',
        ?string $requestId = null
    ): self {
        $errorType = match (true) {
            $exception instanceof \Illuminate\Http\Client\ConnectionException => 'connection',
            str_contains($exception->getMessage(), 'timeout') => 'timeout',
            str_contains($exception->getMessage(), 'model') => 'model_unavailable',
            default => 'processing',
        };

        $severity = match ($errorType) {
            'connection', 'model_unavailable' => 'critical',
            'timeout' => 'high',
            default => 'medium',
        };

        $message = match ($errorType) {
            'connection' => 'Gagal menyambung ke pelayan Ollama',
            'timeout' => 'Masa pemprosesan AI telah tamat tempoh',
            'model_unavailable' => 'Model AI tidak tersedia',
            default => 'Ralat semasa pemprosesan AI',
        };

        return new self(
            errorType: $errorType,
            message: $message,
            severity: $severity,
            context: [
                'operation_type' => $operationType,
                'error_class' => get_class($exception),
            ],
            requestId: $requestId
        );
    }

    

/**
 * @return array<string, mixed>
 */
private function sanitizeContext(array $context): array
    {
        $sanitized = [];
        $allowedKeys = [
            'operation_type',
            'document_id',
            'error_class',
            'retry_count',
            'degradation_tier',
        ];

        foreach ($allowedKeys as $key) {
            if (isset($context[$key])) {
                $sanitized[$key] = $context[$key];
            }
        }

        return $sanitized;
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-alerts'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIErrorOccurred';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'errorType' => $this->errorType,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'severity' => $this->severity,
            'context' => $this->context,
            'occurredAt' => $this->occurredAt,
            'locale' => 'ms',
        ];
    }
}
