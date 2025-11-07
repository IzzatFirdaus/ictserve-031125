<?php

declare(strict_types=1);

namespace Tests\Unit;

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

    public function test_card_portal_variant_uses_surface_palette(): void
    {
        $cardPath = $this->basePath('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.'card.blade.php');
        $contents = file_get_contents($cardPath);

        $this->assertStringContainsString("variant' => 'default'", $contents);
        $this->assertStringContainsString('bg-slate-900/70', $contents);
        $this->assertStringContainsString('border-slate-800', $contents);
    }

    public function test_surface_button_variant_uses_dark_tokens(): void
    {
        $buttonPath = $this->basePath('resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR.'button.blade.php');
        $contents = file_get_contents($buttonPath);

        $this->assertStringContainsString("'surface' => 'bg-slate-800/80", $contents);
        $this->assertStringContainsString('border-slate-700', $contents);
    }
}

