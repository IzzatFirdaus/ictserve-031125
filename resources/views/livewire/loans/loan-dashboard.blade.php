<div class="mx-auto max-w-7xl space-y-8 px-4 py-8 text-slate-100 sm:px-6 lg:px-8">
    <header class="space-y-2">
        <h1 class="text-3xl font-bold text-slate-100">
            {{ __('loan.dashboard.title') }}
        </h1>
        <p class="text-lg text-slate-300">
            {{ __('loan.dashboard.description') }}
        </p>
    </header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">{{ __('loan.dashboard.active_loans') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">{{ $this->statistics['active_loans'] }}</p>
                </div>
                <div class="rounded-full border border-primary-500/30 bg-primary-500/10 p-3 text-primary-300" aria-hidden="true">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">{{ __('loan.dashboard.pending_applications') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">{{ $this->statistics['pending_applications'] }}</p>
                </div>
                <div class="rounded-full border border-warning-500/30 bg-warning-500/10 p-3 text-warning-300" aria-hidden="true">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">{{ __('loan.dashboard.overdue_items') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">{{ $this->statistics['overdue_items'] }}</p>
                </div>
                <div class="rounded-full border border-danger-500/30 bg-danger-500/10 p-3 text-danger-300" aria-hidden="true">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-300">{{ __('loan.dashboard.total_applications') }}</p>
                    <p class="mt-2 text-3xl font-bold text-slate-100">{{ $this->statistics['total_applications'] }}</p>
                </div>
                <div class="rounded-full border border-success-500/30 bg-success-500/10 p-3 text-success-300" aria-hidden="true">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card variant="portal" class="space-y-4 border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-100">{{ __('loan.dashboard.quick_actions') }}</h2>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <a href="{{ route('loan.guest.apply') }}"
                class="flex min-h-11 items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                aria-label="{{ __('loan.dashboard.new_application') }}">
                <svg class="h-8 w-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <div>
                    <p class="font-medium text-slate-100">{{ __('loan.dashboard.new_application') }}</p>
                    <p class="text-sm text-slate-300">{{ __('loan.dashboard.new_application_desc') }}</p>
                </div>
            </a>

            <a href="{{ route('loan.history') }}"
                class="flex min-h-11 items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                aria-label="{{ __('loan.dashboard.view_history') }}">
                <svg class="h-8 w-8 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium text-slate-100">{{ __('loan.dashboard.view_history') }}</p>
                    <p class="text-sm text-slate-300">{{ __('loan.dashboard.view_history_desc') }}</p>
                </div>
            </a>

            <a href="{{ route('loans.assets.available') }}"
                class="flex min-h-11 items-center gap-3 rounded-lg border border-slate-800/80 bg-slate-900/70 p-4 text-slate-100 shadow-lg shadow-slate-950/40 transition hover:bg-slate-900/60 focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                aria-label="{{ __('loan.dashboard.browse_assets') }}">
                <svg class="h-8 w-8 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <div>
                    <p class="font-medium text-slate-100">{{ __('loan.dashboard.browse_assets') }}</p>
                    <p class="text-sm text-slate-300">{{ __('loan.dashboard.browse_assets_desc') }}</p>
                </div>
            </a>
        </div>
    </x-ui.card>

    <section aria-label="{{ __('loan.dashboard.loan_details') }}">
        <x-navigation.tabs
            :tabs="[
                ['id' => 'overview', 'label' => __('loan.dashboard.tabs.overview'), 'icon' => 'home'],
                ['id' => 'active', 'label' => __('loan.dashboard.tabs.active_loans'), 'icon' => 'check-circle', 'badge' => $this->statistics['active_loans']],
                ['id' => 'pending', 'label' => __('loan.dashboard.tabs.pending'), 'icon' => 'clock', 'badge' => $this->statistics['pending_applications']],
            ]"
            :active-tab="$activeTab"
            wire:model.live="activeTab"
        />

        <div id="panel-{{ $activeTab }}" class="mt-6 space-y-4" role="tabpanel" aria-labelledby="tab-{{ $activeTab }}">
            @if ($activeTab === 'overview')
                <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/40 shadow-lg shadow-slate-950/40">
                    <p class="text-slate-300">{{ __('loan.dashboard.overview_text') }}</p>
                </x-ui.card>
            @elseif ($activeTab === 'active')
                @forelse ($this->activeLoans as $loan)
                    @php
                        $statusColor = match ($loan->status->color()) {
                            'green' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'blue' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                            'yellow' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'orange' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'red' => 'bg-danger-900/30 text-danger-400 border border-danger-800',
                            'purple' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                            'teal' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'amber' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'lime' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'emerald' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'gray' => 'bg-slate-800 text-slate-300 border border-slate-700',
                            default => 'bg-slate-800 text-slate-300 border border-slate-700',
                        };
                    @endphp

                    <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/70">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-slate-100">
                                    <a href="{{ route('loan.authenticated.show', $loan) }}" class="text-primary-400 hover:text-primary-300">
                                        {{ $loan->application_number }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-slate-300">{{ $loan->purpose }}</p>
                                <p class="mt-2 text-sm text-slate-400">
                                    {{ __('loan.dashboard.loan_period') }}:
                                    {{ $loan->loan_start_date?->translatedFormat('d/m/Y') }} – {{ $loan->loan_end_date?->translatedFormat('d/m/Y') }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                {{ $loan->status->label() }}
                            </span>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        icon="loan"
                        variant="portal"
                        :message="__('loan.dashboard.no_active_loans')"
                        :action-text="__('loan.dashboard.new_application')"
                        :action-url="route('loan.guest.apply')"
                    />
                @endforelse
            @elseif ($activeTab === 'pending')
                @forelse ($this->pendingApplications as $loan)
                    @php
                        $statusColor = match ($loan->status->color()) {
                            'green' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'blue' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                            'yellow' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'orange' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'red' => 'bg-danger-900/30 text-danger-400 border border-danger-800',
                            'purple' => 'bg-primary-900/30 text-primary-400 border border-primary-800',
                            'teal' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'amber' => 'bg-warning-900/30 text-warning-400 border border-warning-800',
                            'lime' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'emerald' => 'bg-success-900/30 text-success-400 border border-success-800',
                            'gray' => 'bg-slate-800 text-slate-300 border border-slate-700',
                            default => 'bg-slate-800 text-slate-300 border border-slate-700',
                        };
                    @endphp

                    <x-ui.card variant="portal" class="border-slate-800 bg-slate-900/70">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-slate-100">
                                    <a href="{{ route('loan.authenticated.show', $loan) }}" class="text-primary-400 hover:text-primary-300">
                                        {{ $loan->application_number }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-slate-300">{{ $loan->purpose }}</p>
                                <p class="mt-2 text-sm text-slate-400">
                                    {{ __('loan.dashboard.submitted') }}: {{ $loan->created_at?->translatedFormat('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                                {{ $loan->status->label() }}
                            </span>
                        </div>
                    </x-ui.card>
                @empty
                    <x-ui.empty-state
                        icon="loan"
                        variant="portal"
                        :message="__('loan.dashboard.no_pending_applications')"
                    />
                @endforelse
            @endif
        </div>
    </section>
</div>
