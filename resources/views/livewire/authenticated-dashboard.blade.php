{{--
    name: authenticated-dashboard.blade.php
    description: PKS 5.2.1 Compliant staff dashboard - SSO-only architecture
    author: dev-team@motac.gov.my
    trace: D03 SRS-FR-006, D12 §3, D14 §9 (Requirements 1.1-1.4, 25.1, WCAG 2.2 AA)
    last-updated: 2025-12-25
    wcag: 2.2 AA compliant - color contrast 4.5:1, touch targets 44px, focus indicators
    myds: Uses MyDS design tokens from D13 §2.2-2.7
    pks-compliance: PKS 5.2.1 - SSO-only architecture, no guest access
--}}

<div wire:poll.300s="refreshStatistics" class="space-y-6">
    {{-- Dashboard Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold font-heading text-gray-900 dark:text-gray-100">
            {{ __('staff.dashboard.welcome', ['name' => $user->name]) }}
        </h1>
        <button wire:click="refreshStatistics" type="button"
            class="inline-flex items-center min-h-11 min-w-11 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 shadow-button transition-colors duration-200"
            aria-label="{{ __('staff.dashboard.refresh_aria') }}">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ __('staff.dashboard.refresh_button') }}
        </button>
    </div>

    {{-- Statistics Grid - MyDS shadow-card, spacing per D13 §2.6 --}}
    @if ($statistics)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Submissions --}}
            <article
                class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 theme-transition transition-shadow duration-200 hover:shadow-dropdown">
                <div class="flex items-center gap-4">
                    <div class="shrink-0 bg-primary-500 rounded-lg p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path
                                d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('staff.dashboard.summary.total_submissions') }}</p>
                        <p class="text-2xl font-bold font-heading text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['total_submissions'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </article>

            {{-- Pending Actions --}}
            <article
                class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 theme-transition transition-shadow duration-200 hover:shadow-dropdown">
                <div class="flex items-center gap-4">
                    <div class="shrink-0 bg-warning-500 rounded-lg p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('staff.dashboard.summary.pending_actions') }}</p>
                        <p class="text-2xl font-bold font-heading text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['pending_actions'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </article>

            {{-- Recent Updates (7 days) --}}
            <article
                class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 theme-transition transition-shadow duration-200 hover:shadow-dropdown">
                <div class="flex items-center gap-4">
                    <div class="shrink-0 bg-success-500 rounded-lg p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('staff.dashboard.summary.recent_updates') }}</p>
                        <p class="text-2xl font-bold font-heading text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['recent_updates'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </article>

            {{-- Profile Completeness --}}
            <article
                class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6 theme-transition transition-shadow duration-200 hover:shadow-dropdown">
                <div class="flex items-center gap-4">
                    <div class="shrink-0 bg-secondary-500 rounded-lg p-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('staff.dashboard.summary.profile_completeness') }}</p>
                        <p class="text-2xl font-bold font-heading text-gray-900 dark:text-gray-100">
                            {{ $statistics['summary']['profile_completeness'] ?? 0 }}%
                        </p>
                    </div>
                </div>
        </div>
</div>

