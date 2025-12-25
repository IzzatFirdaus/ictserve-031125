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
 * Event broadcast when auto-reply draft is approved
 *
 * Digunakan untuk memberitahu pengguna berkaitan apabila
 * draf balasan automatik telah diluluskan.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 3.4, 3.6, 11.2 - Auto-reply approval workflow
 */
class AutoReplyApproved implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $draftId;

    public int $approvedById;

    public string $approverName;

    public string $requestId;

    public string $message;

    public string $approvedAt;

    /**
     * Create a new event instance.
     *
     * @param  AutoReplyDraft  $draft  Draf yang diluluskan
     * @param  User  $approver  Pengguna yang meluluskan
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        AutoReplyDraft $draft,
        User $approver,
        ?string $requestId = null
    ) {
        $this->draftId = $draft->id;
        $this->approvedById = $approver->id;
        $this->approverName = $approver->name;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->message = sprintf(
            'Balasan automatik #%d telah diluluskan oleh %s',
            $draft->id,
            $approver->name
        );
        $this->approvedAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
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
        return 'AutoReplyApproved';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'draftId' => $this->draftId,
            'approvedById' => $this->approvedById,
            'approverName' => $this->approverName,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'approvedAt' => $this->approvedAt,
            'locale' => 'ms',
        ];
    }
}
