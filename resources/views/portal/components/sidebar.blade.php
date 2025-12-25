{{--
    Component: Portal Sidebar
    Description: Collapsible sidebar navigation for portal layout.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §10
    Version: 1.0.0
    Updated: 2025-12-20
--}}

@php
    $sidebarLinks = [
        [
            'label' => __('common.dashboard'),
            'route' => 'portal.dashboard',
        ],
        [
            'label' => __('portal.history_title'),
            'route' => 'portal.submissions',
        ],
        [
            'label' => __('staff.nav.approvals'),
            'route' => 'portal.approvals',
        ],
        [
            'label' => __('portal.search_submissions'),
            'route' => 'portal.search',
        ],
        [
            'label' => __('staff.nav.profile'),
            'route' => 'portal.profile',
        ],
        [
            'label' => __('staff.claims.title'),
            'route' => 'portal.link-submissions',
        ],
    ];
@endphp

<div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 md:hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-950/70" @click="sidebarOpen = false" aria-hidden="true"></div>
    <aside class="relative h-full w-72 bg-slate-950 border-r border-slate-800 p-4" role="navigation"
        aria-label="{{ __('common.main_navigation') }}">
        <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">{{ __('common.dashboard') }}</span>
            <button type="button" class="min-h-11 min-w-11 rounded-lg border border-slate-800 text-slate-200 hover:bg-slate-900 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500"
                @click="sidebarOpen = false" aria-label="{{ __('common.close') }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mt-6 space-y-2">
            @foreach ($sidebarLinks as $link)
                @continue(!Route::has($link['route']))
                <a href="{{ route($link['route']) }}"
                    class="min-h-11 flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 {{ request()->routeIs($link['route']) ? 'bg-primary-500/20 text-primary-100 border border-primary-500/40' : 'text-slate-300 hover:text-white hover:bg-slate-900 border border-transparent' }}"
                    @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </aside>
</div>

<aside id="portal-sidebar"
    class="hidden md:flex md:flex-col md:w-64 md:shrink-0 bg-slate-950 border-r border-slate-800"
    role="navigation" aria-label="{{ __('common.main_navigation') }}">
    <div class="p-4 space-y-2">
        @foreach ($sidebarLinks as $link)
            @continue(!Route::has($link['route']))
            <a href="{{ route($link['route']) }}"
                class="min-h-11 flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 {{ request()->routeIs($link['route']) ? 'bg-primary-500/20 text-primary-100 border border-primary-500/40' : 'text-slate-300 hover:text-white hover:bg-slate-900 border border-transparent' }}"
                @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</aside>
