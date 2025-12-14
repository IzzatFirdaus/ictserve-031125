<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Lightweight checks to ensure shared component markup carries the standardized styling.
 */
class ComponentMarkupTest extends TestCase
{
    private function basePath(string $path): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.$path;
    }

    #[Test]
    public function card_uses_standard_styling(): void
    {
        $cardPath = $this->basePath('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.'card.blade.php');
        $contents = file_get_contents($cardPath);

        $this->assertStringContainsString('bg-white dark:bg-gray-800', $contents);
        $this->assertStringContainsString('border-gray-200 dark:border-gray-700', $contents);
    }

    #[Test]
    public function button_has_primary_variant(): void
    {
        $buttonPath = $this->basePath('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.'button.blade.php');
        $contents = file_get_contents($buttonPath);

        $this->assertStringContainsString("'primary' => 'bg-primary-600", $contents);
        // min-h-11 min-w-11 = 44px touch target (11 × 4px = 44px per Tailwind spacing scale)
        $this->assertStringContainsString('min-h-11 min-w-11', $contents);
    }
}
