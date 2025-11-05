{{--
/**
 * Authenticated Staff Dashboard View
 *
 * Unified dashboard for authenticated MOTAC staff showing personalized statistics,
 * recent activity from both helpdesk and asset loan modules, and quick action buttons.
 *
 * Features:
 * - 4-column statistics grid (responsive)
 * - Real-time updates with wire:poll.30s
 * - Recent activity feed (tickets and loans)
 * - Quick action buttons
 * - Role-based content (Grade 41+ approval card)
 * - WCAG 2.2 Level AA compliant
 * - Compliant color palette
 *
 * @see D03-FR-019.1 Staff dashboard with personalized statistics
 * @see D03-FR-019.2 Recent activity display
 * @see D03-FR-019.3 Quick action buttons
 * @see D12 §9 WCAG 2.2 AA dashboard compliance
 * @see D14 §4 MOTAC compliant color palette
 *
 * @requirements 19.1, 19.2, 19.3, 19.4, 19.5
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-05
 *
 * @author Frontend Engineering Team
 */
--}}

@php
    use Illuminate\Support\Facades\Auth;
@endphp

<div wire:poll.30s="refreshData" class="py-6">
    {{-- Page Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ __('common.dashboard') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('common.welcome_back') }}, {{ Auth::user()->name }}
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button wire:click="refreshData" type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px]"
                    aria-label="{{ __('common.refresh_dashboard') }}">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ __('common.refresh') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Statistics Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- My Open Tickets Card --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-motac-blue" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ __('common.my_open_tickets') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $this->statistics['open_tickets'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('helpdesk.authenticated.tickets') }}"
                            class="font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                            {{ __('common.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- My Pending Loans Card --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-warning" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ __('common.my_pending_loans') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $this->statistics['pending_loans'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('loan.authenticated.history') }}"
                            class="font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                            {{ __('common.view_all') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- My Approvals Card (Grade 41+ only) --}}
            @if (Auth::user()->hasRole('approver') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('superuser'))
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        {{ __('common.pending_approvals') }}
                                    </dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            {{ $this->statistics['pending_approvals'] ?? 0 }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('staff.approvals.index') }}"
                                class="font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                                {{ __('common.review_approvals') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Overdue Items Card --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-danger" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ __('common.overdue_items') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $this->statistics['overdue_items'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('loan.authenticated.history') }}?status=overdue"
                            class="font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                            {{ __('common.view_overdue') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Section --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('common.quick_actions') }}
            </h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('helpdesk.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-motac-blue hover:bg-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('common.new_ticket') }}
                </a>
                <a href="{{ route('loan.guest.apply') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-motac-blue hover:bg-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('common.request_loan') }}
                </a>
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px]">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    {{ __('common.view_all_services') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Activity Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- My Recent Tickets --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('common.my_recent_tickets') }}
                    </h3>
                </div>
                <div class="px-6 py-4">
                    @if ($this->recentTickets->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">
                            {{ __('common.no_recent_tickets') }}
                        </p>
                    @else
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach ($this->recentTickets as $ticket)
                                <li class="py-4" wire:key="ticket-{{ $ticket->id }}">
                                    <div class="flex space-x-3">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    {{ $ticket->ticket_number }}
                                                </h4>
                                                <x-data.status-badge :status="$ticket->status" type="helpdesk" />
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                {{ Str::limit($ticket->subject, 60) }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $ticket->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <a href="{{ route('helpdesk.authenticated.tickets') }}"
                        class="text-sm font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                        {{ __('common.view_all_tickets') }}
                    </a>
                </div>
            </div>

            {{-- My Recent Loans --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('common.my_recent_loans') }}
                    </h3>
                </div>
                <div class="px-6 py-4">
                    @if ($this->recentLoans->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">
                            {{ __('common.no_recent_loans') }}
                        </p>
                    @else
                        <ul role="list" class="divide-y divide-gray-200">
                            @foreach ($this->recentLoans as $loan)
                                <li class="py-4" wire:key="loan-{{ $loan->id }}">
                                    <div class="flex space-x-3">
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    {{ $loan->application_number }}
                                                </h4>
                                                <x-data.status-badge :status="$loan->status->value" type="loan" />
                                            </div>
                                            <p class="text-sm text-gray-500">
                                                {{ $loan->loanItems->count() }} {{ __('common.items') }}
                                                @if ($loan->loanItems->isNotEmpty())
                                                    - {{ $loan->loanItems->first()->asset->name }}
                                                    @if ($loan->loanItems->count() > 1)
                                                        {{ __('common.and_more', ['count' => $loan->loanItems->count() - 1]) }}
                                                    @endif
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $loan->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <a href="{{ route('loan.authenticated.history') }}"
                        class="text-sm font-medium text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                        {{ __('common.view_all_loans') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading wire:target="refreshData"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-motac-blue" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ __('common.refreshing') }}...</span>
            </div>
        </div>
    </div>
</div>
