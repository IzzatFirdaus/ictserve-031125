{{--
/**
 * Enhanced Theme Toggle Widget for Filament Admin Panel (v3.6.1)
 *
 * WCAG 2.2 AA compliant theme toggle with comprehensive theme management,
 * MyDS color system integration, and accessibility features.
 *
 * @component ThemeToggleWidget
 * @version 3.6.1
 * @trace D12 §4 (Color System), D14 §2 (WCAG 2.2 AA), D15 (Bahasa Melayu)
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 * @requirements R15 (Color System), R16 (WCAG Dark Mode)
 */
--}}
<x-filament-widgets::widget>
    <x-filament.components::widget-card title="{{ __('Tetapan Tema') }}"
        description="{{ __('Kawalan tema dashboard dengan aksesibiliti WCAG 2.2 AA') }}" icon="heroicon-o-swatch"
        color="primary" :interactive="false">

        <div x-data="themeToggleWidget()" x-init="init()" role="region" aria-label="{{ __('Kawalan Tema Dashboard') }}">

            {{-- Accessibility Info Button --}}
            <div class="flex justify-end mb-4">
                <button type="button" @click="showAccessibilityInfo = !showAccessibilityInfo"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    :aria-expanded="showAccessibilityInfo" aria-label="{{ __('Maklumat Aksesibiliti') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>

            {{-- Theme Preference Selection --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Pilihan Tema') }}
                    </label>

                    <div class="grid grid-cols-3 gap-2" role="radiogroup" aria-label="{{ __('Pilihan Tema') }}">
                        {{-- Light Theme --}}
                        <button type="button" @click="setThemePreference('light')" wire:loading.attr="disabled"
                            class="theme-option-button" :class="{ 'active': themePreference === 'light' }"
                            role="radio" :aria-checked="themePreference === 'light'"
                            aria-label="{{ __('Tema Terang') }}">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <span class="text-xs font-medium">{{ __('Terang') }}</span>
                        </button>

                        {{-- Dark Theme --}}
                        <button type="button" @click="setThemePreference('dark')" wire:loading.attr="disabled"
                            class="theme-option-button" :class="{ 'active': themePreference === 'dark' }"
                            role="radio" :aria-checked="themePreference === 'dark'"
                            aria-label="{{ __('Tema Gelap') }}">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                            <span class="text-xs font-medium">{{ __('Gelap') }}</span>
                        </button>

                        {{-- System Theme --}}
                        <button type="button" @click="setThemePreference('system')" wire:loading.attr="disabled"
                            class="theme-option-button" :class="{ 'active': themePreference === 'system' }"
                            role="radio" :aria-checked="themePreference === 'system'"
                            aria-label="{{ __('Tema Sistem') }}">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-xs font-medium">{{ __('Sistem') }}</span>
                        </button>
                    </div>
                </div>

                {{-- High Contrast Toggle --}}
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div>
                        <label for="high-contrast-toggle" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Mod Kontras Tinggi') }}
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Tingkatkan kontras untuk aksesibiliti yang lebih baik') }}
                        </p>
                    </div>

                    <button type="button" id="high-contrast-toggle" @click="toggleHighContrast()"
                        wire:loading.attr="disabled"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        :class="highContrastMode ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'" role="switch"
                        :aria-checked="highContrastMode" aria-label="{{ __('Togol Mod Kontras Tinggi') }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            :class="highContrastMode ? 'translate-x-6' : 'translate-x-1'"></span>
                    </button>
                </div>

                {{-- Current Theme Status --}}
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full"
                            :class="currentTheme === 'dark' ? 'bg-gray-800' : 'bg-yellow-400'"></div>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            {{ __('Tema Semasa') }}:
                            <span x-text="getThemeDisplayName(currentTheme)"></span>
                        </span>
                    </div>

                    <div x-show="themePreference === 'system'" class="mt-2 text-xs text-blue-600 dark:text-blue-300">
                        <span class="flex items-center space-x-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('Mengikut tetapan sistem') }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Accessibility Information Panel --}}
            <div x-show="showAccessibilityInfo" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-1 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-1 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800"
                role="region" aria-label="{{ __('Maklumat Aksesibiliti') }}">

                <h4 class="text-sm font-semibold text-green-800 dark:text-green-200 mb-2">
                    {{ __('Maklumat Aksesibiliti') }}
                </h4>

                <div class="space-y-2 text-xs text-green-700 dark:text-green-300">
                    <div class="flex items-center justify-between">
                        <span>{{ __('Pematuhan WCAG 2.2 AA') }}:</span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('Mematuhi') }}</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span>{{ __('Nisbah Kontras Teks') }}:</span>
                        <span>{{ __('4.5:1 (Minimum)') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span>{{ __('Nisbah Kontras UI') }}:</span>
                        <span>{{ __('3:1 (Minimum)') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span>{{ __('Navigasi Papan Kekunci') }}:</span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('Disokong') }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div wire:loading
                class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 rounded-lg flex items-center justify-center">
                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span>{{ __('Mengemas kini tema...') }}</span>
                </div>
            </div>
        </div>
    </x-filament.components::widget-card>

    {{-- Alpine.js Component --}}
    <script>
        function themeToggleWidget() {
            return {
                themePreference: @entangle('themePreference'),
                currentTheme: @entangle('theme'),
                highContrastMode: @entangle('highContrastMode'),
                systemThemeDetected: @entangle('systemThemeDetected'),
                showAccessibilityInfo: false,

                init() {
                    // Apply initial theme
                    this.applyTheme(this.currentTheme);

                    // Listen for system theme changes
                    if (window.matchMedia) {
                        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                        mediaQuery.addEventListener('change', (e) => {
                            if (this.themePreference === 'system') {
                                const systemTheme = e.matches ? 'dark' : 'light';
                                this.currentTheme = systemTheme;
                                this.applyTheme(systemTheme);
                            }
                        });
                    }

                    // Listen for Livewire events
                    this.$wire.on('theme-preference-changed', (event) => {
                        this.applyTheme(event.theme);

                        // Show success message
                        this.showNotification('success', 'Tema dikemas kini berjaya');
                    });

                    this.$wire.on('high-contrast-changed', (event) => {
                        this.applyHighContrast(event.enabled);

                        // Show success message
                        const message = event.enabled ?
                            'Mod kontras tinggi diaktifkan' :
                            'Mod kontras tinggi dimatikan';
                        this.showNotification('success', message);
                    });
                },

                setThemePreference(preference) {
                    this.$wire.setThemePreference(preference);
                },

                toggleHighContrast() {
                    this.$wire.toggleHighContrast();
                },

                applyTheme(theme) {
                    const root = document.documentElement;
                    const normalizedTheme = theme === 'dark' ? 'dark' : 'light';

                    // Update CSS classes
                    if (normalizedTheme === 'dark') {
                        root.classList.add('dark');
                        root.setAttribute('data-theme', 'dark');
                    } else {
                        root.classList.remove('dark');
                        root.setAttribute('data-theme', 'light');
                    }

                    // Apply high contrast if enabled
                    this.applyHighContrast(this.highContrastMode);

                    // Store in localStorage for persistence
                    try {
                        localStorage.setItem('theme', normalizedTheme);
                        localStorage.setItem('theme-preference', this.themePreference);
                    } catch (error) {
                        console.warn('Could not save theme to localStorage:', error);
                    }

                    // Dispatch global event
                    window.dispatchEvent(new CustomEvent('theme-changed', {
                        detail: {
                            theme: normalizedTheme,
                            preference: this.themePreference,
                            highContrast: this.highContrastMode
                        }
                    }));
                },

                applyHighContrast(enabled) {
                    const root = document.documentElement;

                    if (enabled) {
                        root.setAttribute('data-high-contrast', 'true');
                    } else {
                        root.removeAttribute('data-high-contrast');
                    }

                    // Store in localStorage
                    try {
                        localStorage.setItem('high-contrast', enabled ? 'true' : 'false');
                    } catch (error) {
                        console.warn('Could not save high contrast setting to localStorage:', error);
                    }
                },

                getThemeDisplayName(theme) {
                    const names = {
                        'light': '{{ __('Terang') }}',
                        'dark': '{{ __('Gelap') }}',
                        'system': '{{ __('Sistem') }}'
                    };
                    return names[theme] || theme;
                },

                showNotification(type, message) {
                    // Create and show notification
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
                        type === 'success' 
                            ? 'bg-green-500 text-white' 
                            : 'bg-red-500 text-white'
                    }`;
                    notification.textContent = message;
                    notification.setAttribute('role', 'alert');
                    notification.setAttribute('aria-live', 'polite');

                    document.body.appendChild(notification);

                    // Auto-remove after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 3000);
                }
            }
        }
    </script>

    {{-- CSS Styles --}}
    <style>
        .theme-option-button {
            @apply flex flex-col items-center justify-center p-3 rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-primary-300 dark:hover:border-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-all duration-200 cursor-pointer;
        }

        .theme-option-button.active {
            @apply border-primary-500 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300;
        }

        .theme-option-button:disabled {
            @apply opacity-50 cursor-not-allowed;
        }

        /* High contrast mode enhancements */
        [data-high-contrast="true"] .theme-option-button {
            @apply border-4;
        }

        [data-high-contrast="true"] .theme-option-button.active {
            @apply bg-black text-white border-white;
        }

        [data-theme="dark"][data-high-contrast="true"] .theme-option-button.active {
            @apply bg-white text-black border-black;
        }
    </style>
</x-filament-widgets::widget>
