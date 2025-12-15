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
            $broadcaster->channel(
                $registration['channel'],
                $registration['callback'],
                $registration['options'],
            );
        }
    }
}
