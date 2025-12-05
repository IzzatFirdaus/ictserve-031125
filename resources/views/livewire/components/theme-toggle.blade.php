{{--
    Theme Toggle Component
    @component ThemeToggle
    @description Toggle between light, dark, and system theme preferences
    @trace D12 §6.10, D14 §6.1.2, D14 §8.1
    @wcag SC 1.4.3 Contrast, SC 2.1.1 Keyboard, SC 2.4.7 Focus Visible
    @requirements 25.4, 25.5
--}}
<div class="relative" x-data="{
    theme: @entangle('theme'),
    isOpen: @entangle('isOpen'),
    applyTheme(newTheme) {
        const root = document.documentElement;
        const body = document.body;

        // Remove existing theme classes
        root.classList.remove('dark', 'light');
        root.removeAttribute('data-theme');

        if (newTheme === 'system') {
            // Check system preference
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                root.classList.add('dark');
                root.setAttribute('data-theme', 'dark');
            }
        } else if (newTheme === 'dark') {
            root.classList.add('dark');
            root.setAttribute('data-theme', 'dark');
        }

        // Add transition class for smooth theme change
        body.classList.add('theme-transition');
        setTimeout(() => body.classList.remove('theme-transition'), 200);
    },
    init() {
        // Apply initial theme
        this.applyTheme(this.theme);

        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (this.theme === 'system') {
                this.applyTheme('system');
            }
        });
    }
}" x-init="init()"
    @theme-changed.window="applyTheme($event.detail.theme)" @click.away="isOpen = false"
    @keydown.escape.window="isOpen = false">

    {{-- Toggle Button --}}
    <button type="button" wire:click="toggleDropdown"
        class="relative inline-flex items-center justify-center p-2 rounded-lg
                   text-gray-500 hover:text-gray-700 hover:bg-gray-100
                   dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700
                   focus:outline-none focus:ring-3 focus:ring-primary-500 focus:ring-offset-2
                   dark:focus:ring-offset-gray-900
                   transition-colors duration-200 ease-out
                   touch-target"
        :aria-expanded="isOpen" aria-haspopup="listbox"
        aria-label="{{ __('Theme preference') }}: {{ $this->getCurrentLabel() }}">

        {{-- Sun Icon (Light) --}}
        <svg x-show="theme === 'light'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 rotate-90 scale-0"
            x-transition:enter-end="opacity-100 rotate-0 scale-100" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>

        {{-- Moon Icon (Dark) --}}
        <svg x-show="theme === 'dark'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 rotate-90 scale-0"
            x-transition:enter-end="opacity-100 rotate-0 scale-100" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
        </svg>

        {{-- Computer Icon (System) --}}
        <svg x-show="theme === 'system'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 rotate-90 scale-0"
            x-transition:enter-end="opacity-100 rotate-0 scale-100" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
        </svg>

        <span class="sr-only">{{ __('Current theme') }}: {{ $this->getCurrentLabel() }}</span>
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-36 origin-top-right rounded-lg
                bg-white dark:bg-gray-800
                shadow-dropdown
                ring-1 ring-black ring-opacity-5 dark:ring-gray-700
                focus:outline-none"
        role="listbox" aria-label="{{ __('Select theme') }}" @keydown.arrow-down.prevent="$focus.wrap().next()"
        @keydown.arrow-up.prevent="$focus.wrap().previous()">

        <div class="py-1">
            @foreach ($themes as $key => $themeOption)
                <button type="button" wire:click="setTheme('{{ $key }}')"
                    class="w-full flex items-center gap-3 px-4 py-2 text-sm
                               {{ $theme === $key
                                   ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300'
                                   : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}
                               focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700
                               transition-colors duration-150"
                    role="option" :aria-selected="{{ $theme === $key ? 'true' : 'false' }}">

                    {{-- Theme Icon --}}
                    @if ($themeOption['icon'] === 'sun')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    @elseif($themeOption['icon'] === 'moon')
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                    @endif

                    <span>{{ __($themeOption['label']) }}</span>

                    {{-- Checkmark for selected --}}
                    @if ($theme === $key)
                        <svg class="w-4 h-4 ml-auto text-primary-600 dark:text-primary-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
