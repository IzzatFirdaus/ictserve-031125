<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Services\ThemePreferenceService;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Theme Toggle Component
 *
 * Provides dark/light/system theme switching with persistence.
 *
 * @component ThemeToggle
 *
 * @description Toggle between light, dark, and system theme preferences
 *
 * @author ICTServe Development Team
 *
 * @version 1.0.0
 *
 * @trace D12 §6.10, D14 §6.1.2, D14 §8.1
 *
 * @wcag SC 1.4.3 Contrast (Minimum), SC 2.1.1 Keyboard
 *
 * @requirements 25.1, 25.2, 25.3, 25.4, 25.5
 */
class ThemeToggle extends Component
{
    /**
     * Current theme preference: 'light' or 'dark'
     * Light mode is always default (v3.6.0)
     */
    public string $theme = 'light';

    /**
     * Whether the dropdown is open
     */
    public bool $isOpen = false;

    /**
     * Available theme options
     *
     * @var array<string, array{label: string, icon: string}>
     */
    public array $themes = [
        'light' => [
            'label' => 'Cahaya',
            'icon' => 'sun',
        ],
        'dark' => [
            'label' => 'Gelap',
            'icon' => 'moon',
        ],
    ];

    /**
     * Initialize component with stored theme preference
     */
    public function mount(): void
    {
        $this->theme = app(ThemePreferenceService::class)->getStoredTheme();
    }

    /**
     * Set theme preference and persist it
     * v3.6.0: Only 'light' and 'dark' allowed
     */
    public function setTheme(string $theme): void
    {
        if (! in_array($theme, ['light', 'dark'])) {
            return;
        }

        $this->theme = $theme;
        $this->isOpen = false;

        app(ThemePreferenceService::class)->setTheme($theme);

        // Dispatch browser event to apply theme
        $this->dispatch('theme-changed', theme: $theme);
    }

    /**
     * Toggle between light and dark
     */
    public function toggleTheme(): void
    {
        $nextTheme = $this->theme === 'dark' ? 'light' : 'dark';

        $this->setTheme($nextTheme);
    }

    /**
     * Toggle dropdown visibility
     */
    public function toggleDropdown(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    /**
     * Close dropdown
     */
    #[On('close-theme-dropdown')]
    public function closeDropdown(): void
    {
        $this->isOpen = false;
    }

    /**
     * Get current theme icon name
     */
    public function getCurrentIcon(): string
    {
        return $this->themes[$this->theme]['icon'] ?? 'sun';
    }

    /**
     * Get current theme label
     */
    public function getCurrentLabel(): string
    {
        return $this->themes[$this->theme]['label'] ?? 'Cahaya';
    }

    /**
     * Provide a JSON-safe representation for cases where the client attempts to
     * serialize the Livewire $wire proxy (which may trigger a toJSON call).
     *
     * @return array{theme: string}
     */
    public function toJSON(): array
    {
        return [
            'theme' => $this->theme,
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.components.theme-toggle');
    }
}
