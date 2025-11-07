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

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" aria-describedby="loan-dashboard-description">
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
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-100 dark:text-slate-200" id="dashboard-heading">
            {{ __('loans.authenticated_dashboard') }}
        </h1>
        <p class="mt-2 text-sm text-slate-300 dark:text-slate-400">
            {{ __('loans.dashboard_description') }}
        </p>
    </div>

    {{-- Statistics Cards --}}
    <div id="loan-stats" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8" role="region" aria-label="{{ __('loans.loan_statistics') }}">
        {{-- Active Loans --}}
        <x-ui.card variant="portal" class="bg-gradient-to-br from-blue-900/70 via-blue-800/40 to-blue-600/30 border border-blue-500/30 shadow-lg shadow-blue-900/30">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-blue-200 truncate">
                            {{ __('loans.active_loans') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-white">
                                {{ $this->activeLoansCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </x-ui.card>

        {{-- Pending Applications --}}
        <x-ui.card variant="portal" class="bg-gradient-to-br from-amber-900/70 via-amber-800/40 to-amber-500/20 border border-amber-500/30 shadow-lg shadow-amber-900/30">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-amber-200 truncate">
                            {{ __('loans.pending_applications') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-white">
                                {{ $this->pendingCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </x-ui.card>

        {{-- Overdue Items --}}
        <x-ui.card variant="portal" class="bg-gradient-to-br from-rose-900/70 via-rose-800/40 to-red-500/20 border border-rose-500/30 shadow-lg shadow-rose-900/30">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-rose-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-rose-200 truncate">
                            {{ __('loans.overdue_items') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-white">
                                {{ $this->overdueCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </x-ui.card>

        {{-- Total Applications --}}
        <x-ui.card variant="portal" class="bg-gradient-to-br from-emerald-900/70 via-emerald-800/40 to-emerald-500/20 border border-emerald-500/30 shadow-lg shadow-emerald-900/30">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-emerald-200 truncate">
                            {{ __('loans.total_applications') }}
                        </dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-bold text-white">
                                {{ $this->totalApplicationsCount }}
                            </div>
                        </dd>
                    </dl>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Tabbed Interface --}}
    <div id="loan-tabs" role="region" aria-label="{{ __('loans.loan_management_tabs') }}">
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
        <div id="main-content" class="mt-6" role="tabpanel" aria-labelledby="tab-{{ $activeTab }}">
            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
                <div class="space-y-6">
                    {{-- Quick Actions --}}
                    <x-ui.card variant="portal">
                        <h2 class="text-lg font-semibold text-slate-100 dark:text-slate-200 mb-4">
                            {{ __('loans.quick_actions') }}
                        </h2>
                        <div class="flex flex-wrap gap-4">
                            <x-ui.button href="{{ route('loan.authenticated.create') }}" color="primary" class="min-h-[44px] min-w-[44px]">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('loans.new_application') }}
                            </x-ui.button>

                            <x-ui.button href="{{ route('staff.profile') }}" color="secondary" class="min-h-[44px] min-w-[44px]">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('loans.manage_profile') }}
                            </x-ui.button>
                        </div>
                    </x-ui.card>

                    {{-- Active Loans Section --}}
                    @if($this->activeLoans->count() > 0)
                        <x-ui.card variant="portal">
                            <h2 class="text-lg font-semibold text-slate-100 dark:text-slate-200 mb-4">
                                {{ __('loans.recent_active_loans') }}
                            </h2>
                            <div class="space-y-4">
                                @foreach($this->activeLoans->take(3) as $loan)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2 bg-slate-900/40 dark:bg-slate-900 rounded-r">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-slate-100 dark:text-slate-200">
                                                    {{ $loan->application_number }}
                                                </p>
                                                <p class="text-sm text-slate-300 dark:text-slate-400">
                                                    {{ $loan->loanItems->count() }} {{ __('loans.items') }} •
                                                    {{ $loan->start_date->translatedFormat('d M Y') }} - {{ $loan->end_date->translatedFormat('d M Y') }}
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $loan->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($this->activeLoans->count() > 3)
                                <div class="mt-4">
                                    <button wire:click="switchTab('active')" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium min-h-[44px] min-w-[44px]">
                                        {{ __('loans.view_all_active') }} →
                                    </button>
                                </div>
                            @endif
                        </x-ui.card>
                    @else
                        <x-ui.card variant="portal" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-100 dark:text-slate-200">{{ __('loans.no_active_loans') }}</h3>
                            <p class="mt-1 text-sm text-slate-400 dark:text-slate-400">{{ __('loans.no_active_loans_description') }}</p>
                            <div class="mt-6">
                                <x-ui.button href="{{ route('loan.authenticated.create') }}" color="primary" class="min-h-[44px] min-w-[44px]">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('loans.create_first_application') }}
                                </x-ui.button>
                            </div>
                        </x-ui.card>
                    @endif
                </div>

            {{-- Active Loans Tab --}}
            @elseif($activeTab === 'active')
                <x-ui.card variant="portal">
                    <h2 class="text-lg font-semibold text-slate-100 dark:text-slate-200 mb-4">
                        {{ __('loans.active_loans_list') }}
                    </h2>
                    @if($this->activeLoans->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 dark:divide-slate-700">
                                <thead class="bg-slate-900/40 dark:bg-slate-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.application_number') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.items') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.period') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-slate-900/70 dark:bg-slate-950 divide-y divide-slate-800 dark:divide-slate-700">
                                    @foreach($this->activeLoans as $loan)
                                        <tr class="hover:bg-slate-900/40 dark:hover:bg-slate-900">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-slate-100 dark:text-slate-200">{{ $loan->application_number }}</div>
                                                <div class="text-sm text-slate-400 dark:text-slate-400">{{ $loan->created_at->translatedFormat('d M Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-slate-100 dark:text-slate-200">
                                                    {{ $loan->loanItems->count() }} {{ __('loans.items') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-slate-100 dark:text-slate-200">
                                                    {{ $loan->start_date->translatedFormat('d M Y') }} - {{ $loan->end_date->translatedFormat('d M Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $loan->status->color() }}20; color: {{ $loan->status->color() }};">
                                                    {{ $loan->status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('loan.authenticated.show', $loan->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 min-h-[44px] inline-flex items-center">{{ __('loans.view_details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-slate-400 dark:text-slate-400">{{ __('loans.no_active_loans') }}</p>
                        </div>
                    @endif
                </x-ui.card>

            {{-- Pending Tab --}}
            @elseif($activeTab === 'pending')
                <x-ui.card variant="portal">
                    <h2 class="text-lg font-semibold text-slate-100 dark:text-slate-200 mb-4">
                        {{ __('loans.pending_applications_list') }}
                    </h2>
                    @if($this->pendingApplications->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->pendingApplications as $application)
                                <div class="border border-slate-800 dark:border-slate-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-sm font-semibold text-slate-100 dark:text-slate-200">{{ $application->application_number }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $application->status->color() }}20; color: {{ $application->status->color() }};">
                                            {{ $application->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-300 dark:text-slate-400 mb-2">{{ Str::limit($application->purpose, 100) }}</p>
                                    <div class="flex justify-between items-center text-xs text-slate-400 dark:text-slate-400">
                                        <span>{{ $application->created_at->translatedFormat('d M Y') }}</span>
                                        <a href="{{ route('loan.authenticated.show', $application->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 font-medium min-h-[44px] inline-flex items-center">{{ __('loans.view_details') }} →</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-slate-400 dark:text-slate-400">{{ __('loans.no_pending_applications') }}</p>
                        </div>
                    @endif
                </x-ui.card>

            {{-- History Tab --}}
            @elseif($activeTab === 'history')
                <x-ui.card variant="portal">
                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-lg font-semibold text-slate-100 dark:text-slate-200">
                            {{ __('loans.loan_history') }}
                        </h2>

                        {{-- Search and Filter --}}
                        <div class="flex flex-col sm:flex-row gap-2">
                            <x-form.input
                                wire:model.live.debounce.300ms="search"
                                type="search"
                                placeholder="{{ __('loans.search_loans') }}"
                                class="min-h-[44px]"
                                aria-label="{{ __('loans.search_loans') }}"
                            />

                            @if($search || $statusFilter)
                                <x-ui.button wire:click="clearFilters" color="secondary" size="sm" class="min-h-[44px] min-w-[44px]">
                                    {{ __('loans.clear_filters') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>

                    @if($this->loanHistory->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-800 dark:divide-slate-700">
                                <thead class="bg-slate-900/40 dark:bg-slate-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.application') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.purpose') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.period') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider">{{ __('loans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-slate-900/70 dark:bg-slate-950 divide-y divide-slate-800 dark:divide-slate-700">
                                    @foreach($this->loanHistory as $loan)
                                        <tr class="hover:bg-slate-900/40 dark:hover:bg-slate-900">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-slate-100 dark:text-slate-200">{{ $loan->application_number }}</div>
                                                <div class="text-sm text-slate-400 dark:text-slate-400">{{ $loan->created_at->translatedFormat('d M Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-slate-100 dark:text-slate-200">{{ Str::limit($loan->purpose, 50) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-slate-100 dark:text-slate-200">
                                                    {{ $loan->start_date->translatedFormat('d M') }} - {{ $loan->end_date->translatedFormat('d M Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $loan->status->color() }}20; color: {{ $loan->status->color() }};">
                                                    {{ $loan->status->label() }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('loan.authenticated.show', $loan->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 min-h-[44px] inline-flex items-center">{{ __('loans.view') }}</a>
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
                        <div class="text-center py-12">
                            <p class="text-slate-400 dark:text-slate-400">
                                @if($search || $statusFilter)
                                    {{ __('loans.no_results_found') }}
                                @else
                                    {{ __('loans.no_loan_history') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </x-ui.card>
            @endif
        </div>
    </div>

    {{-- Live Region for Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only">
        @if(session()->has('success'))
            {{ session('success') }}
        @endif
    </div>
</div>
