<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeToggle;
use PHPUnit\Framework\TestCase;

class ThemeToggleTest extends TestCase
{
    public function test_theme_toggle_allows_to_json_call(): void
    {
        $component = new ThemeToggle;
        $component->theme = 'dark';

        $this->assertSame(['theme' => 'dark'], $component->toJSON());
    }
}
