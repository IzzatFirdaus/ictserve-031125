<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
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
        $this->theme = $this->getStoredTheme();
    }

    /**
     * Get stored theme from session/cookie or default to 'light'
     * v3.6.0: Light mode is immutable default
     */
    protected function getStoredTheme(): string
    {
        // Check session first (authenticated users)
        if (Session::has('theme_preference')) {
            $theme = Session::get('theme_preference', 'light');

            return in_array($theme, ['light', 'dark']) ? $theme : 'light';
        }

        // Check cookie (guest users)
        $cookieTheme = Cookie::get('theme_preference');
        if ($cookieTheme && in_array($cookieTheme, ['light', 'dark'])) {
            return $cookieTheme;
        }

        // Check user preference if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->theme_preference && in_array($user->theme_preference, ['light', 'dark'])) {
                return $user->theme_preference;
            }
        }

        // Always default to light mode (v3.6.0 requirement)
        return 'light';
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

        // Persist to session
        Session::put('theme_preference', $theme);

        // Persist to cookie (30 days)
        Cookie::queue('theme_preference', $theme, 60 * 24 * 30);

        // Update user preference if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $user->update(['theme_preference' => $theme]);
            }
        }

        // Dispatch browser event to apply theme
        $this->dispatch('theme-changed', theme: $theme);
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

    public function render(): \Illuminate\View\View
    {
        return view('livewire.components.theme-toggle');
    }
}
