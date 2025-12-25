<?php

declare(strict_types=1);

namespace App\Broadcasting;

use Illuminate\Contracts\Broadcasting\Broadcaster;

final class ChannelRegistrar
{
    /**
     * @var array<int, array{channel: mixed, callback: callable|string, options: array<string, mixed>}>
     */
    private array $registrations = [];

    /**
     * @param  array<string, mixed>  $options
     */
    

/**
 * @param array<string, mixed> $options
 */
public function channel(mixed $channel, callable|string $callback, array $options = []): void
    {
        $this->registrations[] = [
            'channel' => $channel,
            'callback' => $callback,
            'options' => $options,
        ];
    }

    public function register(Broadcaster $broadcaster): void
    {
        foreach ($this->registrations as $registration) {
            $channel = $registration['channel'];
            if (is_string($channel) || $channel instanceof \Illuminate\Contracts\Broadcasting\HasBroadcastChannel) {
                $broadcaster->channel(
                    $channel,
                    $registration['callback'],
                    $registration['options'],
                );
            }
        }
    }
}
