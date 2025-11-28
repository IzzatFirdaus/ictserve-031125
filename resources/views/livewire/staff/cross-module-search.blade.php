{{--
/**
 * Cross-Module Search Component View
 *
 * Unified search interface across helpdesk tickets and loan applications
 * with advanced filtering, real-time suggestions, and export capabilities.
 *
 * @see D03-FR-011.2 (Cross-module search functionality)
 * @see D04 §5.2 (Cross-Module Search System)
 *
 * WCAG 2.2 AA Compliance:
 * - Semantic search form with proper labels
 * - Keyboard navigation for suggestions
 * - Screen reader announcements for results
 * - High contrast search interface
 */
--}}
<div class="space-y-6">
    {{-- Search Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                {{ __('staff.search.global_search') }}
            </h2>
            <button wire:click="toggleAdvancedFilters"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                aria-expanded="{{ $showAdvancedFilters ? 'true' : 'false' }}" aria-controls="advanced-filters">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                </svg>
                {{ __('staff.search.advanced_filters') }}
            </button>
        </div>

        {{-- Main Search Input --}}
        <div class="relative">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-lg"
                    placeholder="{{ __('staff.search.placeholder') }}"
                    aria-label="{{ __('staff.search.search_label') }}" aria-describedby="search-help"
                    autocomplete="off">
                @if ($search)
                    <button wire:click="clearFilters" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        aria-label="{{ __('staff.search.clear_search') }}">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Search Suggestions --}}
            @if ($showSuggestions && !empty($suggestions))
                <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                    <ul class="py-1" role="listbox" aria-label="{{ __('staff.search.suggestions') }}">
                        @foreach ($suggestions as $suggestion)
                            <li>
                                <button wire:click="selectSuggestion('{{ $suggestion }}')"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                                    role="option">
                                    {{ $suggestion }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="mt-3 flex items-center justify-between">
            <p id="search-help" class="text-sm text-gray-600">
                {{ __('staff.search.help_text') }}
            </p>
            <div class="flex items-center space-x-2">
                {{-- History Button --}}
                <button wire:click="toggleHistoryPanel"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-expanded="{{ $showHistoryPanel ? 'true' : 'false' }}" aria-controls="history-panel">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('staff.search.history') }}
                </button>
                {{-- Save Search Button --}}
                @if ($search)
                    <button wire:click="openSaveModal"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        {{ __('staff.search.save_search') }}
                    </button>
                @endif
            </div>
        </div>

        {{-- Search History & Saved Searches Panel --}}
        @if ($showHistoryPanel)
            <div id="history-panel" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Recent Searches --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-900">{{ __('staff.search.recent_searches') }}
                            </h4>
                            @if ($this->searchHistory->count() > 0)
                                <button wire:click="clearSearchHistory"
                                    wire:confirm="{{ __('staff.search.confirm_clear_history') }}"
                                    class="text-xs text-gray-500 hover:text-red-600">
                                    {{ __('staff.search.clear_history') }}
                                </button>
                            @endif
                        </div>
                        @if ($this->searchHistory->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($this->searchHistory as $historyItem)
                                    <li>
                                        <button wire:click="applySearch({{ $historyItem->id }})"
                                            class="w-full text-left px-3 py-2 text-sm text-gray-700 bg-white rounded-md border border-gray-200 hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium truncate">{{ $historyItem->query }}</span>
                                                <span class="text-xs text-gray-400 ml-2 shrink-0">
                                                    {{ $historyItem->last_used_at?->diffForHumans() ?? $historyItem->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            @if ($historyItem->result_count > 0)
                                                <span class="text-xs text-gray-500">
                                                    {{ trans_choice('staff.search.results_count', $historyItem->result_count, ['count' => $historyItem->result_count]) }}
                                                </span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 italic">{{ __('staff.search.no_recent_searches') }}</p>
                        @endif
                    </div>

                    {{-- Saved Searches --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ __('staff.search.saved_searches') }}
                        </h4>
                        @if ($this->savedSearches->count() > 0)
                            <ul class="space-y-2">
                                @foreach ($this->savedSearches as $savedItem)
                                    <li class="flex items-center space-x-2">
                                        <button wire:click="applySearch({{ $savedItem->id }})"
                                            class="flex-1 text-left px-3 py-2 text-sm text-gray-700 bg-white rounded-md border border-gray-200 hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-primary-500 mr-2 shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                                </svg>
                                                <span class="font-medium truncate">{{ $savedItem->name }}</span>
                                            </div>
                                            <span class="text-xs text-gray-500 ml-6">{{ $savedItem->query }}</span>
                                        </button>
                                        <button wire:click="deleteSavedSearch({{ $savedItem->id }})"
                                            wire:confirm="{{ __('staff.search.confirm_delete_saved') }}"
                                            class="p-2 text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 rounded"
                                            aria-label="{{ __('staff.search.delete_saved_search') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 italic">{{ __('staff.search.no_saved_searches') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Advanced Filters --}}
        @if ($showAdvancedFilters)
            <div id="advanced-filters" class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Module Filter --}}
                    <div>
                        <label for="module-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('staff.search.module') }}
                        </label>
                        <select wire:model.live="module" id="module-filter"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">{{ __('staff.search.all_modules') }}</option>
                            <option value="helpdesk">{{ __('staff.search.helpdesk_tickets') }}</option>
                            <option value="loans">{{ __('staff.search.loan_applications') }}</option>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('staff.search.status') }}
                        </label>
                        <select wire:model.live="status" id="status-filter"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="all">{{ __('staff.search.all_statuses') }}</option>
                            <option value="open">{{ __('staff.search.open') }}</option>
                            <option value="in_progress">{{ __('staff.search.in_progress') }}</option>
                            <option value="resolved">{{ __('staff.search.resolved') }}</option>
                            <option value="closed">{{ __('staff.search.closed') }}</option>
                            <option value="pending_approval">{{ __('staff.search.pending_approval') }}</option>
                            <option value="approved">{{ __('staff.search.approved') }}</option>
                            <option value="rejected">{{ __('staff.search.rejected') }}</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('staff.search.date_from') }}
                        </label>
                        <input wire:model.live="dateFrom" type="date" id="date-from"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('staff.search.date_to') }}
                        </label>
                        <input wire:model.live="dateTo" type="date" id="date-to"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="mt-4 flex items-center justify-between">
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ __('staff.search.clear_filters') }}
                    </button>
                    @if ($results && $results->total() > 0)
                        <button wire:click="exportResults"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('staff.search.export_results') }}
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>


    {{-- Search Results --}}
    @if ($search && $results)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            {{-- Results Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('staff.search.results_title') }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ trans_choice('staff.search.results_count', $results->total(), ['count' => $results->total()]) }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label for="per-page"
                            class="text-sm text-gray-700">{{ __('staff.search.per_page') }}:</label>
                        <select wire:model.live="perPage" id="per-page"
                            class="border-gray-300 rounded-md text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results List --}}
            @if ($results->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach ($results as $result)
                        <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    {{-- Result Header --}}
                                    <div class="flex items-center space-x-3 mb-2">
                                        {{-- Type Badge --}}
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result['type'] === 'ticket' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $result['type'] === 'ticket' ? __('staff.search.ticket') : __('staff.search.loan') }}
                                        </span>
                                        {{-- Number --}}
                                        <span class="text-sm font-mono text-gray-600">{{ $result['number'] }}</span>
                                        {{-- Status --}}
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst(str_replace('_', ' ', $result['status'])) }}
                                        </span>
                                    </div>

                                    {{-- Title and Description --}}
                                    <h4 class="text-lg font-medium text-gray-900 mb-1">
                                        <a href="{{ $result['url'] }}"
                                            class="hover:text-primary-600 focus:text-primary-600 focus:outline-none">
                                            {{ $result['title'] }}
                                        </a>
                                    </h4>
                                    @if ($result['description'] && $result['description'] !== $result['title'])
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ Str::limit($result['description'], 150) }}
                                        </p>
                                    @endif

                                    {{-- Metadata --}}
                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                        <span>{{ __('staff.search.by') }}: {{ $result['user_name'] }}</span>
                                        <span>{{ __('staff.search.category') }}: {{ $result['category'] }}</span>
                                        <span>{{ __('staff.search.created') }}:
                                            {{ $result['created_at']->format('M j, Y') }}</span>
                                        @if (isset($result['assets']) && $result['assets'])
                                            <span>{{ __('staff.search.assets') }}: {{ $result['assets'] }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <div class="ml-4 shrink-0">
                                    <a href="{{ $result['url'] }}"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary-700 bg-primary-100 border border-transparent rounded-md hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                        {{ __('staff.search.view_details') }}
                                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @else
                {{-- No Results --}}
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('staff.search.no_results') }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ __('staff.search.no_results_description') }}</p>
                    <div class="mt-6">
                        <button wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-700 bg-primary-100 border border-transparent rounded-md hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            {{ __('staff.search.clear_and_try_again') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @elseif($search)
        {{-- Loading State --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center" wire:loading>
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
            <p class="mt-4 text-sm text-gray-600">{{ __('staff.search.searching') }}</p>
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('staff.search.empty_state_title') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ __('staff.search.empty_state_description') }}</p>
        </div>
    @endif

    {{-- Loading Indicator --}}
    <div wire:loading.delay class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-25">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
                <span class="text-sm font-medium text-gray-900">{{ __('staff.search.loading') }}</span>
            </div>
        </div>
    </div>

    {{-- Save Search Modal --}}
    @if ($showSaveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="save-search-modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showSaveModal', false)"></div>

                {{-- Modal panel --}}
                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary-100">
                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="save-search-modal-title">
                                {{ __('staff.search.save_search_title') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ __('staff.search.save_search_description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="save-search-name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('staff.search.search_name') }}
                        </label>
                        <input wire:model="saveSearchName" type="text" id="save-search-name"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="{{ __('staff.search.search_name_placeholder') }}" autofocus>

                        <div class="mt-3 p-3 bg-gray-50 rounded-md">
                            <p class="text-xs text-gray-500 mb-1">{{ __('staff.search.search_query') }}:</p>
                            <p class="text-sm font-medium text-gray-900">{{ $search }}</p>
                            @if ($module !== 'all' || $status !== 'all' || $dateFrom || $dateTo)
                                <p class="text-xs text-gray-500 mt-2">{{ __('staff.search.filters_applied') }}:</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if ($module !== 'all')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('staff.search.module') }}: {{ $module }}
                                        </span>
                                    @endif
                                    @if ($status !== 'all')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('staff.search.status') }}: {{ $status }}
                                        </span>
                                    @endif
                                    @if ($dateFrom)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ __('staff.search.from') }}: {{ $dateFrom }}
                                        </span>
                                    @endif
                                    @if ($dateTo)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ __('staff.search.to') }}: {{ $dateTo }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button wire:click="saveCurrentSearch" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                            {{ __('staff.search.save') }}
                        </button>
                        <button wire:click="$set('showSaveModal', false)" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            {{ __('staff.search.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
