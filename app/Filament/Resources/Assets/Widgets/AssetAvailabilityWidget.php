<?php

declare(strict_types=1);

namespace App\Filament\Resources\Assets\Widgets;

use Filament\Widgets\Widget;

/**
 * Asset Availability Widget
 *
 * Displays the asset availability calendar in the asset view page.
 *
 * @trace Requirements 2.3, 7.1
 */
class AssetAvailabilityWidget extends Widget
{
    protected string $view = 'filament.resources.assets.widgets.asset-availability-widget';

    /** @var int|string|array<string, int|null> */
    protected int|string|array $columnSpan = 'full';

    public ?int $assetId = null;

    public function mount(?int $assetId = null): void
    {
        $recordId = $assetId ?? request()->route('record');
        $this->assetId = is_numeric($recordId) ? (int) $recordId : null;
    }
}
