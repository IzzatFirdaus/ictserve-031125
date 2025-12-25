{{--
    Mobile sidebar (hamburger menu) per D12 §5.3
    Responsive breakpoint: md: 768px per D14 §7.3
    Transitions: --duration-medium (400ms) per D12 §6.10
    Shadow: shadow-dropdown per D14 §7.5
--}}
<div x-show="sidebarOpen" class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true"
    aria-label="{{ __('Mobile navigation') }}" @keydown.escape.window="sidebarOpen = false" x-cloak>
    {{-- Off-canvas menu overlay with 400ms transition per D12 §6.10 --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-400"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600/75 dark:bg-gray-900/80"
        @click="sidebarOpen = false" aria-hidden="true"></div>

    {{-- Off-canvas menu panel with shadow-dropdown per D14 §7.5 --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-400 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-800 shadow-lg"
        x-trap.noscroll="sidebarOpen">
        {{-- Close button - 44×44px touch target per D12 §4.1 --}}
        <div class="absolute top-0 right-0 -mr-14 pt-2">
            <button @click="sidebarOpen = false" type="button"
                class="flex items-center justify-center h-11 w-11 rounded-full bg-gray-800/50 text-white hover:bg-gray-800/70 focus:outline-none focus-visible:ring-3 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-gray-800 transition-colors duration-150"
                aria-label="{{ __('Close sidebar') }}">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Sidebar content --}}
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="shrink-0 flex items-center px-4">
                <x-application-logo class="h-8 w-auto text-primary-600" />
                <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">ICTServe</span>
            </div>
            <nav class="mt-5 px-2 space-y-1" aria-label="{{ __('Mobile navigation menu') }}">
                <x-navigation.main-menu />
            </nav>
        </div>

        {{-- User info footer --}}
        @auth
            <div class="shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <span
                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                            <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </span>
                        </span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            {{ auth()->user()->name ?? __('User') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ auth()->user()->email ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        @endauth
    </div>

    <div class="shrink-0 w-14" aria-hidden="true"></div>
</div>

{{--
    Static sidebar for desktop per D14 §6.2
    Width: 256px expanded, 64px collapsed per D14 §6.2
    Icons: Heroicons w-5 h-5 per D14 §8.1
    Active state: --bg-primary-50 token per D13 §2.2
--}}
<div class="hidden md:flex md:shrink-0" x-data="{ collapsed: false }">
    <div class="flex flex-col transition-all duration-300 ease-out" :class="collapsed ? 'w-16' : 'w-64'">
        <div
            class="flex-1 flex flex-col min-h-0 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                {{-- Logo and brand --}}
                <div class="flex items-center shrink-0 px-4">
                    <x-application-logo class="h-8 w-auto text-primary-600 shrink-0" />
                    <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white transition-opacity duration-200"
                        :class="collapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'" x-show="!collapsed"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">ICTServe</span>
                </div>

                {{-- Navigation menu --}}
                <nav class="mt-5 flex-1 px-2 bg-white dark:bg-gray-800 space-y-1"
                    aria-label="{{ __('Main navigation') }}">
                    <x-navigation.main-menu :collapsed="false" x-bind:collapsed="collapsed" />
                </nav>
            </div>

            {{-- Collapse toggle button --}}
            <div class="shrink-0 flex border-t border-gray-200 dark:border-gray-700 p-2">
                <button @click="collapsed = !collapsed" type="button"
                    class="flex items-center justify-center w-full p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150 min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    :aria-expanded="!collapsed" aria-controls="sidebar-nav"
                    :aria-label="collapsed ? '{{ __('Expand sidebar') }}' : '{{ __('Collapse sidebar') }}'">
                    <svg class="h-5 w-5 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''"
                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-sm font-medium" x-show="!collapsed"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">{{ __('Collapse') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
