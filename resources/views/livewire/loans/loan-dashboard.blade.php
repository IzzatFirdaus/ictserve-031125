<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 text-slate-100">
    {{-- Page Header --}}
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100">
                {{ __('loan.dashboard.title') }}
            </h1>
            <p class="text-slate-300">
                {{ __('loan.dashboard.description') }}
            </p>
        </div>
    </header>

    {{-- Statistics Cards --}}
    <section aria-label="{{ __('loan.dashboard.statistics') }}">
        <h2 class="mb-4 text-lg font-semibold text-slate-100">{{ __('loan.dashboard.my_statistics') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Active Loans --}}
            <x-ui.card variant="portal" aria-label="{{ __('loan.dashboard.active_loans') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->statistics['active_loans'] }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loan.dashboard.active_loans') }}</p>
                    </div>
                    <div class="rounded-full border border-blue-500/30 bg-blue-500/10 p-3 text-blue-300">
                        <svg class="h-6 w-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Pending Applications --}}
            <x-ui.card variant="portal" aria-label="{{ __('loan.dashboard.pending_applications') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->statistics['pending_applications'] }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loan.dashboard.pending_applications') }}</p>
                    </div>
                    <div class="rounded-full border border-amber-500/30 bg-amber-500/10 p-3 text-amber-300">
                        <svg class="h-6 w-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Overdue Items --}}
            <x-ui.card variant="portal" aria-label="{{ __('loan.dashboard.overdue_items') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->statistics['overdue_items'] }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loan.dashboard.overdue_items') }}</p>
                    </div>
                    <div class="rounded-full border border-red-500/30 bg-red-500/10 p-3 text-red-300">
                        <svg class="h-6 w-6 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Total Applications --}}
            <x-ui.card variant="portal" aria-label="{{ __('loan.dashboard.total_applications') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->statistics['total_applications'] }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loan.dashboard.total_applications') }}</p>
                    </div>
                    <div class="rounded-full border border-green-500/30 bg-green-500/10 p-3 text-green-300">
                        <svg class="h-6 w-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Quick Actions --}}
    <section aria-label="{{ __('loan.dashboard.quick_actions') }}">
        <x-ui.card variant="portal">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-lg font-semibold text-slate-100">{{ __('loan.dashboard.quick_actions') }}</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('loan.guest.apply') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                        aria-label="{{ __('loan.dashboard.new_application') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ __('loan.dashboard.new_application') }}</span>
                    </a>

                    <a href="{{ route('loan.history') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                        aria-label="{{ __('loan.dashboard.view_history') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('loan.dashboard.view_history') }}</span>
                    </a>

                    <a href="{{ route('loan.assets') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-44"
                        aria-label="{{ __('loan.dashboard.browse_assets') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>{{ __('loan.dashboard.browse_assets') }}</span>
                    </a>
                </div>
