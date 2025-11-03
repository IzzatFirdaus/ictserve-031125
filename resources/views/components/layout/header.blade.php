{{--
/**
 * Component: Guest Layout Header
 * Description: WCAG 2.2 AA compliant header for guest-accessible pages with MOTAC branding
 * Author: Pasukan BPM MOTAC
 * Requirements: 5.1, 6.1, 6.2, 6.3, 14.1, 19.5, 20.5
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.2)
 * Version: 1.0.0
 * Created: 2025-11-03
 * Last Updated: 2025-11-03
 */
--}}

<header class="bg-white shadow-md" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo and Branding --}}
            <div class="flex items-center">
                <a href="{{ route('welcome') }}"
                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md p-2"
                    aria-label="{{ __('ICTServe Home') }}">
                    <x-application-logo class="h-10 w-auto" />
                    <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Main Navigation --}}
            <nav class="hidden md:flex space-x-8" role="navigation" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                    {{ __('Home') }}
                </a>

                <a href="{{ route('helpdesk.create') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('helpdesk.*') ? 'page' : 'false' }}">
                    {{ __('Helpdesk') }}
                </a>

                <a href="{{ route('loans.create') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('loans.*') ? 'page' : 'false' }}">
                    {{ __('Asset Loan') }}
                </a>
            </nav>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-4">
                {{-- Language Switcher --}}
                <x-accessibility.language-switcher />

                {{-- Login Link --}}
                @guest
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200 min-h-[44px]">
                        {{ __('Staff Login') }}
                    </a>
                @else
                    <a href="{{ route('staff.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200 min-h-[44px]">
                        {{ __('Dashboard') }}
                    </a>
                @endguest
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden">
                <button type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-blue-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 min-h-[44px] min-w-[44px]"
                    aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('Toggle navigation menu') }}"
                    x-data="{ open: false }" @click="open = !open" :aria-expanded="open.toString()">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div class="md:hidden" id="mobile-menu" x-data="{ open: false }" x-show="open" x-cloak>
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3" role="navigation" aria-label="{{ __('Mobile navigation') }}">
            <a href="{{ route('welcome') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('welcome') ? 'page' : 'false' }}">
                {{ __('Home') }}
            </a>

            <a href="{{ route('helpdesk.create') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('helpdesk.*') ? 'page' : 'false' }}">
                {{ __('Helpdesk') }}
            </a>

            <a href="{{ route('loans.create') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('loans.*') ? 'page' : 'false' }}">
                {{ __('Asset Loan') }}
            </a>
        </div>
    </div>
</header>
