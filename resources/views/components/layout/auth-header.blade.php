{{--
/**
 * Component: Authenticated Portal Header
 * Description: WCAG 2.2 AA compliant header with MOTAC branding, notifications, language switcher, and user menu
 * Author: Pasukan BPM MOTAC
 * @trace D03-FR-018.2 (Authenticated Header Component)
 * @trace D03-FR-018.4 (Notification System)
 * @trace D03-FR-019.4 (Real-time Updates)
 * @trace D04 §6.1 (Layout Components)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @wcag WCAG 2.2 Level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.8)
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 2.0.0
 * @created 2025-11-03
 * @updated 2025-11-05
 */
--}}

@props(['user'])

@php
    // Get unread notifications count (will be implemented with NotificationCenter)
    $unreadCount = 0; // Placeholder for now

    $dashboardUrl = Route::has('staff.dashboard')
        ? route('staff.dashboard')
        : (Route::has('dashboard') ? route('dashboard') : url('/'));

    $staffTicketsUrl = Route::has('staff.tickets.index') ? route('staff.tickets.index') : null;
    $staffLoansUrl = Route::has('staff.loans.index') ? route('staff.loans.index') : null;
    $staffApprovalsUrl = Route::has('staff.approvals.index') ? route('staff.approvals.index') : null;
    $staffProfileUrl = Route::has('staff.profile')
        ? route('staff.profile')
        : (Route::has('profile') ? route('profile') : null);
    $staffSettingsUrl = Route::has('staff.settings') ? route('staff.settings') : null;
@endphp

