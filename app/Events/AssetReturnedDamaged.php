<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Asset;
use App\Models\LoanTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Asset Returned Damaged Event
 *
 * Triggered when an asset is returned with damage or poor condition.
 * Initiates automatic maintenance ticket creation and cross-module integration.
 *
 * @see D03-FR-016.2 Cross-module integration
 * @see D03-FR-018.3 Asset lifecycle tracking
 * @see Requirement 2.3 Automatic maintenance ticket creation
 * @see Requirement 8.4 Cross-module event notifications
 */
class AssetReturnedDamaged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public LoanTransaction $transaction,
        public Asset $asset
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on
     *
     * @return array<int, Channel>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastOn(): array
    {
        // Broadcast to the asset-specific private channel so staff and
        // authorized users monitoring this asset get notified.
        return [
            new PrivateChannel("asset.{$this->asset->id}"),
        ];
    }

    /**
     * The event's broadcast name
     */
    public function broadcastAs(): string
    {
        return 'asset.returned.damaged';
    }

    /**
     * Get the data to broadcast
     *
     * @return array<string, mixed>
     */
    

/**
 * @return array<string, mixed>
 */
public function broadcastWith(): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'asset_id' => $this->asset->id,
            'asset_tag' => $this->asset->asset_tag,
            'loan_application_number' => $this->transaction->loanApplication?->application_number,
            'damage_report' => $this->transaction->damage_report,
            'reported_at' => now()->toISOString(),
        ];
    }
}
