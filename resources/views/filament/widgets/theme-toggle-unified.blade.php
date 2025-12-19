{{--
/**
 * Unified Theme Toggle Widget for Filament Admin Panel (v3.6.1)
 *
 * WCAG 2.2 AA compliant theme toggle for Filament admin interface.
 * Integrates with unified theme system for consistent behavior.
 *
 * @component ThemeToggleWidgetUnified
 * @version 3.6.1
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 * @requirements SRS-UX-007 (Dark Mode Support)
 * @note Light mode is immutable default per ICTServe v3.6.0
 */
--}}
<x-filament-widgets::widget>
    <div class="flex items-center justify-end p-2">
        <div x-data="{
            theme: 'light',
            init() {
                // Sync with global theme API
                if (window.ICTServeTheme) {
                    this.theme = window.ICTServeTheme.get();
                }
        
                // Listen for theme changes from other components
                window.addEventListener('theme-changed', (e) => {
                    this.theme = e.detail?.theme || 'light';
                });
            },
            toggleTheme() {
                if (window.ICTServeTheme) {
                    const newTheme = window.ICTServeTheme.toggle();
                    this.theme = newTheme;
                    $wire.setTheme(newTheme);
                }
            }
        }">
            <button type="button" @click="toggleTheme()" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg
                       bg-gray-100 dark:bg-gray-800
                       text-gray-700 dark:text-gray-200
                       hover:bg-gray-200 dark:hover:bg-gray-700
                       focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2
                       dark:focus:ring-offset-gray-900
                       transition-colors duration-200
                       min-h-44 min-w-44"
                :aria-label="'{{ __('Tukar tema') }} - ' + (theme === 'dark' ? '{{ __('Gelap') }}' : '{{ __('Terang') }}')"
                aria-live="polite" :aria-pressed="theme === 'dark' ? 'true' : 'false'">

                {{-- Sun icon (shown in dark mode to switch to light) --}}
                <svg x-show="theme === 'dark'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>

                {{-- Moon icon (shown in light mode to switch to dark) --}}
                <svg x-show="theme !== 'dark'" class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>

                <span class="text-xs font-medium">
                    <span x-text="theme === 'dark' ? '{{ __('Cahaya') }}' : '{{ __('Gelap') }}'"></span>
                </span>
            </button>
        </div>
    </div>
</x-filament-widgets::widget>
