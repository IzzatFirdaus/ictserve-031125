{{--
    name: recent-activity
    description: Activity feed with filtering blade view
    trace: D03 SRS-FR-001 §8.1-8.5; D12 §3; D14 §9
--}}

<div class="space-y-6">
    {{-- Header with Clear Filters --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ __('staff.recent_activity.title') }}
        </h2>
        @if($activityType !== 'all' || $dateFrom || $dateTo || $search)
            <button wire:click="clearFilters"
                    class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600"
                    aria-label="{{ __('staff.recent_activity.clear_filters_aria') }}">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('staff.recent_activity.clear_filters') }}
                </span>
            </button>
        @endif
    </div>

    {{-- Filters Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Activity Type Filter --}}
            <div>
                <label for="activityType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('staff.recent_activity.filters.activity_type') }}
                </label>
                <select wire:model.live="activityType"
                        id="activityType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                    @foreach($availableActivityTypes as $value => $label)
                        <option wire:key="activity-type-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date From Filter --}}
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('staff.recent_activity.filters.date_from') }}
                </label>
                <input type="date"
                       wire:model.live="dateFrom"
                       id="dateFrom"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            {{-- Date To Filter --}}
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('staff.recent_activity.filters.date_to') }}
                </label>
                <input type="date"
                       wire:model.live="dateTo"
                       id="dateTo"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>

            {{-- Search Filter --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('staff.recent_activity.filters.search') }}
                </label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       id="search"
                       placeholder="{{ __('staff.recent_activity.filters.search_placeholder') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
            </div>
        </div>
    </div>

    {{-- Activities Timeline --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        @if($activities->isEmpty())
            {{-- Empty State --}}
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('staff.recent_activity.empty.title') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($activityType !== 'all' || $dateFrom || $dateTo || $search)
                        {{ __('staff.recent_activity.empty.filtered') }}
                    @else
                        {{ __('staff.recent_activity.empty.default') }}
                    @endif
                </p>
            </div>
        @else
            {{-- Activity Items --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($activities as $activity)
                    <div wire:key="activity-{{ $activity->id }}" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start space-x-3">
                            {{-- Activity Icon --}}
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full
                                    @switch($activity->activity_type)
                                        @case('submission')
                                            bg-blue-100 dark:bg-blue-900/30
                                            @break
                                        @case('login')
                                            bg-green-100 dark:bg-green-900/30
                                            @break
                                        @case('update')
                                            bg-amber-100 dark:bg-amber-900/30
                                            @break
                                        @case('export')
                                            bg-purple-100 dark:bg-purple-900/30
                                            @break
                                        @case('claim')
                                            bg-indigo-100 dark:bg-indigo-900/30
                                            @break
                                        @case('approval')
                                            bg-emerald-100 dark:bg-emerald-900/30
                                            @break
                                        @case('comment')
                                            bg-gray-100 dark:bg-gray-700
                                            @break
                                        @default
                                            bg-gray-100 dark:bg-gray-700
                                    @endswitch
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5
                                        @switch($activity->activity_type)
                                            @case('submission')
                                                text-blue-600 dark:text-blue-400
                                                @break
                                            @case('login')
                                                text-green-600 dark:text-green-400
                                                @break
                                            @case('update')
                                                text-amber-600 dark:text-amber-400
                                                @break
                                            @case('export')
                                                text-purple-600 dark:text-purple-400
                                                @break
                                            @case('claim')
                                                text-indigo-600 dark:text-indigo-400
                                                @break
                                            @case('approval')
                                                text-emerald-600 dark:text-emerald-400
                                                @break
                                            @case('comment')
                                                text-gray-600 dark:text-gray-400
                                                @break
                                            @default
                                                text-gray-600 dark:text-gray-400
                                        @endswitch
                                    " fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        @switch($activity->activity_type)
                                            @case('submission')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                @break
                                            @case('login')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                @break
                                            @case('update')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                @break
                                            @case('export')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                @break
                                            @case('claim')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                @break
                                            @case('approval')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                @break
                                            @case('comment')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                @break
                                            @default
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @endswitch
                                    </svg>
                                </div>
                            </div>

                            {{-- Activity Details --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    @if($activity->subject)
                                        {{ class_basename($activity->subject_type) }}:
                                        <span class="font-medium">
                                            {{ $activity->subject->ticket_no ?? $activity->subject->loan_id ?? $activity->subject->id }}
                                        </span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
