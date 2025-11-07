{{--
    name: authenticated-dashboard.blade.php
    description: Main staff dashboard view with real-time statistics and activity feed
    author: dev-team@motac.gov.my
    trace: D03 SRS-FR-006, D12 §3, D14 §9 (Requirements 1.1-1.4, WCAG 2.2 AA)
    last-updated: 2025-11-06
--}}

<div wire:poll.300s="refreshStatistics" class="space-y-6">
    {{-- Claim Banner (if guest submissions exist) --}}
    @if($showClaimBanner && $claimableSubmissions['total'] > 0)
        <div
            role="alert"
            aria-live="polite"
            class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 flex items-start justify-between"
        >
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.19-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                        {{ __('staff.dashboard.claim_banner.title') }}
                    </h3>
                    <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                        {{ __('staff.dashboard.claim_banner.message', [
                            'total' => $claimableSubmissions['total'],
                            'tickets' => $claimableSubmissions['tickets'],
                            'loans' => $claimableSubmissions['loans'],
                        ]) }}
                    </p>
                    <a
                        href="{{ route('staff.claim-submissions') }}"
                        class="mt-2 inline-flex items-center text-sm font-medium text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100 underline focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 rounded"
                    >
                        {{ __('staff.dashboard.claim_banner.cta') }}
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
            <button
                wire:click="dismissClaimBanner"
                type="button"
                class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-500 rounded"
                aria-label="{{ __('staff.dashboard.claim_banner.close') }}"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Dashboard Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('staff.dashboard.welcome', ['name' => $user->name]) }}
        </h1>
        <button
            wire:click="refreshStatistics"
            type="button"
            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            aria-label="{{ __('staff.dashboard.refresh_aria') }}"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ __('staff.dashboard.refresh_button') }}
        </button>
    </div>

    {{-- Statistics Grid --}}
    @if($statistics)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Submissions --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('staff.dashboard.summary.total_submissions') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['total_submissions'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pending Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-amber-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('staff.dashboard.summary.pending_actions') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['pending_actions'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Recent Updates (7 days) --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('staff.dashboard.summary.recent_updates') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['recent_updates'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Profile Completeness --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('staff.dashboard.summary.profile_completeness') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['profile_completeness'] ?? 0 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Module Statistics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Helpdesk Statistics --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('staff.dashboard.helpdesk.title') }}
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.total') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $statistics['helpdesk']['total'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.pending') }}</span>
                            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $statistics['helpdesk']['pending'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.helpdesk.resolved') }}</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $statistics['helpdesk']['resolved'] }}</span>
                        </div>
                        @if($statistics['helpdesk']['avg_resolution_time'])
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.helpdesk.avg_resolution') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $statistics['helpdesk']['avg_resolution_time'] }}h</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Loan Statistics --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('staff.dashboard.loans.title') }}
                    </h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.total') }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $statistics['loans']['total'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.pending') }}</span>
                            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $statistics['loans']['pending'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.loans.approved') }}</span>
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ $statistics['loans']['approved'] }}</span>
                        </div>
                        @if($statistics['loans']['avg_approval_time'])
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.loans.avg_approval') }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $statistics['loans']['avg_approval_time'] }}h</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Feed --}}
        @if(count($statistics['activity']) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('staff.recent_activity.title') }}
                    </h2>
                    <div class="space-y-4">
                        @foreach($statistics['activity'] as $activity)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        <strong>{{ ucfirst(str_replace('_', ' ', $activity['type'])) }}</strong>
                                        @if($activity['subject_title'])
                                            - {{ $activity['subject_title'] }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $activity['created_at_human'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Loading State --}}
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600" role="status" aria-label="{{ __('common.loading') }}">
                <span class="sr-only">{{ __('common.loading') }}</span>
            </div>
        </div>
    @endif

    {{-- Floating Action Button (Mobile Only) --}}
    <div class="fab-container md:hidden">
        {{-- FAB Button --}}
        <button
            type="button"
            data-fab-button
            class="fab-button touch-target"
            aria-label="{{ __('staff.dashboard.fab.toggle_menu') }}"
            aria-expanded="false"
            aria-controls="fab-menu"
        >
            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>

        {{-- FAB Menu --}}
        <div
            id="fab-menu"
            data-fab-menu
            class="hidden absolute bottom-16 right-0 flex flex-col gap-3"
        >
            {{-- Quick Submit --}}
            <a
                href="{{ route('tickets.create') }}"
                class="fab-menu-item touch-target"
                data-touchable
                aria-label="{{ __('staff.dashboard.fab.new_ticket') }}"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.new_ticket') }}</span>
            </a>

            {{-- View My Submissions --}}
            <a
                href="{{ route('staff.my-submissions') }}"
                class="fab-menu-item touch-target"
                data-touchable
                aria-label="{{ __('staff.dashboard.fab.my_submissions') }}"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                </svg>
                <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.my_submissions') }}</span>
            </a>

            {{-- Refresh --}}
            <button
                wire:click="refreshStatistics"
                type="button"
                class="fab-menu-item touch-target"
                data-touchable
                aria-label="{{ __('staff.dashboard.fab.refresh') }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.refresh') }}</span>
            </button>
        </div>
    </div>
</div>
