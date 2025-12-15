<x-filament-widgets::widget>
    <x-filament::section :heading="null" :collapsible="false" class="theme-toggle-widget">
        <div class="flex items-center justify-end p-2">
            <button
                type="button"
                wire:click="toggleTheme"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 min-h-[44px] min-w-[44px]"
                aria-label="{{ __('Tukar tema') }}"
                aria-live="polite"
            >
                <svg
                    class="theme-icon-light w-5 h-5 text-yellow-500 {{ $this->theme === 'dark' ? '' : 'hidden' }}"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                    ></path>
                </svg>

                <svg
                    class="theme-icon-dark w-5 h-5 text-gray-700 dark:text-gray-300 {{ $this->theme === 'dark' ? 'hidden' : '' }}"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                    ></path>
                </svg>

                <span class="sr-only">{{ __('Tukar tema') }}</span>
            </button>
        </div>
    </x-filament::section>

    <!-- FOUT prevention -->
    <script>
        (function () {
            if (window.__ictserveThemeListenerInitialized) {
                return;
            }
            window.__ictserveThemeListenerInitialized = true;

            const storageKey = 'theme';
            const ttlMs = 1000 * 60 * 60 * 24 * 7; // 7 days

            function getCookieTheme() {
                const match = document.cookie.match(/(?:^|;\s*)theme_preference=([^;]+)/);
                if (!match) {
                    return null;
                }
                try {
                    return decodeURIComponent(match[1]);
                } catch (e) {
                    return match[1];
                }
            }

            function getStoredTheme() {
                try {
                    const raw = localStorage.getItem(storageKey);
                    if (!raw) {
                        return null;
                    }

                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object' && 'value' in parsed && 'expiry' in parsed) {
                        if (typeof parsed.expiry === 'number' && parsed.expiry > Date.now()) {
                            return parsed.value;
                        }

                        localStorage.removeItem(storageKey);

                        return null;
                    }

                    return raw;
                } catch (e) {
                    return null;
                }
            }

            function setStoredTheme(theme) {
                const normalized = theme === 'dark' ? 'dark' : 'light';
                const expiry = Date.now() + ttlMs;

                try {
                    localStorage.setItem(storageKey, JSON.stringify({ value: normalized, expiry }));
                } catch (e) {
                    // Ignore
                }
            }

            function applyThemePreference(theme) {
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

            const initial = getStoredTheme() || getCookieTheme() || 'light';
            applyThemePreference(initial);

            window.addEventListener('theme-changed', function (event) {
                applyThemePreference(event && event.detail ? event.detail.theme : null);
            });
        })();
    </script>
</x-filament-widgets::widget>
