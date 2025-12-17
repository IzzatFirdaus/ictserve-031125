<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Asset;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an asset is checked out.
 *
 * @trace D03-SRS-ASSET-001, Requirements 16.4
 *
 * @version 3.6.0
 */
class AssetCheckedOut
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Asset $asset,
        public ?int $processingTimeMs = null
    ) {}
}
