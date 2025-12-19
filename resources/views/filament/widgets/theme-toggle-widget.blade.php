{{--
    Theme Toggle Widget for Filament Admin Panel
    @component ThemeToggleWidget
    @description Provides light/dark theme toggle with FOUT prevention (v3.6.0)
    @trace D12 §6.10, D13 §2.2, D14 §6.1.2, D14 §8.1
    @wcag SC 1.4.3 Contrast, SC 2.1.1 Keyboard, SC 2.4.7 Focus Visible
    @version 3.6.0
    @note Light mode is immutable default per D00-PREPLANNING §2.4
--}}
<x-filament-widgets::widget>
    <div class="flex items-center justify-end p-2">
        <button type="button" wire:click="toggleTheme" wire:loading.attr="disabled"
            class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg
                   bg-gray-100 dark:bg-gray-800
                   text-gray-700 dark:text-gray-200
                   hover:bg-gray-200 dark:hover:bg-gray-700
                   focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-900
                   transition-colors duration-200
                   min-h-[44px] min-w-[44px]"
            aria-label="{{ __('Tukar tema') }}" aria-live="polite"
            aria-pressed="{{ $this->theme === 'dark' ? 'true' : 'false' }}">
            {{-- Sun icon (shown in dark mode to switch to light) --}}
            <svg class="w-5 h-5 text-yellow-500 {{ $this->theme === 'dark' ? '' : 'hidden' }}" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>

            {{-- Moon icon (shown in light mode to switch to dark) --}}
            <svg class="w-5 h-5 text-gray-700 dark:text-gray-300 {{ $this->theme === 'dark' ? 'hidden' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>

            <span class="text-xs font-medium">
                {{ $this->theme === 'dark' ? __('Cahaya') : __('Gelap') }}
            </span>
        </button>
    </div>

    {{-- Theme change event listener (FOUT prevention) --}}
    <script>
        (function() {
            // Prevent multiple initializations
            if (window.__ictserveFilamentThemeInit) {
                return;
            }
            window.__ictserveFilamentThemeInit = true;

            const STORAGE_KEY = 'theme';
            const TTL_MS = 1000 * 60 * 60 * 24 * 7; // 7 days

            /**
             * Get theme from cookie (fallback)
             */
            function getCookieTheme() {
                const match = document.cookie.match(/(?:^|;\s*)theme_preference=([^;]+)/);
                if (!match) return null;
                try {
                    return decodeURIComponent(match[1]);
                } catch (e) {
                    return match[1];
                }
            }

            /**
             * Get theme from localStorage with TTL support
             */
            function getStoredTheme() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    if (!raw) return null;

                    // Handle both simple string and TTL object formats
                    try {
                        const parsed = JSON.parse(raw);
                        if (parsed && typeof parsed === 'object' && 'value' in parsed) {
                            if ('expiry' in parsed && typeof parsed.expiry === 'number') {
                                if (parsed.expiry > Date.now()) {
                                    return parsed.value;
                                }
                                localStorage.removeItem(STORAGE_KEY);
                                return null;
                            }
                            return parsed.value;
                        }
                    } catch (e) {
                        // Not JSON, treat as simple string
                    }
                    return raw;
                } catch (e) {
                    return null;
                }
            }

            /**
             * Store theme with TTL
             */
            function setStoredTheme(theme) {
                const normalized = theme === 'dark' ? 'dark' : 'light';
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        value: normalized,
                        expiry: Date.now() + TTL_MS
                    }));
                } catch (e) {
                    // Ignore storage errors
                }
            }

            /**
             * Apply theme to document
             */
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

            // Apply initial theme
            const initialTheme = getStoredTheme() || getCookieTheme() || 'light';
            applyTheme(initialTheme);

            // Listen for Livewire theme-changed events
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
        })();
    </script>
</x-filament-widgets::widget>
