{{--
/**
 * Submission History View
 *
 * Displays comprehensive submission history for authenticated users with tabbed interface
 * for helpdesk tickets and loan applications. Includes search, filtering, sorting, and
 * pagination capabilities with WCAG 2.2 Level AA compliance.
 *
 * Features:
 * - Tabbed interface (My Tickets | My Loan Requests)
 * - Search functionality with debouncing (300ms)
 * - Status filtering for both tickets and loans
 * - Date range filtering
 * - Sortable columns with ARIA attributes
 * - Pagination with accessible controls
 * - Empty states for no results
 * - Loading states with wire:loading
 * - Responsive design (mobile, tablet, desktop)
 *
 * @see D03-FR-021.1 Submission history with tabbed interface
 * @see D03-FR-021.2 Ticket history display
 * @see D03-FR-021.3 Loan history display
 * @see D03-FR-021.4 Search and filter functionality
 * @see D12 §9 WCAG 2.2 AA compliance
 * @see D14 §4 MOTAC compliant color palette
 *
 * @requirements 21.1, 21.2, 21.3, 21.4
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

<div class="py-6">
    {{-- Page Header --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ __('common.submission_history') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('common.view_all_submissions') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tabbed Interface --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="bg-white shadow rounded-lg">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" role="tablist" aria-label="{{ __('common.submission_types') }}">
                    <button wire:click="switchTab('tickets')" type="button" role="tab"
                        aria-selected="{{ $activeTab === 'tickets' ? 'true' : 'false' }}" aria-controls="tickets-panel"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm min-h-[44px] focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 {{ $activeTab === 'tickets' ? 'border-motac-blue text-motac-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="inline-block h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        {{ __('common.my_tickets') }}
                    </button>
                    <button wire:click="switchTab('loans')" type="button" role="tab"
                        aria-selected="{{ $activeTab === 'loans' ? 'true' : 'false' }}" aria-controls="loans-panel"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm min-h-[44px] focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 {{ $activeTab === 'loans' ? 'border-motac-blue text-motac-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="inline-block h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        {{ __('common.my_loan_requests') }}
                    </button>
                </nav>
            </div>

            {{-- Search and Filters Section --}}
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Search Input --}}
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.search') }}
                        </label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search"
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-motac-blue focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 sm:text-sm"
                            placeholder="{{ $activeTab === 'tickets' ? __('common.search_tickets') : __('common.search_loans') }}"
                            aria-label="{{ __('common.search') }}">
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.status') }}
                        </label>
                        <select wire:model.live="statusFilter" id="status-filter"
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-motac-blue focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 sm:text-sm">
                            @if ($activeTab === 'tickets')
                                @foreach ($this->getTicketStatusOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            @else
                                @foreach ($this->getLoanStatusOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Date Range Filters --}}
                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.from_date') }}
                        </label>
                        <input wire:model.live="dateFrom" type="date" id="date-from"
                            class="block w-full min-h-[44px] px-3 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-motac-blue focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 sm:text-sm">
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="flex-1">
                        <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('common.to_date') }}
                        </label>
                        <input wire:model.live="dateTo" type="date" id="date-to"
                            class="block w-full max-w-xs min-h-[44px] px-3 py-2.5 rounded-md border-gray-300 shadow-sm focus:border-motac-blue focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 sm:text-sm">
                    </div>
                    <div class="ml-4">
                        <button wire:click="resetFilters" type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px] min-w-[44px]">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            {{ __('common.reset_filters') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tickets Tab Content --}}
            @if ($activeTab === 'tickets')
                <div id="tickets-panel" role="tabpanel" aria-labelledby="tickets-tab" class="p-6">
                    @if ($this->filteredTickets->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('common.no_tickets_found') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('common.try_adjusting_filters') }}</p>
                        </div>
                    @else
                        {{-- Tickets Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <button wire:click="sortBy('ticket_number')" type="button"
                                                class="group inline-flex items-center focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2"
                                                aria-sort="{{ $sortField === 'ticket_number' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                                {{ __('common.ticket_number') }}
                                                @if ($sortField === 'ticket_number')
                                                    <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        @if ($sortDirection === 'asc')
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 15l7-7 7 7" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        @endif
                                                    </svg>
                                                @endif
                                            </button>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.subject') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.status') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.priority') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <button wire:click="sortBy('created_at')" type="button"
                                                class="group inline-flex items-center focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2"
                                                aria-sort="{{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                                {{ __('common.created_date') }}
                                                @if ($sortField === 'created_at')
                                                    <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        @if ($sortDirection === 'asc')
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 15l7-7 7 7" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        @endif
                                                    </svg>
                                                @endif
                                            </button>
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('common.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($this->filteredTickets as $ticket)
                                        <tr wire:key="ticket-{{ $ticket->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $ticket->ticket_number }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ Str::limit($ticket->subject, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-data.status-badge :status="$ticket->status" type="helpdesk" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-data.status-badge :status="$ticket->priority" type="priority" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $ticket->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('helpdesk.show', $ticket) }}"
                                                    class="text-motac-blue hover:text-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2">
                                                    {{ __('common.view_details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $this->filteredTickets->links() }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Loans Tab Content --}}
            @if ($activeTab === 'loans')
                <div id="loans-panel" role="tabpanel" aria-labelledby="loans-tab" class="p-6">
                    @if ($this->filteredLoans->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('common.no_loans_found') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('common.try_adjusting_filters') }}</p>
                        </div>
                    @else
                        {{-- Loans Grid --}}
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($this->filteredLoans as $loan)
                                <div wire:key="loan-{{ $loan->id }}"
                                    class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="p-6">
                                        {{-- Application Number and Status --}}
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ $loan->application_number }}
                                            </h3>
                                            <x-data.status-badge :status="$loan->status" type="loan" />
                                        </div>

                                        {{-- Loan Details --}}
                                        <dl class="space-y-2">
                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase">
                                                    {{ __('common.items') }}
                                                </dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    {{ $loan->loanItems->count() }} {{ __('common.items') }}
                                                    @if ($loan->loanItems->isNotEmpty())
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            {{ $loan->loanItems->first()->asset->name }}
                                                            @if ($loan->loanItems->count() > 1)
                                                                {{ __('common.and_more', ['count' => $loan->loanItems->count() - 1]) }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase">
                                                    {{ __('common.loan_period') }}
                                                </dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    {{ $loan->loan_start_date->format('Y-m-d') }} -
                                                    {{ $loan->loan_end_date->format('Y-m-d') }}
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase">
                                                    {{ __('common.purpose') }}
                                                </dt>
                                                <dd class="mt-1 text-sm text-gray-900">
                                                    {{ Str::limit($loan->purpose, 60) }}
                                                </dd>
                                            </div>

                                            <div>
                                                <dt class="text-xs font-medium text-gray-500 uppercase">
                                                    {{ __('common.submitted_date') }}
                                                </dt>
                                                <dd class="mt-1 text-sm text-gray-500">
                                                    {{ $loan->created_at->format('Y-m-d') }}
                                                </dd>
                                            </div>
                                        </dl>

                                        {{-- View Details Button --}}
                                        <div class="mt-6">
                                            <a href="{{ route('loan.show', $loan) }}"
                                                class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-motac-blue hover:bg-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 min-h-[44px]">
                                                {{ __('common.view_details') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $this->filteredLoans->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading wire:target="switchTab,resetFilters,sortBy"
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
                <span class="text-sm font-medium text-gray-900">{{ __('common.loading') }}...</span>
            </div>
        </div>
    </div>
</div>
