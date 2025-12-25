{{--
    name: submission-filters.blade.php
    description: Reusable filtering component with multi-select status, date range, category, and priority filters
    author: dev-team@motac.gov.my
    trace: SRS-FR-002; D04 §3.2; D12 §4; Requirements 8.2, 8.3
    last-updated: 2025-12-15
    WCAG 2.2 AA Compliant: Semantic HTML, ARIA attributes, 44×44px touch targets, keyboard navigation
--}}

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    {{-- Filter Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('portal.filters') }}
            </h3>
            @if($this->activeFilterCount > 0)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ trans_choice('portal.active_filters_count', $this->activeFilterCount, ['count' => $this->activeFilterCount]) }}
                </p>
            @endif
        </div>

        {{-- Clear Filters Button --}}
        @if($this->hasActiveFilters)
            <button
                type="button"
                wire:click="clearFilters"
                class="inline-flex items-center px-4 py-2 min-h-11 min-w-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 transition-colors duration-200"
                aria-label="{{ __('portal.clear_all_filters') }}"
            >
                <x-heroicon-o-x-mark class="w-4 h-4 mr-2" aria-hidden="true" />
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

            <div class="relative" x-data="{ open: false }" x-cloak>
                {{-- Status Dropdown Button --}}
                <button
                    type="button"
                    @click="open = !open"
                    @click.away="open = false"
                    class="w-full inline-flex items-center justify-between px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500"
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
                    <x-heroicon-o-chevron-down class="ml-2 h-5 w-5 text-gray-400" aria-hidden="true" />
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
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-lg py-1 text-base ring-1 ring-black/5 overflow-auto focus:outline-none sm:text-sm"
                    role="listbox"
                    style="display: none;"
                >
                    {{-- Select All / Deselect All --}}
                    <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600 flex gap-2">
                        <button
                            type="button"
                            wire:click="selectAllStatuses"
                            class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-3 py-2"
                        >
                            {{ __('portal.select_all') }}
                        </button>
                        <button
                            type="button"
                            wire:click="deselectAllStatuses"
                            class="text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 font-medium focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-3 py-2"
                        >
                            {{ __('portal.deselect_all') }}
                        </button>
                    </div>

                    {{-- Status Checkboxes --}}
                    @foreach($this->availableStatuses as $statusValue => $statusLabel)
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
                                class="h-4 w-4 text-primary-600 focus-visible:ring-3 focus-visible:ring-primary-500 border-gray-300 rounded cursor-pointer"
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
                class="w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500"
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
                class="w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500"
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
                class="w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm text-gray-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500"
                aria-label="{{ $filterType === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}"
            >
                <option value="">{{ __('portal.all_categories') }}</option>
                @foreach($this->availableCategories as $category)
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
                    class="w-full px-4 py-2 min-h-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm text-gray-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus:border-primary-500"
                    aria-label="{{ __('portal.priority') }}"
                >
                    <option value="">{{ __('portal.all_priorities') }}</option>
                    @foreach($this->availablePriorities as $priorityValue => $priorityLabel)
                        <option wire:key="priority-{{ $priorityValue }}" value="{{ $priorityValue }}">{{ $priorityLabel }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    {{-- Active Filter Chips --}}
    @if($this->hasActiveFilters)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 self-center">
                    {{ __('portal.active_filters') }}:
                </span>

                {{-- Status Chips --}}
                @foreach($selectedStatuses as $status)
                    <span wire:key="active-status-{{ $status }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200">
                        {{ $this->availableStatuses[$status] }}
                        <button
                            type="button"
                            wire:click="toggleStatus('{{ $status }}')"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-primary-200 dark:hover:bg-primary-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500"
                            aria-label="{{ __('portal.remove_filter', ['filter' => $this->availableStatuses[$status]]) }}"
                        >
                            <x-heroicon-s-x-mark class="w-3 h-3" aria-hidden="true" />
                        </button>
                    </span>
                @endforeach

                {{-- Date Range Chip --}}
                @if($dateFrom || $dateTo)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-info-100 dark:bg-info-900 text-info-800 dark:text-info-200">
                        {{ __('portal.date_range') }}:
                        {{ $dateFrom ?? __('portal.any') }} - {{ $dateTo ?? __('portal.any') }}
                        <button
                            type="button"
                            wire:click="$set('dateFrom', null); $set('dateTo', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-info-200 dark:hover:bg-info-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-info-500"
                            aria-label="{{ __('portal.remove_date_filter') }}"
                        >
                            <x-heroicon-s-x-mark class="w-3 h-3" aria-hidden="true" />
                        </button>
                    </span>
                @endif

                {{-- Category Chip --}}
                @if($selectedCategory)
                    @php
                        $categoryName = $this->availableCategories->firstWhere('id', $selectedCategory)?->name ?? '';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-secondary-100 dark:bg-secondary-900 text-secondary-800 dark:text-secondary-200">
                        {{ $filterType === 'helpdesk' ? __('portal.category') : __('portal.asset_type') }}:
                        {{ $categoryName }}
                        <button
                            type="button"
                            wire:click="$set('selectedCategory', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-secondary-200 dark:hover:bg-secondary-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-secondary-500"
                            aria-label="{{ __('portal.remove_category_filter') }}"
                        >
                            <x-heroicon-s-x-mark class="w-3 h-3" aria-hidden="true" />
                        </button>
                    </span>
                @endif

                {{-- Priority Chip (Helpdesk Only) --}}
                @if($filterType === 'helpdesk' && $selectedPriority)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-warning-100 dark:bg-warning-900 text-warning-800 dark:text-warning-200">
                        {{ __('portal.priority') }}: {{ $this->availablePriorities[$selectedPriority] }}
                        <button
                            type="button"
                            wire:click="$set('selectedPriority', null)"
                            class="ml-1 inline-flex items-center p-0.5 rounded-full hover:bg-warning-200 dark:hover:bg-warning-800 focus:outline-none focus-visible:ring-3 focus-visible:ring-warning-500"
                            aria-label="{{ __('portal.remove_priority_filter') }}"
                        >
                            <x-heroicon-s-x-mark class="w-3 h-3" aria-hidden="true" />
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
            class="w-full inline-flex items-center justify-center px-6 py-3 min-h-11 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg shadow-sm focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 transition-colors duration-200"
            aria-label="{{ __('portal.apply_filters') }}"
        >
            <x-heroicon-o-funnel class="w-5 h-5 mr-2" aria-hidden="true" />
            {{ __('portal.apply_filters') }}
        </button>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="applyFilters,clearFilters,toggleStatus,selectAllStatuses,deselectAllStatuses" class="absolute inset-0 bg-white/75 dark:bg-gray-800/75 flex items-center justify-center rounded-lg">
        <div class="flex flex-col items-center">
            <x-heroicon-o-arrow-path class="animate-spin h-10 w-10 text-primary-600" aria-hidden="true" />
            <span class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('portal.applying_filters') }}</span>
        </div>
    </div>
</div>

