{{--
    name: submission-history.blade.php
    description: Unified submission history interface with tabbed navigation, advanced filtering, and saved searches
    author: dev-team@motac.gov.my
    trace: D03 SRS-FR-001 Â§2.1-2.5; D12 Â§3; D14 Â§9 (WCAG 2.2 AA)
    last-updated: 2025-11-06
--}}

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

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-700" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabbed Navigation --}}
    <div class="mb-6" role="tablist" aria-label="{{ __('portal.history_tablist_label') }}">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="{{ __('portal.tabs_label') }}">
                {{-- Helpdesk Tab --}}
                <button
                    wire:click="switchTab('helpdesk')"
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeTab === 'helpdesk' ? 'true' : 'false' }}"
                    aria-controls="helpdesk-panel"
                    class="group inline-flex items-center border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900
                        @if($activeTab === 'helpdesk')
                            border-amber-500 text-amber-600 dark:text-amber-400
                        @else
                            border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300
                        @endif"
                >
                    <svg class="-ml-0.5 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                    {{ __('portal.history_helpdesk_tab') }}
                </button>

                {{-- Loans Tab --}}
                <button
                    wire:click="switchTab('loans')"
                    type="button"
                    role="tab"
                    aria-selected="{{ $activeTab === 'loans' ? 'true' : 'false' }}"
                    aria-controls="loans-panel"
                    class="group inline-flex items-center border-b-2 py-4 px-1 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900
                        @if($activeTab === 'loans')
                            border-amber-500 text-amber-600 dark:text-amber-400
                        @else
                            border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300
                        @endif"
                >
                    <svg class="-ml-0.5 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                    </svg>
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
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            id="search"
                            name="search"
                            class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                            placeholder="{{ $activeTab === 'helpdesk' ? __('portal.search_placeholder_helpdesk') : __('portal.search_placeholder_loans') }}"
                            aria-label="{{ __('portal.search_submissions') }}"
                        />
                    </div>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.status') }}
                    </label>
                    <select
                        wire:model.live="statusFilter"
                        id="status-filter"
                        name="status"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white"
                        aria-label="{{ __('portal.filter_by_status') }}"
                    >
                        @foreach($this->availableStatuses as $value => $label)
                            <option wire:key="history-status-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category/Asset Type Filter --}}
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $activeTab === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}
                    </label>
                    <select
                        wire:model.live="categoryFilter"
                        id="category-filter"
                        name="category"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white"
                        aria-label="{{ __('portal.filter_by_category') }}"
                    >
                        @foreach($this->availableCategories as $value => $label)
                            <option wire:key="history-category-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Priority Filter (Helpdesk Only) --}}
                @if($activeTab === 'helpdesk')
                    <div>
                        <label for="priority-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('portal.priority') }}
                        </label>
                        <select
                            wire:model.live="priorityFilter"
                            id="priority-filter"
                            name="priority"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white"
                            aria-label="{{ __('portal.filter_by_priority') }}"
                        >
                            @foreach($this->availablePriorities as $value => $label)
                                <option wire:key="history-priority-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Date From --}}
                <div>
                    <label for="date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.date_from') }}
                    </label>
                    <input
                        type="date"
                        wire:model.live="dateFrom"
                        id="date-from"
                        name="date_from"
                        class="mt-1 focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                        aria-label="{{ __('portal.start_date') }}"
                    />
                </div>

                {{-- Date To --}}
                <div>
                    <label for="date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('portal.date_to') }}
                    </label>
                    <input
                        type="date"
                        wire:model.live="dateTo"
                        id="date-to"
                        name="date_to"
                        class="mt-1 focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                        aria-label="{{ __('portal.end_date') }}"
                    />
                </div>
            </div>

            {{-- Filter Actions --}}
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($this->hasActiveFilters)
                        <button
                            wire:click="clearFilters"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 min-w-[44px] min-h-[44px]"
                            aria-label="{{ __('portal.clear_filters') }}"
                        >
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            {{ __('portal.clear_filters') }}
                        </button>
                    @endif

                    <button
                        wire:click="openSaveSearchModal"
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 min-w-[44px] min-h-[44px]"
                        aria-label="{{ __('portal.save_search_aria') }}"
                    >
                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                        </svg>
                        {{ __('portal.save_search') }}
                    </button>
                </div>

                {{-- Saved Searches Dropdown --}}
                @if(count($this->savedSearches) > 0)
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 min-w-[44px] min-h-[44px]"
                            aria-haspopup="true"
                            :aria-expanded="open"
                            aria-label="{{ __('portal.saved_searches_aria') }}"
                        >
                            <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('portal.saved_searches') }} ({{ count($this->savedSearches) }})
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="origin-top-right absolute right-0 mt-2 w-72 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 focus:outline-none z-10"
                            role="menu"
                            aria-orientation="vertical"
                            style="display: none;"
                        >
                            @foreach($this->savedSearches as $search)
                                <div wire:key="saved-search-{{ $search['id'] }}" class="py-1">
                                    <div class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <button
                                            wire:click="applySavedSearch({{ $search['id'] }})"
                                            @click="open = false"
                                            type="button"
                                            class="flex-1 text-left text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 rounded min-h-[44px] flex items-center"
                                            role="menuitem"
                                        >
                                            {{ $search['name'] }}
                                        </button>
                                        <button
                                            wire:click="deleteSavedSearch({{ $search['id'] }})"
                                            type="button"
                                            class="ml-2 p-1 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 rounded min-w-[32px] min-h-[32px]"
                                            aria-label="{{ __('portal.delete_search') }}"
                                            title="{{ __('portal.delete') }}"
                                        >
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Submissions Table --}}
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
        {{-- Loading State --}}
        <div wire:loading class="absolute inset-0 bg-white/75 dark:bg-gray-800/75 flex items-center justify-center z-10">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-amber-500 transition ease-in-out duration-150">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('portal.loading') }}
            </div>
        </div>

        @if($this->submissions->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ __('portal.no_submissions_found') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->hasActiveFilters ? __('portal.adjust_filters_message') : __('portal.no_submissions_yet') }}
                </p>
                @if(!$this->hasActiveFilters)
                    <div class="mt-6">
                        <a
                            href="{{ $activeTab === 'helpdesk' ? route('helpdesk.create') : route('loans.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 min-w-[44px] min-h-[44px]"
                        >
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $activeTab === 'helpdesk' ? __('portal.create_helpdesk_ticket') : __('portal.create_loan_application') }}
                        </a>
                    </div>
                @endif
            </div>
        @else
            {{-- Submissions Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @if($activeTab === 'helpdesk')
                                {{-- Helpdesk Columns --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.ticket_number') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.subject') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.category') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.priority') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.created_on') }}
                                </th>
                            @else
                                {{-- Loan Columns --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.application_number') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.asset') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.loan_period') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('portal.requested_on') }}
                                </th>
                            @endif
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">{{ __('portal.actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->submissions as $submission)
                            <tr wire:key="submission-{{ $activeTab }}-{{ $submission->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                @if($activeTab === 'helpdesk')
                                    {{-- Helpdesk Row --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $submission->ticket_no }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ Str::limit($submission->subject ?? $submission->description, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('helpdesk.categories.' . $submission->category) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($submission->priority === 'urgent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($submission->priority === 'high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @elseif($submission->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @endif">
                                            {{ __('helpdesk.priorities.' . $submission->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($submission->status === 'closed') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @elseif($submission->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($submission->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($submission->status === 'assigned') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                            @endif">
                                            {{ __('helpdesk.statuses.' . $submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->created_at->format('d/m/Y') }}
                                    </td>
                                @else
                                    {{-- Loan Row --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        LOAN-{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $submission->items->first()->asset->name ?? __('portal.not_applicable') }}
                                        @if($submission->items->count() > 1)
                                            <span class="text-gray-500 dark:text-gray-400">+{{ $submission->items->count() - 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->start_date->format('d/m/Y') }} - {{ $submission->end_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($submission->status === 'returned') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                            @elseif($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($submission->status === 'active') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($submission->status === 'overdue') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @endif">
                                            {{ __('loans.statuses.' . $submission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $submission->created_at->format('d/m/Y') }}
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a
                                        href="{{ $activeTab === 'helpdesk' ? route('helpdesk.show', $submission) : route('loans.show', $submission) }}"
                                        class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-500 rounded px-2 py-1 min-w-[44px] min-h-[44px] inline-flex items-center"
                                        aria-label="{{ __('portal.view_submission') }}"
                                    >
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
                {{ $this->submissions->links() }}
            </div>
        @endif
    </div>

    {{-- Save Search Modal --}}
    @if($showSaveSearchModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeSaveSearchModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    {{ __('portal.save_search') }}
                                </h3>
                                <div class="mt-4">
                                    <label for="search-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('portal.search_name') }}
                                    </label>
                                    <input
                                        type="text"
                                        wire:model="savedSearchName"
                                        id="search-name"
                                        name="search_name"
                                        maxlength="50"
                                        class="mt-1 focus:ring-amber-500 focus:border-amber-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                                        placeholder="{{ __('portal.search_name_placeholder') }}"
                                        aria-label="{{ __('portal.search_name') }}"
                                        aria-describedby="search-name-error"
                                    />
                                    @error('savedSearchName')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400" id="search-name-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="saveSearch"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 sm:ml-3 sm:w-auto sm:text-sm min-w-[44px] min-h-[44px]"
                        >
                            {{ __('portal.save') }}
                        </button>
                        <button
                            wire:click="closeSaveSearchModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-gray-900 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm min-w-[44px] min-h-[44px]"
                        >
                            {{ __('portal.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
