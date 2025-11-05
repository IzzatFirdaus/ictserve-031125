<?php
/**
 * Portal Navigation Component
 *
 * Volt component for main staff/portal navigation bar with responsive mobile menu,
 * language switcher, and user authentication controls. Implements WCAG 2.2 AA
 * accessibility standards with proper ARIA landmarks and keyboard navigation.
 *
 * @component PortalNavigation
 * @trace D03-FR-001.1 (Authentication), D04 §6.1 (Layout Components), D12 §9 (WCAG Compliance)
 * @wcag-level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.1.1)
 * @version 2.0.0 (Volt+Blade hybrid implementation)
 * @author Pasukan BPM MOTAC
 * @created 2025-11-05
 */

use Livewire\Volt\Component;

new class extends Component {
    /**
     * Portal navigation links for authenticated staff
     *
     * @return array<int, array<string, string>>
     */
    public function getPortalLinks(): array
    {
        return [
            [
                'label' => __('staff.nav.dashboard'),
                'route' => 'staff.dashboard',
            ],
            [
                'label' => __('staff.nav.helpdesk'),
                'route' => 'helpdesk.authenticated.dashboard',
            ],
            [
                'label' => __('staff.nav.loans'),
                'route' => 'loan.authenticated.dashboard',
            ],
        ];
    }

    /**
     * Filter portal links by available routes
     *
     * @return array<int, array<string, string>>
     */
    public function getAvailableLinks(): array
    {
        return array_filter($this->getPortalLinks(), function (array $link): bool {
            return \Illuminate\Support\Facades\Route::has($link['route']);
        });
    }
};
?>

@php
    $user = auth()->user();
    $portalLinks = $this->getAvailableLinks();
    $isCurrentRoute = fn(string $route): bool => request()->routeIs($route);
@endphp

<header class="bg-slate-900 text-slate-100 border-b border-slate-800" role="banner" aria-label="{{ __('common.site_header') }}">
    {{-- Desktop/Tablet Navigation --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        {{-- Logo and Primary Navigation --}}
        <div class="flex items-center gap-8">
            {{-- Logo/Branding --}}
            <a href="{{ route('staff.dashboard') }}"
                class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 rounded-md transition-colors duration-150"
                wire:navigate
                aria-label="{{ __('common.site_home') }}">
                <x-application-logo class="h-8 w-auto text-slate-100" />
                <span class="text-lg font-semibold hidden sm:block">{{ config('app.name', 'ICTServe') }}</span>
            </a>

            {{-- Main Navigation Links (Desktop Only) --}}
            <nav id="sidebar-navigation"
                class="hidden md:flex items-center gap-6 text-sm font-medium"
                role="navigation"
                aria-label="{{ __('common.main_navigation') }}"
                tabindex="-1">
                @foreach ($portalLinks as $link)
                    <a href="{{ route($link['route']) }}"
                        wire:navigate
                        class="px-1 pb-1 border-b-2 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 rounded-sm {{ $isCurrentRoute($link['route']) ? 'border-blue-500 text-white font-semibold' : 'border-transparent text-slate-300 hover:text-white hover:border-slate-500' }}"
                        @if ($isCurrentRoute($link['route'])) aria-current="page" @endif>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Right-side Actions (User Menu & Language Switcher) --}}
        <div class="flex items-center gap-4" id="user-menu">
            {{-- Language Switcher Component (Blade) --}}
            <x-accessibility.language-switcher variant="dark" />

            {{-- User Dropdown Menu --}}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-slate-800 text-slate-200 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition-colors duration-150 min-h-[44px]"
                        aria-haspopup="menu"
                        aria-label="{{ __('staff.nav.user_menu') }}: ' . ($user?->name ?? __('common.user')) }}">
                        <span>{{ $user?->name ?? __('staff.nav.user_menu') }}</span>
                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('staff.profile')" wire:navigate>
                        {{ __('staff.nav.profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('staff.nav.logout') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    {{-- Mobile Navigation Menu --}}
    <nav x-data="{ mobileMenuOpen: false }"
        class="md:hidden border-t border-slate-800"
        role="navigation"
        aria-label="{{ __('common.mobile_navigation') }}">

        {{-- Mobile Menu Toggle Button --}}
        <button type="button"
            class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-slate-200 bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset transition-colors duration-150 min-h-[44px]"
            @click="mobileMenuOpen = !mobileMenuOpen"
            :aria-expanded="mobileMenuOpen.toString()"
            aria-controls="mobile-menu"
            aria-label="{{ __('staff.nav.toggle_menu') }}">
            <span>{{ __('staff.nav.menu') }}</span>
            <svg class="h-5 w-5 transition-transform duration-150"
                :class="{ '-rotate-180': mobileMenuOpen }"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        {{-- Mobile Menu Items --}}
        <div id="mobile-menu"
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="bg-slate-900 border-t border-slate-800"
            style="display: none;">

            {{-- Primary Portal Links --}}
            @foreach ($portalLinks as $link)
                <a href="{{ route($link['route']) }}"
                    wire:navigate
                    @click="mobileMenuOpen = false"
                    class="flex px-4 py-3 text-sm min-h-[44px] items-center focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150 {{ $isCurrentRoute($link['route']) ? 'text-white bg-slate-800 font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                    @if ($isCurrentRoute($link['route'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach

            {{-- Profile Link --}}
            <a href="{{ route('staff.profile') }}"
                wire:navigate
                @click="mobileMenuOpen = false"
                class="flex px-4 py-3 text-sm min-h-[44px] items-center text-slate-300 hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150">
                {{ __('staff.nav.profile') }}
            </a>

            {{-- Logout Form --}}
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-800">
                @csrf
                <button type="submit"
                    class="w-full text-left px-4 py-3 text-sm min-h-[44px] flex items-center text-slate-300 hover:bg-slate-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors duration-150">
                    {{ __('staff.nav.logout') }}
                </button>
            </form>
        </div>
    </nav>
</header>
