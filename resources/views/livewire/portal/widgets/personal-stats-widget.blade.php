{{--
/**
 * Personal Statistics Widget View
 * 
 * Displays personalized user statistics in a card format.
 * Supports real-time updates and responsive design.
 * 
 * @author Frontend Engineering Team
 * @version 1.0.0
 * @created 2025-11-28
 */
--}}

<div 
    class="personal-stats-widget"
    @if($liveUpdates && $pollInterval > 0)
        wire:poll.{{ $pollInterval }}s="refresh"
    @endif
    x-data="{ visible: @entangle('visible') }"
    x-show="visible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Open Tickets Stat --}}
        @if($config['show_tickets'])
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-blue-400 transition-colors">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">
                                    {{ __('dashboard.open_tickets') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" wire:loading.remove wire:target="refresh">
                                        {{ $this->openTicketsCount }}
                                    </div>
                                    <div wire:loading wire:target="refresh" class="text-sm text-gray-500">
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('staff.tickets.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-900 transition-colors">
                        {{ __('dashboard.view_all') }}
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Pending Loans Stat --}}
        @if($config['show_loans'])
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-green-400 transition-colors">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">
                                    {{ __('dashboard.pending_loans') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" wire:loading.remove wire:target="refresh">
                                        {{ $this->pendingLoansCount }}
                                    </div>
                                    <div wire:loading wire:target="refresh" class="text-sm text-gray-500">
                                        <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('staff.loans.index') }}" class="text-sm font-medium text-green-600 hover:text-green-900 transition-colors">
                        {{ __('dashboard.view_all') }}
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Pending Approvals Stat (Grade 41+ only) --}}
        @if($config['show_approvals'] && $this->pendingApprovalsCount > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-yellow-400 transition-colors">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">
                                    {{ __('dashboard.pending_approvals') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" wire:loading.remove wire:target="refresh">
                                        {{ $this->pendingApprovalsCount }}
                                    </div>
                                    <div wire:loading wire:target="refresh" class="text-sm text-gray-500">
                                        <svg class="animate-spin h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('staff.approvals.index') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-900 transition-colors">
                        {{ __('dashboard.view_all') }}
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Overdue Items Stat --}}
        @if($config['show_overdue'] && $this->overdueItemsCount > 0)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-red-400 transition-colors">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">
                                    {{ __('dashboard.overdue_items') }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900" wire:loading.remove wire:target="refresh">
                                        {{ $this->overdueItemsCount }}
                                    </div>
                                    <div wire:loading wire:target="refresh" class="text-sm text-gray-500">
                                        <svg class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ route('staff.loans.index', ['filter' => 'overdue']) }}" class="text-sm font-medium text-red-600 hover:text-red-900 transition-colors">
                        {{ __('dashboard.view_all') }}
                        <span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
