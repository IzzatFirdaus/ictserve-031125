{{--
/**
 * Unified Theme Dropdown Component (v3.6.1)
 *
 * WCAG 2.2 AA compliant dropdown theme selector with keyboard navigation.
 * Provides light/dark mode selection with proper accessibility features.
 *
 * @component ThemeDropdownUnified
 * @version 3.6.1
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus), SC 4.1.2 (Name, Role, Value)
 * @requirements SRS-UX-007 (Dark Mode Support)
 * @note Light mode is immutable default per ICTServe v3.6.0
 */
--}}
@php
    $currentTheme = $theme ?? 'light';
@endphp

<div x-data="{
    open: false,
    theme: '{{ $currentTheme }}',
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
    setTheme(newTheme) {
        this.theme = newTheme;
        this.open = false;
        $wire.setTheme(newTheme);
    },
    handleKeydown(event) {
        if (event.key === 'Escape') {
            this.open = false;
        } else if (event.key === 'ArrowDown' && !this.open) {
            this.open = true;
            event.preventDefault();
        }
    }
}" class="relative">
    {{-- Theme Toggle Button (WCAG 2.2 AA: 44x44px minimum touch target) --}}
    <button @click="open = !open" @keydown="handleKeydown($event)" type="button"
        class="flex items-center justify-center w-11 h-11 rounded-lg 
               text-gray-600 dark:text-gray-300 
               hover:bg-gray-100 dark:hover:bg-gray-700 
               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 
               dark:focus:ring-offset-gray-800 
               transition-colors duration-200"
        :class="{ 'bg-gray-100 dark:bg-gray-700': open }"
        :aria-label="'{{ __('Pilihan tema') }} - ' + (theme === 'dark' ? '{{ __('Gelap') }}' : '{{ __('Terang') }}')"
        :aria-expanded="open.toString()" aria-haspopup="listbox">

        {{-- Sun icon (shown in dark mode to switch to light) --}}
        <svg x-show="theme === 'dark'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100"
            class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        {{-- Moon icon (shown in light mode to switch to dark) --}}
        <svg x-show="theme === 'light'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100" class="w-5 h-5"
            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false" @keydown.escape.window="open = false"
        class="absolute right-0 mt-2 w-40 origin-top-right rounded-lg 
               bg-white dark:bg-gray-800 
               shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 
               focus:outline-none z-50"
        role="listbox" aria-label="{{ __('Pilih tema') }}">
        <div class="py-1">
            {{-- Light Mode Option --}}
            <button @click="setTheme('light')" type="button"
                class="flex items-center w-full px-4 py-2 text-sm 
                       text-gray-700 dark:text-gray-200 
                       hover:bg-gray-100 dark:hover:bg-gray-700 
                       focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700
                       transition-colors duration-150"
                :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300': theme === 'light' }"
                role="option" :aria-selected="theme === 'light'">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>{{ __('Terang') }}</span>
                <svg x-show="theme === 'light'" class="w-4 h-4 ml-auto text-primary-600 dark:text-primary-400"
                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Dark Mode Option --}}
            <button @click="setTheme('dark')" type="button"
                class="flex items-center w-full px-4 py-2 text-sm 
                       text-gray-700 dark:text-gray-200 
                       hover:bg-gray-100 dark:hover:bg-gray-700 
                       focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700
                       transition-colors duration-150"
                :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300': theme === 'dark' }"
                role="option" :aria-selected="theme === 'dark'">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <span>{{ __('Gelap') }}</span>
                <svg x-show="theme === 'dark'" class="w-4 h-4 ml-auto text-primary-600 dark:text-primary-400"
                    fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Livewire event handler (inject once per page) --}}
@once
    <script>
        (function() {
            // Prevent multiple initializations
            if (window.__ictserveThemeDropdownHandler) return;
            window.__ictserveThemeDropdownHandler = true;

            // Listen for Livewire theme-changed events
            document.addEventListener('livewire:init', function() {
                if (window.Livewire) {
                    Livewire.on('theme-changed', function(data) {
                        const theme = data?.theme || data?.[0]?.theme || 'light';
                        if (window.ICTServeTheme) {
                            window.ICTServeTheme.set(theme);
                        }
                    });
                }
            });
        })
        ();
    </script>
@endonce
