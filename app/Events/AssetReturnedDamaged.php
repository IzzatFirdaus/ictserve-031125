<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Asset;
use App\Models\LoanTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
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
class AssetReturnedDamaged
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
}
