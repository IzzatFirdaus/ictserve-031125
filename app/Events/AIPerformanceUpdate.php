<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event broadcast for real-time AI performance metrics
 *
 * Digunakan untuk menghantar metrik prestasi AI secara masa nyata
 * ke dashboard admin melalui Laravel Pulse integration.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0, D11 Technical Design v3.6.0
 *
 * @requirements 8.7, 11.3 - Real-time performance monitoring
 */
class AIPerformanceUpdate implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $metrics;

    public string $timestamp;

    /**
     * Create a new event instance.
     *
     * @param  array  $metrics  Metrik prestasi AI
     */
    public function __construct(array $metrics)
    {
        $this->metrics = $this->sanitizeMetrics($metrics);
        $this->timestamp = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    /**
     * Sanitize metrics to only include allowed keys.
     */
    private function sanitizeMetrics(array $metrics): array
    {
        $allowedKeys = [
            'response_time_p50',
            'response_time_p95',
            'response_time_p99',
            'requests_per_minute',
            'active_connections',
            'queue_depth',
            'error_rate',
            'uptime_percentage',
        ];

        return array_intersect_key($metrics, array_flip($allowedKeys));
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-performance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIPerformanceUpdate';
    }

    public function broadcastWith(): array
    {
        return [
            'metrics' => $this->metrics,
            'timestamp' => $this->timestamp,
            'locale' => 'ms',
        ];
    }
}
