<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\ThemePreferenceService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

/**
 * Theme Toggle Widget v3.6.0
 *
 * Provides light/dark theme toggle functionality for Filament admin panel.
 * Theme preference is persisted via ThemePreferenceService (session + cookie + user DB redundancy).
 * Light mode is the default and cannot be changed by system detection.
 *
 * Features:
 * - Persistent theme selection (session/cookie/DB)
 * - FOUT (Flash of Unstyled Text) prevention
 * - Accessible toggle button with ARIA attributes
 * - Smooth theme transition
 *
 * @trace D03-FR-001 (UI/UX Requirements)
 * @trace D12 UI/UX Design Guide - Theme Management
 * @trace WCAG 2.2 AA - Interactive Controls
 *
 * @author ICTServe Development Team
 *
 * @version 3.6.0
 *
 * @created 2025-12-14
 */
class ThemeToggleWidget extends Widget
{
    protected string $view = 'filament.widgets.theme-toggle-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -100; // Display at the top

    /**
     * Current theme preference: 'light' or 'dark'
     */
    public string $theme = 'light';

    public function mount(): void
    {
        $this->theme = app(ThemePreferenceService::class)->getStoredTheme();
    }

    public function toggleTheme(): void
    {
        $nextTheme = $this->theme === 'dark' ? 'light' : 'dark';

        $this->theme = $nextTheme;
        app(ThemePreferenceService::class)->setTheme($nextTheme);

        $this->dispatch('theme-changed', theme: $nextTheme);
    }

    /**
     * Can view widget based on user authentication
     */
    public static function canView(): bool
    {
        return Auth::check();
    }

    /**
     * Get widget heading
     */
    public function getHeading(): ?string
    {
        return null; // No heading needed for toggle widget
    }
}
