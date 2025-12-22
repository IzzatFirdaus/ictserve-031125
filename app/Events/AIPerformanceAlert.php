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
 * Event broadcast when AI performance threshold is breached
 *
 * Digunakan untuk memberitahu admin/superuser secara segera apabila
 * ambang prestasi AI dilanggar (CPU > 80%, Memory > 90%, Response > 5s).
 * Menggunakan ShouldBroadcastNow untuk penghantaran segera.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0, D11 Technical Design v3.6.0
 *
 * @requirements 8.4, 11.1, 11.2 - Graceful degradation notifications
 */
class AIPerformanceAlert implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $alertType;

    public string $requestId;

    public string $message;

    public string $severity;

    public array $metrics;

    public int $degradationTier;

    public string $alertedAt;

    /**
     * Create a new event instance.
     *
     * @param  string  $alertType  Jenis amaran: 'cpu_high', 'memory_high', 'response_slow', 'queue_backlog', 'cache_miss_high'
     * @param  array  $metrics  Metrik prestasi semasa
     * @param  int  $degradationTier  Tahap degradasi semasa (1-4)
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        string $alertType,
        array $metrics = [],
        int $degradationTier = 1,
        ?string $requestId = null
    ) {
        $this->alertType = $alertType;
        $this->metrics = $metrics;
        $this->degradationTier = $degradationTier;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->severity = $this->determineSeverity($alertType, $degradationTier);
        $this->message = $this->generateMessage($alertType, $metrics);
        $this->alertedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    private function determineSeverity(string $alertType, int $degradationTier): string
    {
        if ($degradationTier >= 3) {
            return 'critical';
        }

        return match ($alertType) {
            'cpu_high', 'memory_high' => $degradationTier >= 2 ? 'high' : 'medium',
            'response_slow' => 'high',
            'queue_backlog' => 'medium',
            'cache_miss_high' => 'low',
            default => 'medium',
        };
    }

    private function generateMessage(string $alertType, array $metrics): string
    {
        return match ($alertType) {
            'cpu_high' => sprintf(
                'Penggunaan CPU tinggi: %.1f%% (ambang: 80%%)',
                $metrics['cpu_usage'] ?? 0
            ),
            'memory_high' => sprintf(
                'Penggunaan memori tinggi: %.1f%% (ambang: 90%%)',
                $metrics['memory_usage'] ?? 0
            ),
            'response_slow' => sprintf(
                'Masa respons AI lambat: %.2fs (ambang: 5s)',
                $metrics['response_time'] ?? 0
            ),
            'queue_backlog' => sprintf(
                'Baris gilir AI tertunggak: %d kerja menunggu',
                $metrics['queue_size'] ?? 0
            ),
            'cache_miss_high' => sprintf(
                'Kadar cache miss tinggi: %.1f%%',
                $metrics['cache_miss_rate'] ?? 0
            ),
            default => 'Amaran prestasi sistem AI',
        };
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
        return 'AIPerformanceAlert';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'alertType' => $this->alertType,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'severity' => $this->severity,
            'metrics' => $this->metrics,
            'degradationTier' => $this->degradationTier,
            'alertedAt' => $this->alertedAt,
            'locale' => 'ms',
        ];
    }
}
