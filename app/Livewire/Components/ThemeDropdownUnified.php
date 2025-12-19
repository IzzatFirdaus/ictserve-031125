<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Component;

/**
 * Unified Theme Dropdown Component (v3.6.1)
 *
 * WCAG 2.2 AA compliant dropdown theme selector with keyboard navigation.
 * Follows D13 Livewire patterns and D14 UI standards.
 *
 * @version 3.6.1
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 4.1.2 (Name, Role, Value)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class ThemeDropdownUnified extends Component
{
    public string $theme = 'light';

    public function mount(): void
    {
        // Theme is managed client-side via localStorage
        // Server-side state is for initial render only
        $this->theme = 'light'; // Immutable default per v3.6.0
    }

    public function setTheme(string $theme): void
    {
        // Validate and normalize theme
        $this->theme = $theme === 'dark' ? 'dark' : 'light';

        // Dispatch event to client-side for immediate UI update
        $this->dispatch('theme-changed', ['theme' => $this->theme]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.components.theme-dropdown-unified');
    }
}
