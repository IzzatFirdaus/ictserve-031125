<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event broadcast for real-time AI resource usage
 *
 * Digunakan untuk menghantar penggunaan sumber AI secara masa nyata
 * ke dashboard admin melalui Laravel Pulse integration.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0, D11 Technical Design v3.6.0
 *
 * @requirements 8.5, 11.3 - Resource utilization monitoring
 */
class AIResourceUsageUpdate implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $usage;

    public int $degradationTier;

    public string $timestamp;

    /**
     * Create a new event instance.
     *
     * @param  array  $usage  Penggunaan sumber AI
     * @param  int  $degradationTier  Tahap degradasi semasa (1-4)
     */
    public function __construct(array $usage, int $degradationTier = 1)
    {
        $this->usage = $this->sanitizeUsage($usage);
        $this->degradationTier = $degradationTier;
        $this->timestamp = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    /**
     * Sanitize usage to only include allowed keys.
     */
    private function sanitizeUsage(array $usage): array
    {
        $allowedKeys = [
            'cpu_percent',
            'memory_percent',
            'memory_used_mb',
            'memory_total_mb',
            'ollama_status',
            'model_loaded',
            'gpu_memory_percent',
            'disk_usage_percent',
        ];

        return array_intersect_key($usage, array_flip($allowedKeys));
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-performance'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AIResourceUsageUpdate';
    }

    public function broadcastWith(): array
    {
        return [
            'usage' => $this->usage,
            'degradationTier' => $this->degradationTier,
            'timestamp' => $this->timestamp,
            'locale' => 'ms',
        ];
    }
}
