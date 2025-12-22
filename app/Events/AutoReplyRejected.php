<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AutoReplyDraft;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Event broadcast when auto-reply draft is rejected
 *
 * Digunakan untuk memberitahu pengguna berkaitan apabila
 * draf balasan automatik telah ditolak.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 3.4, 3.6, 11.2 - Auto-reply approval workflow
 */
class AutoReplyRejected implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $draftId;

    public int $rejectedById;

    public string $rejectorName;

    public ?string $reason;

    public string $requestId;

    public string $message;

    public string $rejectedAt;

    /**
     * Create a new event instance.
     *
     * @param  AutoReplyDraft  $draft  Draf yang ditolak
     * @param  User  $rejector  Pengguna yang menolak
     * @param  string|null  $reason  Sebab penolakan
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        AutoReplyDraft $draft,
        User $rejector,
        ?string $reason = null,
        ?string $requestId = null
    ) {
        $this->draftId = $draft->id;
        $this->rejectedById = $rejector->id;
        $this->rejectorName = $rejector->name;
        $this->reason = $reason;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->message = sprintf(
            'Balasan automatik #%d telah ditolak oleh %s',
            $draft->id,
            $rejector->name
        );
        $this->rejectedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        return [
            new PrivateChannel('ai-approvals'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AutoReplyRejected';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'draftId' => $this->draftId,
            'rejectedById' => $this->rejectedById,
            'rejectorName' => $this->rejectorName,
            'reason' => $this->reason,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'rejectedAt' => $this->rejectedAt,
            'locale' => 'ms',
        ];
    }
}
