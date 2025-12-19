{{--
    Theme Toggle Component - Unified Design (v3.6.0)
    @component ThemeToggle
    @description Simple sun/moon toggle for light/dark themes
    @trace D12 §6.10, D13 §2.2, D14 §6.1.2, D14 §8.1, D00-PREPLANNING §2.1-2.4
    @wcag SC 1.4.3 Contrast, SC 2.1.1 Keyboard, SC 2.4.7 Focus Visible, SC 2.5.8 Touch Target
    @requirements 25.4, 25.5
    @version 3.6.0 - Light mode immutable default, no system preference
    @note Standardized across all layouts (guest, portal, landing, admin)
--}}
@php
    $isDark = $theme === 'dark';
@endphp
<div x-data="{ theme: '{{ $theme }}' }" x-init="(function() {
    var stored = localStorage.getItem('theme');
    if (stored) {
        try {
            var parsed = JSON.parse(stored);
            theme = (parsed && (parsed.value || stored)) === 'dark' ? 'dark' : 'light';
        } catch (e) {
            theme = stored === 'dark' ? 'dark' : 'light';
        }
    }
})()" @theme-changed.window="theme = $event.detail?.theme || 'light'"
    class="inline-flex">
    <button type="button" wire:click="toggleTheme" wire:loading.attr="disabled"
        x-bind:aria-pressed="theme === 'dark' ? 'true' : 'false'" aria-label="{{ __('Tukar tema') }}"
        class="p-2.5 rounded-lg
               bg-white/10 dark:bg-gray-800/50
               hover:bg-gray-100 dark:hover:bg-gray-700
               text-gray-600 dark:text-gray-300
               ring-1 ring-black/5 dark:ring-white/10
               focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2
               dark:focus:ring-offset-gray-900
               transition-colors duration-200
               min-h-[44px] min-w-[44px]
               flex items-center justify-center">
        {{-- Sun icon (shown in dark mode to switch to light) --}}
        <svg x-show="theme === 'dark'" x-cloak class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>

        {{-- Moon icon (shown in light mode to switch to dark) --}}
        <svg x-show="theme !== 'dark'" class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>

        <span class="sr-only">{{ __('Tukar tema') }}</span>
    </button>
</div>

{{-- Theme change event handler (only inject once per page) --}}
@once
    <script>
        (function() {
            if (window.__ictserveThemeToggleInit) return;
            window.__ictserveThemeToggleInit = true;

            const STORAGE_KEY = 'theme';
            const TTL_MS = 1000 * 60 * 60 * 24 * 7; // 7 days

            function setStoredTheme(theme) {
                const normalized = theme === 'dark' ? 'dark' : 'light';
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        value: normalized,
                        expiry: Date.now() + TTL_MS
                    }));
                } catch (e) {}
            }

            function applyTheme(theme) {
                const normalized = theme === 'dark' ? 'dark' : 'light';
                const root = document.documentElement;

                setStoredTheme(normalized);

                if (normalized === 'dark') {
                    root.classList.add('dark');
                    root.setAttribute('data-theme', 'dark');
                } else {
                    root.classList.remove('dark');
                    root.setAttribute('data-theme', 'light');
                }
            }

            // Listen for theme-changed events from Livewire
            window.addEventListener('theme-changed', function(event) {
                const theme = event?.detail?.theme || 'light';
                applyTheme(theme);
            });

            // Also listen for Livewire dispatched events
            document.addEventListener('livewire:init', function() {
                if (window.Livewire) {
                    Livewire.on('theme-changed', function(data) {
                        const theme = data?.theme || data?.[0]?.theme || 'light';
                        applyTheme(theme);
                    });
                }
            });
        })
        ();
    </script>
@endonce
