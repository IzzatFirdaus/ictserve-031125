<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Unified Theme Toggle Component (v3.6.1)
 *
 * WCAG 2.2 AA compliant theme switcher with optimized performance.
 * Follows D13 Livewire patterns and D14 UI standards.
 *
 * @version 3.6.1
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class ThemeToggleUnified extends Component
{
    public string $theme = 'light';

    public function mount(): void
    {
        // Use default light theme (no window.ICTServeTheme dependency)
        // Client-side will sync from localStorage via Alpine.js
        $this->theme = 'light'; // Default per ICTServe v3.6.0
    }

    public function toggleTheme(): void
    {
        // Toggle theme state
        $this->theme = $this->theme === 'dark' ? 'light' : 'dark';

        // Dispatch event to client-side for immediate UI update
        $this->dispatch('theme-changed', ['theme' => $this->theme]);
    }

    public function render(): View
    {
        return view('livewire.components.theme-toggle-unified');
    }
}
