<?php

declare(strict_types=1);

namespace App\Broadcasting;

use Illuminate\Broadcasting\BroadcastManager as BaseBroadcastManager;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Contracts\Foundation\Application;

final class BroadcastManager extends BaseBroadcastManager
{
    public function __construct(
        Application $app,
        private ChannelRegistrar $channelRegistrar,
    ) {
        parent::__construct($app);
    }

    public function driver($name = null): Broadcaster
    {
        /** @var Broadcaster $broadcaster */
        $broadcaster = parent::driver($name);
        $this->channelRegistrar->register($broadcaster);

        return $broadcaster;
    }
}
