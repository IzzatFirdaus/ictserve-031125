{{--
/**
 * Component: Authenticated Layout Header
 * Description: WCAG 2.2 AA compliant header for authenticated staff portal with user menu
 * Author: Pasukan BPM MOTAC
 * Requirements: 1.3, 22.1, 22.2, 22.4, 6.1, 6.2, 6.3
 * WCAG Level: AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5)
 * Version: 1.0.0
 * Created: 2025-11-03
 * Last Updated: 2025-11-03
 */
--}}

@props(['user'])

<header class="bg-white shadow-md" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo and Branding --}}
            <div class="flex items-center">
                <a href="{{ route('staff.dashboard') }}"
                    class="flex items-center focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md p-2"
                    aria-label="{{ __('Staff Dashboard') }}">
                    <x-application-logo class="h-10 w-auto" />
                    <span class="ml-3 text-xl font-bold text-gray-900">{{ config('app.name', 'ICTServe') }}</span>
                </a>
            </div>

            {{-- Main Navigation --}}
            <nav class="hidden md:flex space-x-8" role="navigation" aria-label="{{ __('Main navigation') }}">
                <a href="{{ route('staff.dashboard') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.dashboard') ? 'page' : 'false' }}">
                    {{ __('Dashboard') }}
                </a>

                <a href="{{ route('staff.tickets.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.tickets.*') ? 'page' : 'false' }}">
                    {{ __('My Tickets') }}
                </a>

                <a href="{{ route('staff.loans.index') }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.loans.*') ? 'page' : 'false' }}">
                    {{ __('My Loans') }}
                </a>

                @if ($user->canApprove())
                    <a href="{{ route('staff.approvals.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                        aria-current="{{ request()->routeIs('staff.approvals.*') ? 'page' : 'false' }}">
                        {{ __('Approvals') }}
                    </a>
                @endif
            </nav>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-4">
                {{-- Language Switcher --}}
                <x-accessibility.language-switcher />

                {{-- User Menu --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button"
                        class="flex items-center space-x-2 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 min-h-[44px]"
                        aria-expanded="false" aria-haspopup="menu" aria-controls="user-menu"
                        aria-label="{{ __('User menu') }}" @click="open = !open" :aria-expanded="open.toString()">
                        <span>{{ $user->name }}</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open" @click.away="open = false" @keydown.escape.window="open = false" x-cloak
                        id="user-menu" role="menu"
                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a href="{{ route('staff.profile') }}" role="menuitem"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px] flex items-center">
                                {{ __('Profile') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" role="menuitem"
                                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px] flex items-center">
                                    {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
            <a href="{{ route('staff.dashboard') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.dashboard') ? 'page' : 'false' }}">
                {{ __('Dashboard') }}
            </a>

            <a href="{{ route('staff.tickets.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.tickets.*') ? 'page' : 'false' }}">
                {{ __('My Tickets') }}
            </a>

            <a href="{{ route('staff.loans.index') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.loans.*') ? 'page' : 'false' }}">
                {{ __('My Loans') }}
            </a>

            @if ($user->canApprove())
                <a href="{{ route('staff.approvals.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                    aria-current="{{ request()->routeIs('staff.approvals.*') ? 'page' : 'false' }}">
                    {{ __('Approvals') }}
                </a>
            @endif

            <a href="{{ route('staff.profile') }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]">
                {{ __('Profile') }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]">
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </div>
</header>
