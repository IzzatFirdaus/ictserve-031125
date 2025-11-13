{{--
    name: submission-filters.blade.php
    description: Reusable filtering component with multi-select status, date range, category, and priority filters
    author: dev-team@motac.gov.my
    trace: SRS-FR-002; D04 §3.2; D12 §4; Requirements 8.2, 8.3
    last-updated: 2025-11-06
    WCAG 2.2 AA Compliant: Semantic HTML, ARIA attributes, 44×44px touch targets, keyboard navigation
--}}

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    {{-- Filter Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('portal.filters') }}
            </h3>
            @if($activeFilterCount > 0)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ trans_choice('portal.active_filters_count', $activeFilterCount, ['count' => $activeFilterCount]) }}
                </p>
            @endif
        </div>

        {{-- Clear Filters Button --}}
        @if($hasActiveFilters)
            <button
                type="button"
                wire:click="clearFilters"
                class="inline-flex items-center px-4 py-2 min-h-[44px] min-w-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                aria-label="{{ __('portal.clear_all_filters') }}"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ __('portal.clear_filters') }}
            </button>
        @endif
    </div>

    {{-- Filter Controls Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Status Filter (Multi-Select) --}}
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('portal.status') }}
            </label>

            <div class="relative" x-data="{ open: false }">
                {{-- Status Dropdown Button --}}
                <button
                    type="button"
                    @click="open = !open"
                    @click.away="open = false"
                    class="w-full inline-flex items-center justify-between px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    :aria-expanded="open"
                >
                    <span class="truncate">
                        @if(empty($selectedStatuses))
                            {{ __('portal.select_status') }}
                        @else
                            {{ trans_choice('portal.statuses_selected', count($selectedStatuses), ['count' => count($selectedStatuses)]) }}
                        @endif
                    </span>
                    <svg class="ml-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Status Dropdown Menu --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
                    role="listbox"
                    style="display: none;"
                >
                    {{-- Select All / Deselect All --}}
                    <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600 flex gap-2">
                        <button
                            type="button"
                            wire:click="selectAllStatuses"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-3 py-2"
                        >
                            {{ __('portal.select_all') }}
                        </button>
                        <button
                            type="button"
                            wire:click="deselectAllStatuses"
                            class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-3 py-2"
                        >
                            {{ __('portal.deselect_all') }}
                        </button>
                    </div>

                    {{-- Status Checkboxes --}}
                    @foreach($availableStatuses as $statusValue => $statusLabel)
                        <label
                            wire:key="status-{{ $statusValue }}"
                            class="flex items-center px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                            role="option"
                            aria-selected="{{ in_array($statusValue, $selectedStatuses) ? 'true' : 'false' }}"
                        >
                            <input
                                type="checkbox"
                                wire:click="toggleStatus('{{ $statusValue }}')"
                                @checked(in_array($statusValue, $selectedStatuses))
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer"
                                aria-label="{{ $statusLabel }}"
                            >
                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $statusLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Date From Filter --}}
        <div class="space-y-2">
            <label for="filter-date-from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('portal.date_from') }}
            </label>
            <input
                type="date"
                id="filter-date-from"
                wire:model.live="dateFrom"
                class="w-full px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                aria-label="{{ __('portal.date_from') }}"
            >
        </div>

        {{-- Date To Filter --}}
        <div class="space-y-2">
            <label for="filter-date-to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('portal.date_to') }}
            </label>
            <input
                type="date"
                id="filter-date-to"
                wire:model.live="dateTo"
                class="w-full px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                aria-label="{{ __('portal.date_to') }}"
            >
        </div>

        {{-- Category Filter --}}
        <div class="space-y-2">
            <label for="filter-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $filterType === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}
            </label>
            <select
                id="filter-category"
                wire:model.live="selectedCategory"
                class="w-full px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                aria-label="{{ $filterType === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}"
            >
                <option value="">{{ __('portal.all_categories') }}</option>
                @foreach($availableCategories as $category)
                    <option wire:key="category-{{ $category->id }}" value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Priority Filter (Helpdesk Only) --}}
        @if($filterType === 'helpdesk')
            <div class="space-y-2">
                <label for="filter-priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('portal.priority') }}
                </label>
                <select
                    id="filter-priority"
                    wire:model.live="selectedPriority"
                    class="w-full px-4 py-2 min-h-[44px] bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    aria-label="{{ __('portal.priority') }}"
                >
                    <option value="">{{ __('portal.all_priorities') }}</option>
                    @foreach($availablePriorities as $priorityValue => $priorityLabel)
                        <option wire:key="priority-{{ $priorityValue }}" value="{{ $priorityValue }}">{{ $priorityLabel }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Active Filter Chips --}}
    @if($hasActiveFilters)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 self-center">
                    {{ __('portal.active_filters') }}:
                </span>

                {{-- Status Chips --}}
                @foreach($selectedStatuses as $status)
                    <span wire:key="active-status-{{ $status }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ $availableStatuses[$status] }}
                        <button
                            type="button"
                            wire:click="toggleStatus('{{ $status }}')"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="{{ __('portal.remove_filter', ['filter' => $availableStatuses[$status]]) }}"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                @endforeach

                {{-- Date Range Chip --}}
                @if($dateFrom || $dateTo)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        {{ __('portal.date_range') }}:
                        {{ $dateFrom ?? __('portal.any') }} - {{ $dateTo ?? __('portal.any') }}
                        <button
                            type="button"
                            wire:click="$set('dateFrom', null); $set('dateTo', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-green-200 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500"
                            aria-label="{{ __('portal.remove_date_filter') }}"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                @endif

                {{-- Category Chip --}}
                @if($selectedCategory)
                    @php
                        $categoryName = $availableCategories->firstWhere('id', $selectedCategory)?->name ?? '';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                        {{ $filterType === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}:
                        {{ $categoryName }}
                        <button
                            type="button"
                            wire:click="$set('selectedCategory', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-purple-200 dark:hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            aria-label="{{ __('portal.remove_category_filter') }}"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                @endif

                {{-- Priority Chip (Helpdesk Only) --}}
                @if($filterType === 'helpdesk' && $selectedPriority)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                        {{ __('portal.priority') }}: {{ $availablePriorities[$selectedPriority] }}
                        <button
                            type="button"
                            wire:click="$set('selectedPriority', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-orange-200 dark:hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500"
                            aria-label="{{ __('portal.remove_priority_filter') }}"
                        >
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                @endif
            </div>
        </div>
    @endif

    {{-- Apply Filters Button --}}
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <button
            type="button"
            wire:click="applyFilters"
            class="w-full inline-flex items-center justify-center px-6 py-3 min-h-[44px] bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            aria-label="{{ __('portal.apply_filters') }}"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            {{ __('portal.apply_filters') }}
        </button>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="applyFilters,clearFilters,toggleStatus,selectAllStatuses,deselectAllStatuses" class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center rounded-lg">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.applying_filters') }}</span>
        </div>
    </div>
</div>