=======
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
>>>>>>> Stashed changes
            </div>
        </x-ui.card>
    </section>

    {{-- Tabbed Content --}}
    {{-- Page Header --}}
    <section aria-label="{{ __('loan.dashboard.loan_details') }}">
        <x-ui.card variant="portal">
            <div class="mb-4 flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex space-x-8">
                    <button
                        wire:click="setTab('overview')"
                        class="px-4 py-2 text-sm font-medium {{ $activeTab === 'overview' ? 'border-b-2 border-blue-500 text-blue-300' : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        {{ __('loan.dashboard.tabs.overview') }}
                    </button>
                    <button
                        wire:click="setTab('active')"
                        class="px-4 py-2 text-sm font-medium {{ $activeTab === 'active' ? 'border-b-2 border-blue-500 text-blue-300' : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        {{ __('loan.dashboard.tabs.active_loans') }}
                    </button>
                    <button
                        wire:click="setTab('pending')"
                        class="px-4 py-2 text-sm font-medium {{ $activeTab === 'pending' ? 'border-b-2 border-blue-500 text-blue-300' : 'text-slate-400 hover:text-slate-200' }}"
                    >
                        {{ __('loan.dashboard.tabs.pending') }}
                    </button>
                </div>
            </div>

            <div class="mt-4">
                @if($activeTab === 'overview')
                    <div class="space-y-4">
                        <p class="text-slate-300">
                            {{ __('loan.dashboard.overview_text') }}
                        </p>
                    </div>
                @elseif($activeTab === 'active')
                    <div class="space-y-4">
                        @forelse($this->activeLoans as $loan)
                            <div class="flex items-start gap-4 border-l-4 border-blue-500/50 bg-slate-900/40 p-4 rounded-r-lg hover:bg-slate-900/60 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-slate-200">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-400">{{ $loan->purpose }}</p>
                                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-300">
                                        <span>{{ __('loan.dashboard.loan_period') }}: {{ $loan->loan_start_date->format('d/m/Y') }} - {{ $loan->loan_end_date->format('d/m/Y') }}</span>
                                        <span class="inline-flex items-center rounded-full border border-blue-500/30 bg-blue-500/10 px-2 py-0.5 text-xs font-medium text-blue-300">
                                            {{ $loan->status->label() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-slate-400">
                                {{ __('loan.dashboard.no_active_loans') }}
                            </div>
                        @endforelse
                    </div>
                @elseif($activeTab === 'pending')
                    <div class="space-y-4">
                        @forelse($this->pendingApplications as $loan)
                            <div class="flex items-start gap-4 border-l-4 border-amber-500/50 bg-slate-900/40 p-4 rounded-r-lg hover:bg-slate-900/60 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-medium text-slate-200">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-400">{{ $loan->purpose }}</p>
                                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-300">
                                        <span>{{ __('loan.dashboard.submitted') }}: {{ $loan->created_at->format('d/m/Y H:i') }}</span>
                                        <span class="inline-flex items-center rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs font-medium text-amber-300">
                                            {{ $loan->status->label() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-slate-400">
                                {{ __('loan.dashboard.no_pending_applications') }}
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </x-ui.card>
    </section>
=======
    <x-navigation.tabs>
        <x-slot:tabs>
            <button
                wire:click="setTab('overview')"
                class="px-4 py-2 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 {{ $activeTab === 'overview' ? 'border-b-2 border-blue-600 text-blue-300' : 'text-slate-300 hover:text-slate-100' }}"
            >
                {{ __('loan.dashboard.tabs.overview') }}
            </button>
            <button
                wire:click="setTab('active')"
                class="px-4 py-2 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 {{ $activeTab === 'active' ? 'border-b-2 border-blue-600 text-blue-300' : 'text-slate-300 hover:text-slate-100' }}"
            >
                {{ __('loan.dashboard.tabs.active_loans') }}
            </button>
            <button
                wire:click="setTab('pending')"
                class="px-4 py-2 text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 {{ $activeTab === 'pending' ? 'border-b-2 border-blue-600 text-blue-300' : 'text-slate-300 hover:text-slate-100' }}"
            >
                {{ __('loan.dashboard.tabs.pending') }}
            </button>
        </x-slot:tabs>

        <x-slot:content>
            @if($activeTab === 'overview')
                <div class="space-y-4">
                    <p class="text-slate-300">
                        {{ __('loan.dashboard.overview_text') }}
                    </p>
                </div>
            @elseif($activeTab === 'active')
                <div class="space-y-4">
                    @forelse($this->activeLoans as $loan)
                        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/70">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-100">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="text-sm text-slate-300">
                                        {{ $loan->purpose }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ __('loan.dashboard.loan_period') }}:
                                        {{ $loan->loan_start_date->format('d/m/Y') }} - {{ $loan->loan_end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full bg-blue-500/10 text-blue-300 {{ $loan->status->color() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.empty-state
                            :message="__('loan.dashboard.no_active_loans')"
                            :action-text="__('loan.dashboard.new_application')"
                            :action-url="route('loan.guest.apply')"
                        />
                    @endforelse
                </div>
            @elseif($activeTab === 'pending')
                <div class="space-y-4">
                    @forelse($this->pendingApplications as $loan)
                        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/70">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-slate-100">
                                        {{ $loan->application_number }}
                                    </h3>
                                    <p class="text-sm text-slate-300">
                                        {{ $loan->purpose }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ __('loan.dashboard.submitted') }}: {{ $loan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full bg-amber-500/10 text-amber-300 {{ $loan->status->color() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </div>
                        </x-ui.card>
                    @empty
                        <x-ui.empty-state
                            :message="__('loan.dashboard.no_pending_applications')"
                        />
                    @endforelse
                </div>
            @endif
        </x-slot:content>
    </x-navigation.tabs>
    {{-- merge-resolved: Combined improved layout, accessibility and slate theming --}}
</div>
