<x-filament-widgets::widget>
    <x-filament::section
        :heading="null"
        :collapsible="false"
        class="theme-toggle-widget"
    >
        <div class="flex items-center justify-end p-2">
            <button
                type="button"
                id="theme-toggle"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 min-w-[44px] min-h-[44px]"
                aria-label="{{ __('filament.actions.toggle_theme') }}"
                aria-live="polite"
            >
                {{-- Sun Icon (Light Mode) --}}
                <svg
                    id="theme-icon-light"
                    class="w-5 h-5 text-yellow-500 hidden"
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

                {{-- Moon Icon (Dark Mode) --}}
                <svg
                    id="theme-icon-dark"
                    class="w-5 h-5 text-gray-700 dark:text-gray-300 hidden"
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

                <span id="theme-toggle-text" class="sr-only">
                    {{ __('filament.actions.toggle_theme') }}
                </span>
            </button>
        </div>
    </x-filament::section>

    {{-- Theme Toggle Script with FOUT Prevention --}}
    @push('scripts')
    <script>
        (function() {
            'use strict';

            // FOUT Prevention: Apply theme before render
            const THEME_KEY = 'theme';
            const THEME_TTL = 365 * 24 * 60 * 60 * 1000; // 1 year in milliseconds

            function getStoredTheme() {
                try {
                    const stored = localStorage.getItem(THEME_KEY);
                    if (stored) {
                        const data = JSON.parse(stored);
                        const now = Date.now();
                        if (data.expiry && now < data.expiry) {
                            return data.value;
                        }
                        // Expired, remove
                        localStorage.removeItem(THEME_KEY);
                    }
                } catch (e) {
                    console.error('Error reading theme from localStorage:', e);
                }
                return 'light'; // Default to light
            }

            function setStoredTheme(theme) {
                try {
                    const expiry = Date.now() + THEME_TTL;
                    localStorage.setItem(THEME_KEY, JSON.stringify({
                        value: theme,
                        expiry: expiry
                    }));
                } catch (e) {
                    console.error('Error writing theme to localStorage:', e);
                }
            }

            function applyTheme(theme) {
                const html = document.documentElement;
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            }

            function updateToggleUI(theme) {
                const lightIcon = document.getElementById('theme-icon-light');
                const darkIcon = document.getElementById('theme-icon-dark');
                const toggleText = document.getElementById('theme-toggle-text');

                if (lightIcon && darkIcon && toggleText) {
                    if (theme === 'dark') {
                        lightIcon.classList.remove('hidden');
                        darkIcon.classList.add('hidden');
                        toggleText.textContent = '{{ __('filament.actions.switch_to_light_mode') }}';
                    } else {
                        lightIcon.classList.add('hidden');
                        darkIcon.classList.remove('hidden');
                        toggleText.textContent = '{{ __('filament.actions.switch_to_dark_mode') }}';
                    }
                }
            }

            // Apply theme immediately (FOUT prevention)
            const currentTheme = getStoredTheme();
            applyTheme(currentTheme);

            // Initialize toggle button when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                updateToggleUI(currentTheme);

                const toggleButton = document.getElementById('theme-toggle');
                if (toggleButton) {
                    toggleButton.addEventListener('click', function() {
                        const currentTheme = getStoredTheme();
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                        setStoredTheme(newTheme);
                        applyTheme(newTheme);
                        updateToggleUI(newTheme);

                        // Announce theme change for screen readers
                        const announcement = newTheme === 'dark'
                            ? '{{ __('filament.announcements.dark_mode_enabled') }}'
                            : '{{ __('filament.announcements.light_mode_enabled') }}';

                        // Create temporary announcement element
                        const announcer = document.createElement('div');
                        announcer.setAttribute('role', 'status');
                        announcer.setAttribute('aria-live', 'polite');
                        announcer.setAttribute('aria-atomic', 'true');
                        announcer.className = 'sr-only';
                        announcer.textContent = announcement;
                        document.body.appendChild(announcer);

                        setTimeout(() => {
                            document.body.removeChild(announcer);
                        }, 1000);
                    });
                }
            });
        })();
    </script>
    @endpush

    {{-- Styles for smooth transition --}}
    @push('styles')
    <style>
        .theme-toggle-widget {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        #theme-toggle {
            transition: all 0.2s ease;
        }

        #theme-toggle:hover {
            transform: scale(1.05);
        }

        #theme-toggle:active {
            transform: scale(0.95);
        }

        /* Ensure minimum touch target size for WCAG 2.2 AA */
        #theme-toggle {
            min-width: 44px;
            min-height: 44px;
        }
    </style>
    @endpush
</x-filament-widgets::widget>
