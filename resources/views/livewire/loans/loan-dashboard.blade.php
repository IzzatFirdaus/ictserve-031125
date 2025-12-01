<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 text-slate-100">
    {{-- Page Header --}}
    <header class="space-y-2">
        <h1 class="text-3xl font-bold text-slate-100">
            {{ __('loan.dashboard.title') }}
        </h1>
        <p class="text-lg text-slate-300">
            {{ __('loan.dashboard.description') }}
        </p>
    </header>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        {{-- Active Loans --}}
        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">
                        {{ __('loan.dashboard.active_loans') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">
                        {{ $this->statistics['active_loans'] }}
                    </p>
                </div>
                <div class="rounded-full border border-blue-500/30 bg-blue-500/10 p-3 text-blue-300">
                    <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Pending Applications --}}
        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">
                        {{ __('loan.dashboard.pending_applications') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">
                        {{ $this->statistics['pending_applications'] }}
                    </p>
                </div>
                <div class="rounded-full border border-amber-500/30 bg-amber-500/10 p-3 text-amber-300">
                    <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Overdue Items --}}
        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">
                        {{ __('loan.dashboard.overdue_items') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">
                        {{ $this->statistics['overdue_items'] }}
                    </p>
                </div>
                <div class="rounded-full border border-red-500/30 bg-red-500/10 p-3 text-red-300">
                    <svg class="w-8 h-8 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        {{-- Total Applications --}}
        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">
                        {{ __('loan.dashboard.total_applications') }}
                    </p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">
                        {{ $this->statistics['total_applications'] }}
                    </p>
                </div>
                <div class="rounded-full border border-emerald-500/30 bg-emerald-500/10 p-3 text-emerald-300">
                    <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Quick Actions --}}
    <div>
        <x-ui.card variant="portal" class="space-y-4 border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-100">
                    {{ __('loan.dashboard.quick_actions') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <a href="{{ route('loan.guest.apply') }}"
                    class="flex items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                    aria-label="{{ __('loan.dashboard.new_application') }}">
                    <svg class="h-8 w-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <div>
                        <p class="font-medium text-slate-100">{{ __('loan.dashboard.new_application') }}</p>
                        <p class="text-sm text-slate-300">{{ __('loan.dashboard.new_application_desc') }}</p>
                    </div>
                </a>

                <a href="{{ route('loan.history') }}"
                    class="flex items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                    aria-label="{{ __('loan.dashboard.view_history') }}">
                    <svg class="h-8 w-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-slate-100">{{ __('loan.dashboard.view_history') }}</p>
                        <p class="text-sm text-slate-300">{{ __('loan.dashboard.view_history_desc') }}</p>
                    </div>
                </a>

                <a href="{{ route('loan.assets') }}"
                    class="flex items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                    aria-label="{{ __('loan.dashboard.browse_assets') }}">
                    <svg class="h-8 w-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <div>
                        <p class="font-medium text-slate-100">{{ __('loan.dashboard.browse_assets') }}</p>
                        <p class="text-sm text-slate-300">{{ __('loan.dashboard.browse_assets_desc') }}</p>
                    </div>
                </a>
            </div>
        </x-ui.card>
    </div>

    {{-- Tabbed Content --}}
    <section aria-label="{{ __('loan.dashboard.loan_details') }}">
        <x-navigation.tabs>
            <x-slot:tabs>
*** End Patch