{{--
Component name: Authenticated Loan Dashboard
Description: WCAG 2.2 AA compliant dashboard for authenticated users showing loan statistics and management interface

@author Pasukan BPM MOTAC
@trace D03-FR-011.1, D03-FR-011.2, D03-FR-011.5
@trace D04 §6.2 (Authenticated Livewire Components)
@trace D12 §9 (WCAG 2.2 AA Dashboard Design)
@trace D14 §5 (Compliant Color Palette)
@requirements 11.1, 11.2, 11.5, 15.1
@wcag-level AA
@version 1.0.0
@created 2025-11-03
--}}

<div class="space-y-8 text-slate-100" aria-describedby="loan-dashboard-description">
    <p id="loan-dashboard-description" class="sr-only">
        {{ __('Ringkasan statistik pinjaman aset dan aktiviti semasa untuk kakitangan MOTAC.') }}
    </p>
    {{-- Skip Links for WCAG AA --}}
    <x-navigation.skip-links :targets="[
        ['id' => 'loan-stats', 'label' => __('loans.skip_to_statistics')],
        ['id' => 'loan-tabs', 'label' => __('loans.skip_to_tabs')],
        ['id' => 'main-content', 'label' => __('loans.skip_to_main_content')]
    ]" />

    {{-- Page Header --}}
    <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-100" id="dashboard-heading">
                {{ __('loans.authenticated_dashboard') }}
            </h1>
            <p class="text-slate-300">
                {{ __('loans.dashboard_description') }}
            </p>
        </div>
        <x-ui.button
            icon="heroicon-o-arrow-path"
            wire:click="refreshData"
            wire:loading.attr="disabled"
            aria-label="{{ __('common.refresh_dashboard') }}"
        >
            {{ __('common.refresh') }}
        </x-ui.button>
    </header>

    {{-- Statistics Cards --}}
    <section aria-label="{{ __('loans.loan_statistics') }}">
        <h2 class="mb-4 text-lg font-semibold text-slate-100">{{ __('loans.loan_statistics') }}</h2>
        <div id="loan-stats" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" role="region">
            {{-- Active Loans --}}
            <x-ui.card variant="portal" aria-label="{{ __('loans.active_loans') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->activeLoansCount }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loans.active_loans') }}</p>
                    </div>
                    <div class="rounded-full border border-primary-500/30 bg-primary-500/10 p-3 text-primary-300">
                        <svg class="h-6 w-6 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Pending Applications --}}
            <x-ui.card variant="portal" aria-label="{{ __('loans.pending_applications') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->pendingCount }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loans.pending_applications') }}</p>
                    </div>
                    <div class="rounded-full border border-warning-500/30 bg-warning-500/10 p-3 text-warning-300">
                        <svg class="h-6 w-6 text-warning-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Overdue Items --}}
            <x-ui.card variant="portal" aria-label="{{ __('loans.overdue_items') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->overdueCount }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loans.overdue_items') }}</p>
                    </div>
                    <div class="rounded-full border border-danger-500/30 bg-danger-500/10 p-3 text-danger-300">
                        <svg class="h-6 w-6 text-danger-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </x-ui.card>

            {{-- Total Applications --}}
            <x-ui.card variant="portal" aria-label="{{ __('loans.total_applications') }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-semibold text-slate-100">{{ $this->totalApplicationsCount }}</p>
                        <p class="mt-1 text-sm text-slate-400">{{ __('loans.total_applications') }}</p>
                    </div>
                    <div class="rounded-full border border-success-500/30 bg-success-500/10 p-3 text-success-300">
                        <svg class="h-6 w-6 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </section>

    {{-- Tabbed Interface --}}
    <section id="loan-tabs" aria-label="{{ __('loans.loan_management_tabs') }}">
        <x-navigation.tabs
            :tabs="[
                ['id' => 'overview', 'label' => __('loans.tab_overview'), 'icon' => 'home'],
                ['id' => 'active', 'label' => __('loans.tab_active_loans'), 'icon' => 'check-circle', 'badge' => $this->activeLoansCount],
                ['id' => 'pending', 'label' => __('loans.tab_pending'), 'icon' => 'clock', 'badge' => $this->pendingCount],
                ['id' => 'history', 'label' => __('loans.tab_history'), 'icon' => 'document-text'],
            ]"
            :active-tab="$activeTab"
            wire:model.live="activeTab"
        />

        {{-- Tab Content --}}
        {{-- Use panel-<id> to match aria-controls on tabs and aria-labelledby to match the tab id --}}
        <div id="panel-{{ $activeTab }}" class="mt-6" role="tabpanel" aria-labelledby="tab-{{ $activeTab }}">
            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
                <div class="space-y-6">
                    {{-- Quick Actions --}}
                    <x-ui.card variant="portal">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-lg font-semibold text-slate-100">
                                {{ __('loans.quick_actions') }}
                            </h2>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('loan.authenticated.create') }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-11">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('loans.new_application') }}
                                </a>

                                <a href="{{ route('staff.profile') }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-11">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ __('loans.manage_profile') }}
                                </a>
                            </div>
                        </div>
                    </x-ui.card>

                    {{-- Active Loans Section --}}
                    @if($this->activeLoans->count() > 0)
                        <x-ui.card variant="portal">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-slate-100">
                                    {{ __('loans.recent_active_loans') }}
                                </h2>
                            </div>
                            <div class="space-y-4" role="feed" aria-label="{{ __('loans.recent_active_loans') }}">
                                @foreach($this->activeLoans->take(3) as $loan)
                                    <div class="flex items-start gap-4 border-l-4 border-primary-500/50 bg-slate-900/40 p-4 rounded-r-lg hover:bg-slate-900/60 transition-colors" role="article">
                                        <div class="shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full border border-primary-500/30 bg-primary-500/10">
                                                <svg class="h-5 w-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-200">
                                                <a href="{{ route('loan.authenticated.show', $loan->id) }}" class="text-slate-200 hover:text-primary-400 transition-colors">
                                                    {{ $loan->application_number }}
                                                </a>
                                            </p>
                                            <p class="mt-1 text-sm text-slate-400">
                                                {{ $loan->loanItems->count() }} {{ __('loans.items') }} •
                                                {{ optional($loan->loan_start_date)->translatedFormat('d M Y') ?? '-' }} - {{ optional($loan->loan_end_date)->translatedFormat('d M Y') ?? '-' }}
                                            </p>
                                            <div class="mt-2">
                                                <span class="inline-flex items-center rounded-full border border-primary-500/30 bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-300">
                                                    {{ $loan->status->label() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($this->activeLoans->count() > 3)
                                <div class="mt-4 text-center">
                                    <button wire:click="switchTab('active')" class="text-sm font-medium text-primary-300 hover:text-primary-400 min-h-11 inline-flex items-center">
                                        {{ __('loans.view_all_active') }} &rarr;
                                    </button>
                                </div>
                            @endif
                        </x-ui.card>
                    @else
                        <x-ui.card variant="portal" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-200">{{ __('loans.no_active_loans') }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ __('loans.no_active_loans_description') }}</p>
                            <div class="mt-6">
                                <a href="{{ route('loan.authenticated.create') }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-11">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('loans.create_first_application') }}
                                </a>
                            </div>
                        </x-ui.card>
                    @endif
                </div>

            {{-- Active Loans Tab --}}
            @elseif($activeTab === 'active')
                <x-ui.card variant="portal">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-100">
                            {{ __('loans.active_loans_list') }}
                        </h2>
                    </div>
                    @if($this->activeLoans->count() > 0)
                        <div class="overflow-hidden rounded-lg border border-slate-800">
                            <table class="min-w-full divide-y divide-slate-800">
                                <thead class="bg-slate-900/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.application_number') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.items') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.period') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 bg-slate-900/70">
                                    @foreach($this->activeLoans as $loan)
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-slate-100">
                                                <div class="font-medium text-slate-200">{{ $loan->application_number }}</div>
                                                <div class="text-xs text-slate-400">{{ optional($loan->created_at)->translatedFormat('d M Y') ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-200">
                                                {{ $loan->loanItems->count() }} {{ __('loans.items') }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-200">
                                                {{ optional($loan->loan_start_date)->translatedFormat('d M Y') ?? '-' }} - {{ optional($loan->loan_end_date)->translatedFormat('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-200">
                                                <span class="inline-flex items-center rounded-full border border-primary-500/30 bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-300">
                                                    {{ $loan->status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-right">
                                                <a href="{{ route('loan.authenticated.show', $loan->id) }}" class="font-medium text-primary-300 hover:text-primary-400 min-h-11 inline-flex items-center">{{ __('loans.view_details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-8 text-center text-sm text-slate-400">
                            {{ __('loans.no_active_loans') }}
                        </div>
                    @endif
                </x-ui.card>

            {{-- Pending Tab --}}
            @elseif($activeTab === 'pending')
                <x-ui.card variant="portal">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-100">
                            {{ __('loans.pending_applications_list') }}
                        </h2>
                    </div>
                    @if($this->pendingApplications->count() > 0)
                        <div class="space-y-4" role="feed" aria-label="{{ __('loans.pending_applications_list') }}">
                            @foreach($this->pendingApplications as $application)
                                <div class="flex items-start gap-4 border-l-4 border-warning-500/50 bg-slate-900/40 p-4 rounded-r-lg hover:bg-slate-900/60 transition-colors" role="article">
                                    <div class="shrink-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full border border-warning-500/30 bg-warning-500/10">
                                            <svg class="h-5 w-5 text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-200">
                                            <a href="{{ route('loan.authenticated.show', $application->id) }}" class="text-slate-200 hover:text-primary-400 transition-colors">
                                                {{ $application->application_number }}
                                            </a>
                                        </p>
                                        <p class="mt-1 text-sm text-slate-400">{{ Str::limit($application->purpose, 100) }}</p>
                                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-300">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $application->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="inline-flex items-center rounded-full border border-warning-500/30 bg-warning-500/10 px-2 py-0.5 text-xs font-medium text-warning-300">
                                                {{ $application->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-sm text-slate-400">
                            {{ __('loans.no_pending_applications') }}
                        </div>
                    @endif
                </x-ui.card>

            {{-- History Tab --}}
            @elseif($activeTab === 'history')
                <x-ui.card variant="portal">
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-lg font-semibold text-slate-100">
                            {{ __('loans.loan_history') }}
                        </h2>

                        {{-- Search and Filter --}}
                        <div class="flex flex-col sm:flex-row gap-2">
                            <x-form.input
                                name="search"
                                wire:model.live.debounce.300ms="search"
                                type="search"
                                placeholder="{{ __('loans.search_loans') }}"
                                class="min-h-11"
                                aria-label="{{ __('loans.search_loans') }}"
                            />

                            @if($search || $statusFilter)
                                <button wire:click="clearFilters" class="inline-flex items-center gap-2 rounded-xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/40 transition-colors hover:bg-slate-900 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-slate-950 min-h-11">
                                    {{ __('loans.clear_filters') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($this->loanHistory->count() > 0)
                        <div class="overflow-hidden rounded-lg border border-slate-800">
                            <table class="min-w-full divide-y divide-slate-800">
                                <thead class="bg-slate-900/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.application') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.purpose') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.period') }}</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.status') }}</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-400">{{ __('loans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800 bg-slate-900/70">
                                    @foreach($this->loanHistory as $loan)
                                        <tr>
                                            <td class="px-4 py-4 text-sm text-slate-100">
                                                <div class="font-medium text-slate-200">{{ $loan->application_number }}</div>
                                                <div class="text-xs text-slate-400">{{ $loan->created_at->translatedFormat('d M Y') }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-200">{{ Str::limit($loan->purpose, 50) }}</td>
                                            <td class="px-4 py-4 text-sm text-slate-200">
                                                {{ optional($loan->loan_start_date)->translatedFormat('d M') ?? '-' }} - {{ optional($loan->loan_end_date)->translatedFormat('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-slate-200">
                                                <span class="inline-flex items-center rounded-full border border-primary-500/30 bg-primary-500/10 px-2 py-0.5 text-xs font-medium text-primary-300">
                                                    {{ $loan->status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-right">
                                                <a href="{{ route('loan.authenticated.show', $loan->id) }}" class="font-medium text-primary-300 hover:text-primary-400 min-h-11 inline-flex items-center">{{ __('loans.view') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $this->loanHistory->links() }}
                        </div>
                    @else
                        <div class="py-8 text-center text-sm text-slate-400">
                            @if($search || $statusFilter)
                                {{ __('loans.no_results_found') }}
                            @else
                                {{ __('loans.no_loan_history') }}
                            @endif
                        </div>
                    @endif
                </x-ui.card>
            @endif
        </div>
    </section>

    {{-- Live Region for Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only">
        @if(session()->has('success'))
            {{ session('success') }}
        @endif
    </div>
</div>
