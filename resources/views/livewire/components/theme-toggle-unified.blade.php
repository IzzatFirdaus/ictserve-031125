{{--
/**
 * Unified Theme Toggle Component (v3.6.1)
 *
 * WCAG 2.2 AA compliant theme switcher with optimized performance.
 * Replaces all previous theme toggle implementations.
 *
 * @component ThemeToggleUnified
 * @version 3.6.1
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 * @wcag SC 1.4.3 (Contrast 4.5:1), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible), SC 2.5.8 (Touch Target 44x44px)
 * @requirements SRS-UX-007 (Dark Mode Support)
 * @note Light mode is immutable default per ICTServe v3.6.0
 */
--}}
@php
    $currentTheme = $theme ?? 'light';
@endphp

<div x-data="{
    theme: '{{ $currentTheme }}',
    init() {
        // Get theme from localStorage or default to light
        try {
            this.theme = localStorage.getItem('theme') || 'light';
        } catch (error) {
            this.theme = 'light';
        }

        // Listen for theme changes from other components
        window.addEventListener('theme-changed', (e) => {
            this.theme = e.detail?.theme || 'light';
        });
    }
}" class="inline-flex">
    <button type="button" wire:click="toggleTheme" wire:loading.attr="disabled"
        x-bind:aria-pressed="theme === 'dark' ? 'true' : 'false'" aria-label="{{ __('Tukar tema') }}"
        class="flex items-center justify-center
               p-2.5 rounded-lg
               bg-white/10 dark:bg-slate-800/50
               hover:bg-slate-100 dark:hover:bg-slate-700
               text-slate-600 dark:text-slate-300
               ring-1 ring-black/5 dark:ring-white/10
               focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
               dark:focus-visible:ring-offset-slate-900
               transition-colors duration-200
               min-h-11 min-w-11">

        {{-- Sun icon (shown in dark mode to switch to light) --}}
        <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5 text-warning-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>

        {{-- Moon icon (shown in light mode to switch to dark) --}}
        <svg x-show="theme === 'light'" class="w-5 h-5 text-slate-600 dark:text-slate-400" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
            </path>
        </svg>

        {{-- Screen reader text --}}
        <span class="sr-only">
            <span
                x-text="theme === 'dark' ? '{{ __('Tukar ke mod terang') }}' : '{{ __('Tukar ke mod gelap') }}'"></span>
        </span>
    </button>
</div>

{{-- Livewire event handler (inject once per page) --}}
@once
    <script>
        (function() {
            // Prevent multiple initializations
            if (window.__ictserveThemeToggleHandler) return;
            window.__ictserveThemeToggleHandler = true;

            // Listen for Livewire theme-changed events
            document.addEventListener('livewire:init', function() {
                if (window.Livewire) {
                    Livewire.on('theme-changed', function(data) {
                        const theme = data?.theme || data?.[0]?.theme || 'light';
                        // Dispatch to app.js event listeners (no window.ICTServeTheme)
                        window.dispatchEvent(new CustomEvent('theme-changed', {
                            detail: {
                                theme: theme
                            }
                        }));
                    });
                }
            });
        })
        ();
    </script>
@endonce
