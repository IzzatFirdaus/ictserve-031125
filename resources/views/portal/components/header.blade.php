{{--
    Component: Portal Header
    Description: Top bar with branding, user menu, and utilities.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §3
    Version: 1.0.0
    Updated: 2025-12-20
--}}

@php
    $user = auth()->user();
    $displayName = $user?->name ?? __('staff.nav.user_menu');

    if (is_array($displayName)) {
        $displayName = $displayName['ms'] ?? $displayName['en'] ?? (array_values($displayName)[0] ?? __('staff.nav.user_menu'));
    }

    $displayName = is_string($displayName) ? $displayName : (string) $displayName;
    $initial = $displayName !== '' ? mb_substr($displayName, 0, 1) : 'U';
@endphp

<header class="bg-slate-950/90 border-b border-slate-800" role="banner">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 py-3">
            <div class="flex items-center gap-3">
                <button type="button" class="md:hidden min-h-11 min-w-11 rounded-lg border border-slate-800 text-slate-200 hover:bg-slate-900 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500"
                    @click="sidebarOpen = true" aria-label="{{ __('common.skip_to_sidebar') }}">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <a href="{{ Route::has('portal.dashboard') ? route('portal.dashboard') : '/' }}"
                    class="flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">
                    <x-application-logo class="h-8 w-auto text-slate-100" />
                    <span class="text-lg font-semibold text-slate-100 hidden sm:inline">
                        {{ config('app.name', 'ICTServe') }}
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-3">
                @include('portal.components.language-switcher')
                @include('portal.components.accessibility-menu')

                <livewire:components.theme-toggle-unified />

                @auth
                    <x-ui.dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button"
                                class="inline-flex items-center gap-2 min-h-11 px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-100 hover:bg-slate-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary-500/20 text-primary-200 font-semibold">
                                    {{ $initial }}
                                </span>
                                <span class="hidden md:inline text-sm font-medium">{{ $displayName }}</span>
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @if (Route::has('portal.profile'))
                                <x-ui.dropdown-item href="{{ route('portal.profile') }}">
                                    {{ __('staff.nav.profile') }}
                                </x-ui.dropdown-item>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-ui.dropdown-item href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('staff.nav.logout') }}
                                </x-ui.dropdown-item>
                            </form>
                        </x-slot>
                    </x-ui.dropdown>
                @endauth

                @guest
                    <a href="{{ Route::has('login') ? route('login') : '#' }}"
                        class="inline-flex items-center min-h-11 px-4 py-2 rounded-lg border border-slate-700 text-sm text-slate-100 hover:bg-slate-900 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500">
                        {{ __('auth.login') }}
                    </a>
                @endguest
            </div>
        </div>
    </div>
</header>
