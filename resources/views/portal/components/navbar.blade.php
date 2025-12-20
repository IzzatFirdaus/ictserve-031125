{{--
    Component: Portal Navbar
    Description: Primary navigation links for portal sections.
    Author: Pasukan BPM MOTAC
    Trace: D12 §4, D14 §10
    Version: 1.0.0
    Updated: 2025-12-20
--}}

@php
    $links = [
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
    ];
@endphp

<nav id="portal-primary-navigation" class="bg-slate-950/80 border-b border-slate-800" role="navigation"
    aria-label="{{ __('common.main_navigation') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto py-2">
            @foreach ($links as $link)
                @continue(!Route::has($link['route']))
                <a href="{{ route($link['route']) }}"
                    class="min-h-11 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 {{ request()->routeIs($link['route']) ? 'bg-primary-500/20 text-primary-100 border border-primary-500/40' : 'text-slate-300 hover:text-white hover:bg-slate-900 border border-transparent' }}"
                    @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
