<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use Filament\Widgets\Widget;

class ThemeToggleWidget extends Widget
{
    use WidgetMetadata;

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets, D12 §4 MyDS Design System';
    }

    protected string $view = 'filament.widgets.theme-toggle-unified';

    public string $theme = 'light';

    public function mount(): void
    {
        $this->theme = 'light';
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme === 'dark' ? 'dark' : 'light';

        $this->dispatch('theme-changed', ['theme' => $this->theme]);
    }
}
