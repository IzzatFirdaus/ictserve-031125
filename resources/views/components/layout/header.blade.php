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
    $isHelpdeskNavigation = request()->routeIs('helpdesk.*');
    $headerClasses = $isHelpdeskNavigation
        ? 'bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-900 shadow-[0_4px_20px_rgba(2,6,23,0.6)]'
        : 'bg-white shadow-md';
    $linkColorClasses = $isHelpdeskNavigation
        ? 'text-slate-100 hover:text-blue-200 focus:ring-blue-200'
        : 'text-gray-700 hover:text-blue-600 focus:ring-blue-600';
    $buttonBgClasses = $isHelpdeskNavigation
        ? 'bg-white text-slate-900 hover:bg-slate-100 focus:ring-blue-200'
        : 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-600';
    $ghostButtonClasses = $isHelpdeskNavigation
        ? 'text-slate-100 hover:text-white focus:ring-blue-200'
        : 'text-gray-700 hover:text-blue-600 focus:ring-blue-600';
@endphp

<header class="{{ $headerClasses }}" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo and Branding --}}
            <div class="flex items-center">
                <a href="{{ route('welcome') }}"
                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md p-2"
                    aria-label="{{ __('common.home') }}">
                    <x-application-logo class="h-10 w-auto" />
                    <span
                        class="ml-3 text-xl font-bold transition-colors duration-200 {{ $isHelpdeskNavigation ? 'text-slate-100' : 'text-gray-900' }}">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Main Navigation --}}
            <nav class="hidden md:flex space-x-6" role="navigation" aria-label="{{ __('common.main_navigation') }}">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }}"
                    aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                    {{ __('common.home') }}
                </a>

                <a href="{{ route('services') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }}"
                    aria-current="{{ request()->routeIs('services') ? 'page' : 'false' }}">
                    {{ __('common.services') ?? 'Services' }}
                </a>

                <a href="{{ route('contact') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }}"
                    aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}">
                    {{ __('common.contact') ?? 'Contact' }}
                </a>

                <a href="{{ route('accessibility') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }}"
                    aria-current="{{ request()->routeIs('accessibility') ? 'page' : 'false' }}">
                    {{ __('common.accessibility') ?? 'Accessibility' }}
                </a>
            </nav>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-3">
                {{-- Language Switcher --}}
                <x-accessibility.language-switcher :variant="$isHelpdeskNavigation ? 'dark' : 'light'" />

                {{-- Login Link --}}
                @guest
                    @if ($adminLoginRoute)
                        <a href="{{ route($adminLoginRoute) }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $ghostButtonClasses }}">
                            {{ __('common.admin_login') }}
                        </a>
                    @endif

                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $buttonBgClasses }}">
                        {{ __('common.staff_login') }}
                    </a>
                @else
                    <a href="{{ route('staff.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $buttonBgClasses }}">
                        {{ __('common.dashboard') }}
                    </a>
                @endguest
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md transition-colors duration-150 min-h-[44px] min-w-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $ghostButtonClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-800' : 'hover:bg-gray-100' }}"
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
                            class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900' : 'hover:bg-gray-50' }}"
                            aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                            {{ __('common.home') }}
                        </a>

                        <a href="{{ route('services') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900' : 'hover:bg-gray-50' }}"
                            aria-current="{{ request()->routeIs('services') ? 'page' : 'false' }}">
                            {{ __('common.services') ?? 'Services' }}
                        </a>

                        <a href="{{ route('contact') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900' : 'hover:bg-gray-50' }}"
                            aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}">
                            {{ __('common.contact') ?? 'Contact' }}
                        </a>

                        <a href="{{ route('accessibility') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $linkColorClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900' : 'hover:bg-gray-50' }}"
                            aria-current="{{ request()->routeIs('accessibility') ? 'page' : 'false' }}">
                            {{ __('common.accessibility') ?? 'Accessibility' }}
                        </a>

                        @guest
                            @if ($adminLoginRoute)
                                <a href="{{ route($adminLoginRoute) }}"
                                    class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $ghostButtonClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900' : 'hover:bg-gray-50' }}">
                                    {{ __('common.admin_login') }}
                                </a>
                            @endif

                            <a href="{{ route('login') }}"
                                class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200 min-h-[44px] focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $buttonBgClasses }} {{ $isHelpdeskNavigation ? 'hover:bg-slate-900/60' : 'hover:bg-gray-50' }}">
                                {{ __('common.staff_login') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
