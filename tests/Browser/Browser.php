<?php

declare(strict_types=1);

namespace Tests\Browser;

/**
 * Minimal Dusk Browser stub for environments without Laravel Dusk installed.
 */
class Browser
{
    public function __call(string $name, array $arguments): self
    {
        return $this;
    }

    /**
     * @return array<int, mixed>
     */
    public function script(string $script): array
    {
        return [null];
    }
}
