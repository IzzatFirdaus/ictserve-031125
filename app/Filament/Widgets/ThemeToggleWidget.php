<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ThemeToggleWidget extends Widget
{
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
