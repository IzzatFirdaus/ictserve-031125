{{-- Staff Submission History Component - uses class-based component properties --}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" role="main" aria-label="{{ __('portal.history_title') }}">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('portal.history_title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('portal.history_subtitle') }}
        </p>
    </div>

    {{-- Tabbed Navigation --}}
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" role="tablist" aria-label="{{ __('portal.history_tablist_label') }}">
                {{-- Tickets Tab --}}
                <button wire:click="switchTab('tickets')" type="button" role="tab" id="tickets-tab"
                    aria-selected="{{ $activeTab === 'tickets' ? 'true' : 'false' }}" aria-controls="tickets-panel"
                    tabindex="{{ $activeTab === 'tickets' ? '0' : '-1' }}"
                    class="group inline-flex items-center border-b-2 py-4 px-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 min-h-11 min-w-11 {{ $activeTab === 'tickets' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-400 dark:text-gray-200 dark:hover:text-white' }}">
                    <x-heroicon-s-bell class="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.history_helpdesk_tab') }}
                </button>

                {{-- Loans Tab --}}
                <button wire:click="switchTab('loans')" type="button" role="tab" id="loans-tab"
                    aria-selected="{{ $activeTab === 'loans' ? 'true' : 'false' }}" aria-controls="loans-panel"
                    tabindex="{{ $activeTab === 'loans' ? '0' : '-1' }}"
                    class="group inline-flex items-center border-b-2 py-4 px-2 text-sm font-medium transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 min-h-11 min-w-11 {{ $activeTab === 'loans' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-700 hover:text-gray-900 hover:border-gray-400 dark:text-gray-200 dark:hover:text-white' }}">
                    <x-heroicon-s-document class="-ml-0.5 mr-2 h-5 w-5" aria-hidden="true" />
                    {{ __('portal.history_loans_tab') }}
                </button>
            </nav>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Search Input --}}
                <div class="col-span-full">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.search') }}
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" id="search" name="search"
                            class="focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
                            placeholder="{{ $activeTab === 'tickets' ? __('portal.search_placeholder_helpdesk') : __('portal.search_placeholder_loans') }}"
                            aria-label="{{ __('portal.search_submissions') }}" />
                    </div>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.status') }}
                    </label>
                    <select wire:model.live="statusFilter" id="status-filter" name="status" multiple
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
                        aria-label="{{ __('portal.filter_by_status') }}">
                        @if ($activeTab === 'tickets')
                            @foreach ($this->ticketStatusOptions as $value => $label)
                                <option wire:key="ticket-status-{{ $value }}" value="{{ $value }}">
                                    {{ $label }}</option>
                            @endforeach
                        @else
                            @foreach ($this->loanStatusOptions as $value => $label)
                                <option wire:key="loan-status-{{ $value }}" value="{{ $value }}">
                                    {{ $label }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label for="date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.date_from') }}
                    </label>
                    <input type="date" wire:model.live="dateFrom" id="date-from" name="date_from"
                        class="mt-1 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
                        aria-label="{{ __('portal.start_date') }}" />
                </div>

                {{-- Date To --}}
                <div>
                    <label for="date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.date_to') }}
                    </label>
                    <input type="date" wire:model.live="dateTo" id="date-to" name="date_to"
                        class="mt-1 focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white min-h-11"
                        aria-label="{{ __('portal.end_date') }}" />
                </div>
            </div>

            {{-- Filter Actions --}}
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <button wire:click="resetFilters" type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 min-w-11 min-h-11"
                        aria-label="{{ __('portal.clear_filters') }}">
                        <x-heroicon-o-x-mark class="-ml-0.5 mr-2 h-4 w-4" aria-hidden="true" />
                        {{ __('portal.clear_filters') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Submissions Table --}}
    <div id="{{ $activeTab }}-panel" role="tabpanel" aria-labelledby="{{ $activeTab }}-tab"
        class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden" aria-live="polite" tabindex="0"></div>

        @php
            $submissions = $activeTab === 'tickets' ? $this->filteredTickets : $this->filteredLoans;
        @endphp

        @if ($submissions->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-12">
                <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400" aria-hidden="true" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ __('portal.no_submissions_found') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('portal.no_submissions_yet') }}
                </p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @if ($activeTab === 'tickets')
                                {{-- Ticket Columns --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.ticket_number') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.subject') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.status') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.created_on') }}
                                </th>
                            @else
                                {{-- Loan Columns --}}
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.application_number') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.purpose') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.status') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.requested_on') }}
                                </th>
                            @endif
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('portal.actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($submissions as $submission)
                            <tr wire:key="submission-{{ $loop->iteration }}-{{ $submission->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group"
                                role="row">
                                @php
                                    $statusLabel = $submission->status instanceof \App\Enums\LoanStatus
                                        ? $submission->status->label()
                                        : ucfirst((string) $submission->status);
                                @endphp
                                @if ($activeTab === 'tickets')
                                    {{-- Ticket Row --}}
                                    <td scope="row"
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $submission->ticket_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($submission->subject ?? $submission->description, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->created_at->format('d/m/Y') }}
                                    </td>
                                @else
                                    {{-- Loan Row --}}
                                    <td scope="row"
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $submission->application_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($submission->purpose, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->created_at->format('d/m/Y') }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#"
                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-2 py-1 min-w-11 min-h-11 inline-flex items-center">
                                        {{ __('portal.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
