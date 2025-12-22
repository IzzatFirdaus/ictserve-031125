<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AutoReplyDraft;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Event broadcast when auto-reply draft is created
 *
 * Digunakan untuk memberitahu approver/admin/superuser apabila
 * draf balasan automatik baharu dicipta dan memerlukan semakan.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0
 *
 * @requirements 3.4, 11.2 - Auto-reply approval workflow
 */
class AutoReplyDraftCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $draftId;

    public string $replyableType;

    public int $replyableId;

    public string $requestId;

    public string $message;

    public string $status;

    public string $createdAt;

    /**
     * Create a new event instance.
     *
     * @param  AutoReplyDraft  $draft  Draf balasan automatik
     * @param  string|null  $requestId  X-Request-ID untuk audit trail
     */
    public function __construct(
        AutoReplyDraft $draft,
        ?string $requestId = null
    ) {
        $this->draftId = $draft->id;
        $this->replyableType = $draft->replyable_type;
        $this->replyableId = $draft->replyable_id;
        $this->status = $draft->status;
        $this->requestId = $requestId ?? (string) Str::uuid();
        $this->message = $this->generateMessage($draft);
        $this->createdAt = now()->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s');
    }

    private function generateMessage(AutoReplyDraft $draft): string
    {
        $type = match ($draft->replyable_type) {
            'App\\Models\\HelpdeskTicket' => 'tiket helpdesk',
            'App\\Models\\LoanApplication' => 'permohonan pinjaman',
            default => 'penyerahan',
        };

        return sprintf('Draf balasan automatik baharu untuk %s #%d', $type, $draft->replyable_id);
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
        return 'AutoReplyDraftCreated';
    }

    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'draftId' => $this->draftId,
            'replyableType' => $this->replyableType,
            'replyableId' => $this->replyableId,
            'requestId' => $this->requestId,
            'message' => $this->message,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'locale' => 'ms',
        ];
    }
}
