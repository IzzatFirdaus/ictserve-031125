@php
    $user = auth()->user();
    $portalLinks = [
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
@endphp

<header class="bg-slate-900 text-slate-100 border-b border-slate-800" role="banner" aria-label="{{ __('common.site_header') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-8">
            <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 rounded-md" wire:navigate>
                <x-application-logo class="h-8 w-auto text-slate-100" />
                <span class="text-lg font-semibold hidden sm:block">{{ config('app.name', 'ICTServe') }}</span>
            </a>

            <nav id="sidebar-navigation" class="hidden md:flex items-center gap-6 text-sm font-medium" role="navigation" aria-label="{{ __('common.main_navigation') }}" tabindex="-1">
                @foreach ($portalLinks as $link)
                    @continue(! Route::has($link['route']))
                    <a href="{{ route($link['route']) }}" wire:navigate
                        class="px-1 pb-1 border-b-2 transition-colors {{ request()->routeIs($link['route']) ? 'border-blue-500 text-white' : 'border-transparent text-slate-300 hover:text-white hover:border-slate-500' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="flex items-center gap-4" id="user-menu">
            <x-accessibility.language-switcher variant="dark" />

            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-slate-800 text-slate-200 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition">
                        <span>{{ $user?->name ?? __('staff.nav.user_menu') }}</span>
                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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

    <nav x-data="{ open: false }" class="md:hidden border-t border-slate-800" role="navigation" aria-label="{{ __('common.mobile_navigation') }}">
        <button type="button"
            class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-slate-200 bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900"
            @click="open = !open"
            :aria-expanded="open.toString()">
            <span>{{ __('staff.nav.menu') }}</span>
            <svg class="h-5 w-5 transition-transform" :class="{ '-rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div x-show="open" x-transition class="bg-slate-900 border-t border-slate-800">
            @foreach ($portalLinks as $link)
                @continue(! Route::has($link['route']))
                <a href="{{ route($link['route']) }}" wire:navigate
                    class="block px-4 py-3 text-sm {{ request()->routeIs($link['route']) ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('staff.profile') }}" wire:navigate class="block px-4 py-3 text-sm text-slate-300 hover:bg-slate-800 hover:text-white">
                {{ __('staff.nav.profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-800">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-slate-300 hover:bg-slate-800 hover:text-white">
                    {{ __('staff.nav.logout') }}
                </button>
            </form>
        </div>
    </nav>
</header>
