{{--
/**
 * Component name: Header Layout
 * Description: Guest header with MOTAC branding, primary navigation, and language switcher.
 * Author: Pasukan BPM MOTAC
 * References: D03-FR-006.1, D03-FR-018.1, D04 section 6.1, D10 section 7, D12 section 9, D14 section 8
 * WCAG: 2.2 Level AA
 * Version: 1.0.0 (2025-11-03)
 */
--}}

@php
    $helpdeskRouteName = collect(['helpdesk.submit', 'helpdesk.create'])->first(fn(string $name) => Route::has($name));
    $loanRouteName = collect(['loans.create', 'loan.guest.create', 'loan.authenticated.create'])->first(
        fn(string $name) => Route::has($name),
    );
    $adminLoginRoute = Route::has('filament.admin.auth.login') ? 'filament.admin.auth.login' : null;
@endphp

<header class="bg-white shadow-md" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo and Branding --}}
            <div class="flex items-center">
                <a href="{{ route('welcome') }}"
                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md p-2"
                    aria-label="{{ __('common.home') }}">
                    <x-application-logo class="h-10 w-auto" />
                    <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Main Navigation --}}
            <nav class="hidden md:flex space-x-8" role="navigation" aria-label="{{ __('common.main_navigation') }}">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                    {{ __('common.home') }}
                </a>

                <a href="{{ route('services') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('services') ? 'page' : 'false' }}">
                    {{ __('common.services') ?? 'Services' }}
                </a>

                <a href="{{ route('contact') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}">
                    {{ __('common.contact') ?? 'Contact' }}
                </a>

                <a href="{{ route('accessibility') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('accessibility') ? 'page' : 'false' }}">
                    {{ __('common.accessibility') ?? 'Accessibility' }}
                </a>
            </nav>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-3">
                {{-- Language Switcher --}}
                <x-accessibility.language-switcher />

                {{-- Login Link --}}
                @guest
                    @if ($adminLoginRoute)
                        <x-ui.button variant="ghost" :href="route($adminLoginRoute)">
                            {{ __('common.admin_login') }}
                        </x-ui.button>
                    @endif

                    <x-ui.button :href="route('login')">
                        {{ __('common.staff_login') }}
                    </x-ui.button>
                @else
                    <a href="{{ route('staff.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200 min-h-[44px]">
                        {{ __('common.dashboard') }}
                    </a>
                @endguest
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 min-h-[44px] min-w-[44px]"
                    @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu"
                    aria-label="{{ __('common.toggle_navigation_menu') }}">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Mobile Menu --}}
                <div class="md:hidden" id="mobile-menu" x-show="open" x-transition x-cloak>
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3" role="navigation"
                        aria-label="{{ __('common.mobile_navigation') }}">
                        <a href="{{ route('welcome') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                            aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                            {{ __('common.home') }}
                        </a>

                        <a href="{{ route('services') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                            aria-current="{{ request()->routeIs('services') ? 'page' : 'false' }}">
                            {{ __('common.services') ?? 'Services' }}
                        </a>

                        <a href="{{ route('contact') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                            aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}">
                            {{ __('common.contact') ?? 'Contact' }}
                        </a>

                        <a href="{{ route('accessibility') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                            aria-current="{{ request()->routeIs('accessibility') ? 'page' : 'false' }}">
                            {{ __('common.accessibility') ?? 'Accessibility' }}
                        </a>

                        @guest
                            @if ($adminLoginRoute)
                                <a href="{{ route($adminLoginRoute) }}"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]">
                                    {{ __('common.admin_login') }}
                                </a>
                            @endif

                            <a href="{{ route('login') }}"
                                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]">
                                {{ __('common.staff_login') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
