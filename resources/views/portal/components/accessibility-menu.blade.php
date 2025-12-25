{{--
    Component: Portal Accessibility Menu
    Description: Quick access menu for skip links and accessibility statement.
    Author: Pasukan BPM MOTAC
    Trace: D12 §9, D14 §10.5
    Version: 1.0.0
    Updated: 2025-12-20
--}}

<x-ui.dropdown align="right" width="56">
    <x-slot name="trigger">
        <button type="button"
            class="inline-flex items-center gap-2 min-h-11 px-3 py-2 rounded-lg text-sm font-medium text-slate-200 bg-slate-900/60 border border-slate-800 hover:bg-slate-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
            aria-label="{{ __('common.accessibility') }}">
            <svg class="h-5 w-5 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 4.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm0 0v2.25m-6.75 1.5 6.75 1.5 6.75-1.5M6.75 9.75 10.5 12m6.75-2.25L13.5 12m-3 0-2.25 7.5m7.5 0L13.5 12" />
            </svg>
            <span class="hidden sm:inline">{{ __('common.accessibility') }}</span>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-ui.dropdown-item href="#main-content">
            {{ __('common.skip_to_main_content') }}
        </x-ui.dropdown-item>
        <x-ui.dropdown-item href="#portal-primary-navigation">
            {{ __('common.skip_to_navigation') }}
        </x-ui.dropdown-item>
        <x-ui.dropdown-item href="#portal-sidebar">
            {{ __('common.skip_to_sidebar') }}
        </x-ui.dropdown-item>
        @if (Route::has('accessibility'))
            <x-ui.dropdown-item href="{{ route('accessibility') }}">
                {{ __('common.accessibility') }}
            </x-ui.dropdown-item>
        @endif
    </x-slot>
</x-ui.dropdown>