<header class="bg-white shadow-md border-b border-gray-200" role="banner" aria-label="{{ __('common.site_header') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- MOTAC Branding with Jata Negara --}}
            <div class="flex items-center space-x-4">
                {{-- Jata Negara (Malaysian Coat of Arms) --}}
                <img src="{{ asset('images/jata-negara.png') }}" alt="{{ __('common.jata_negara') }}" class="h-12 w-auto"
                    loading="eager">

                {{-- MOTAC Logo and Name --}}
                <a href="{{ $dashboardUrl }}"
                    class="flex items-center space-x-3 focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 rounded-md p-2 transition-all"
                    aria-label="{{ __('common.staff_dashboard') }}">
                    <x-application-logo class="h-10 w-auto text-motac-blue" />
                    <div class="hidden sm:block">
                        <div class="text-lg font-bold text-gray-900">{{ config('app.name', 'ICTServe') }}</div>
                        <div class="text-xs text-gray-600">{{ __('common.staff_portal') }}</div>
                    </div>
                </a>
            </div>

            {{-- Main Navigation --}}
            <nav class="hidden md:flex space-x-8" role="navigation" aria-label="{{ __('Main navigation') }}">
                <a href="{{ $dashboardUrl }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.dashboard') ? 'page' : 'false' }}">
                    {{ __('Dashboard') }}
                </a>

                @if ($staffTicketsUrl)
                <a href="{{ $staffTicketsUrl }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.tickets.*') ? 'page' : 'false' }}">
                    {{ __('My Tickets') }}
                </a>
                @endif

                @if ($staffLoansUrl)
                <a href="{{ $staffLoansUrl }}"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                    aria-current="{{ request()->routeIs('staff.loans.*') ? 'page' : 'false' }}">
                    {{ __('My Loans') }}
                </a>
                @endif

                @if ($user->canApprove() && $staffApprovalsUrl)
                    <a href="{{ $staffApprovalsUrl }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 rounded-md transition-colors duration-200"
                        aria-current="{{ request()->routeIs('staff.approvals.*') ? 'page' : 'false' }}">
                        {{ __('Approvals') }}
                    </a>
                @endif
            </nav>

            {{-- Right Side Actions --}}
            <div class="flex items-center space-x-2 sm:space-x-4"></div>
            {{-- Language Switcher --}}
            <x-accessibility.language-switcher />

            {{-- Notification Bell with Badge (wire:poll.30s for real-time updates) --}}
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="relative p-2 text-gray-600 hover:text-motac-blue hover:bg-gray-100 rounded-full focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px] flex items-center justify-center transition-all"
                    aria-label="{{ __('common.notifications') }} @if ($unreadCount > 0) ({{ $unreadCount }} {{ __('common.unread') }}) @endif"
                    aria-expanded="false" aria-haspopup="menu" :aria-expanded="open.toString()">
                    {{-- Bell Icon --}}
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    {{-- Unread Badge --}}
                    @if ($unreadCount > 0)
                        <span
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-danger rounded-full min-w-[20px] min-h-[20px]"
                            aria-label="{{ $unreadCount }} {{ __('common.unread_notifications') }}">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                {{-- Notifications Dropdown (will be enhanced with NotificationCenter component) --}}
                <div x-show="open" @click.away="open = false" @keydown.escape.window="open = false" x-cloak
                    class="absolute right-0 mt-2 w-80 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 max-h-96 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('common.notifications') }}</h3>
                    </div>
                    <div class="py-2">
                        @if ($unreadCount > 0)
                            {{-- Placeholder for notifications list --}}
                            <div class="px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                {{ __('common.notifications_will_appear_here') }}
                            </div>
                        @else
                            <div class="px-4 py-8 text-center text-sm text-gray-500">
                                {{ __('common.no_new_notifications') }}
                            </div>
                        @endif
                    </div>
                    @if (Route::has('staff.notifications'))
                        <div class="p-2 border-t border-gray-200">
                            <a href="{{ route('staff.notifications') }}"
                                class="block px-4 py-2 text-sm text-center text-motac-blue hover:bg-gray-50 rounded-md focus:outline-none focus:ring-2 focus:ring-motac-blue min-h-[44px] flex items-center justify-center">
                                {{ __('common.view_all_notifications') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Menu --}}
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" id="user-menu-button"
                    class="flex items-center space-x-2 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-motac-blue hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] transition-all"
                    aria-expanded="false" aria-haspopup="menu" aria-controls="user-menu"
                    aria-label="{{ __('common.user_menu') }}" :aria-expanded="open.toString()">
                    {{-- User Avatar --}}
                    <div
                        class="h-8 w-8 rounded-full bg-motac-blue text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <span class="hidden sm:block">{{ $user->name }}</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" @click.away="open = false" @keydown.escape.window="open = false" x-cloak
                    id="user-menu" role="menu" aria-labelledby="user-menu-button"
                    class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        {{-- User Info --}}
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-motac-blue-light text-motac-blue">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>

                        {{-- Menu Items --}}
                        @if ($staffProfileUrl)
                            <a href="{{ $staffProfileUrl }}" role="menuitem"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-motac-blue min-h-[44px] flex items-center">
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('common.profile') }}
                            </a>
                        @endif

                        @if ($staffSettingsUrl)
                            <a href="{{ $staffSettingsUrl }}" role="menuitem"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-motac-blue min-h-[44px] flex items-center">
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 00-1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ __('common.settings') }}
                            </a>
                        @endif

                        <div class="border-t border-gray-200"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-motac-blue min-h-[44px] flex items-center">
                                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('common.logout') }}
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
            <a href="{{ $dashboardUrl }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.dashboard') ? 'page' : 'false' }}">
                {{ __('Dashboard') }}
            </a>

            @if ($staffTicketsUrl)
            <a href="{{ $staffTicketsUrl }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.tickets.*') ? 'page' : 'false' }}">
                {{ __('My Tickets') }}
            </a>
            @endif

            @if ($staffLoansUrl)
            <a href="{{ $staffLoansUrl }}"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                aria-current="{{ request()->routeIs('staff.loans.*') ? 'page' : 'false' }}">
                {{ __('My Loans') }}
            </a>
            @endif

            @if ($user->canApprove() && $staffApprovalsUrl)
                <a href="{{ $staffApprovalsUrl }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]"
                    aria-current="{{ request()->routeIs('staff.approvals.*') ? 'page' : 'false' }}">
                    {{ __('Approvals') }}
                </a>
            @endif

            @if ($staffProfileUrl)
                <a href="{{ $staffProfileUrl }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-600 min-h-[44px]">
                    {{ __('common.profile') }}
                </a>
            @endif

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