{{-- Module Statistics - MyDS shadow-card per D14 §7.5 --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Helpdesk Statistics --}}
    <section class="bg-white dark:bg-gray-800 rounded-lg shadow-card theme-transition"
        aria-labelledby="helpdesk-stats-title">
        <div class="p-6">
            <h2 id="helpdesk-stats-title"
                class="text-lg font-semibold font-heading text-gray-900 dark:text-gray-100 mb-4">
                {{ __('staff.dashboard.helpdesk.title') }}
            </h2>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.total') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $statistics['helpdesk']['total'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.pending') }}
                    </dt>
                    <dd class="text-sm font-medium text-warning-600 dark:text-warning-400">
                        {{ $statistics['helpdesk']['pending'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.helpdesk.resolved') }}
                    </dt>
                    <dd class="text-sm font-medium text-success-600 dark:text-success-400">
                        {{ $statistics['helpdesk']['resolved'] }}</dd>
                </div>
                @if ($statistics['helpdesk']['avg_resolution_time'])
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('staff.dashboard.helpdesk.avg_resolution') }}</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $statistics['helpdesk']['avg_resolution_time'] }}h</dd>
                    </div>
                @endif
            </dl>
        </div>
    </section>

    {{-- Loan Statistics --}}
    <section class="bg-white dark:bg-gray-800 rounded-lg shadow-card theme-transition"
        aria-labelledby="loan-stats-title">
        <div class="p-6">
            <h2 id="loan-stats-title" class="text-lg font-semibold font-heading text-gray-900 dark:text-gray-100 mb-4">
                {{ __('staff.dashboard.loans.title') }}
            </h2>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.total') }}</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $statistics['loans']['total'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.common.pending') }}
                    </dt>
                    <dd class="text-sm font-medium text-warning-600 dark:text-warning-400">
                        {{ $statistics['loans']['pending'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.loans.approved') }}
                    </dt>
                    <dd class="text-sm font-medium text-success-600 dark:text-success-400">
                        {{ $statistics['loans']['approved'] }}</dd>
                </div>
                @if ($statistics['loans']['avg_approval_time'])
                    <div class="flex justify-between">
                        <span
                            class="text-sm text-gray-600 dark:text-gray-400">{{ __('staff.dashboard.loans.avg_approval') }}</span>
                        <span
                            class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $statistics['loans']['avg_approval_time'] }}h</span>
                    </div>
                @endif
        </div>
</div>
</div>
</div>

{{-- Recent Activity Feed --}}
@if (count($statistics['activity']) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('staff.recent_activity.title') }}
            </h2>
            <div class="space-y-4">
                @foreach ($statistics['activity'] as $activity)
                    <div class="flex items-start">
                        <div class="shrink-0">
                            <div
                                class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="currentColor"
                                    viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                <strong>{{ ucfirst(str_replace('_', ' ', $activity['type'])) }}</strong>
                                @if ($activity['subject_title'])
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
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600" role="status"
        aria-label="{{ __('common.loading') }}">
        <span class="sr-only">{{ __('common.loading') }}</span>
    </div>
</div>
@endif

{{-- Floating Action Button (Mobile Only) --}}
<div class="fab-container md:hidden">
    {{-- FAB Button --}}
    <button type="button" data-fab-button class="fab-button touch-target"
        aria-label="{{ __('staff.dashboard.fab.toggle_menu') }}" aria-expanded="false" aria-controls="fab-menu">
        <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
    </button>

    {{-- FAB Menu - Use x-show for proper toggle instead of conflicting hidden/flex --}}
    <div id="fab-menu" data-fab-menu x-data="{ open: false }" x-show="open" x-cloak
        class="absolute bottom-16 right-0 flex flex-col gap-3">
        {{-- Quick Submit --}}
        <a href="{{ route('tickets.create') }}" class="fab-menu-item touch-target" data-touchable
            aria-label="{{ __('staff.dashboard.fab.new_ticket') }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.new_ticket') }}</span>
        </a>

        {{-- View My Submissions --}}
        <a href="{{ route('staff.my-submissions') }}" class="fab-menu-item touch-target" data-touchable
            aria-label="{{ __('staff.dashboard.fab.my_submissions') }}">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd"
                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                    clip-rule="evenodd" />
            </svg>
            <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.my_submissions') }}</span>
        </a>

        {{-- Refresh --}}
        <button wire:click="refreshStatistics" type="button" class="fab-menu-item touch-target" data-touchable
            aria-label="{{ __('staff.dashboard.fab.refresh') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span class="ml-3 text-sm font-medium">{{ __('staff.dashboard.fab.refresh') }}</span>
        </button>
    </div>
</div>
</div>
