{{--
    name: recent-activity
    description: Activity feed with filtering blade view
    trace: D03 SRS-FR-001 §8.1-8.5; D12 §3; D14 §9
--}}

<div class="space-y-6">
    {{-- Header with Clear Filters --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
            {{ __('staff.recent_activity.title') }}
        </h2>
        @if($activityType !== 'all' || $dateFrom || $dateTo || $search)
            <button wire:click="clearFilters"
                    class="px-3 py-1.5 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-slate-300 dark:focus-visible:ring-3 focus-visible:ring-slate-600 min-h-11 min-w-11"
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
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Activity Type Filter --}}
            <div>
                <label for="activityType" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('staff.recent_activity.filters.activity_type') }}
                </label>
                <select wire:model.live="activityType"
                        id="activityType"
                        class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-3 focus-visible:ring-primary-400 min-h-11">
                    @foreach($availableActivityTypes as $value => $label)
                        <option wire:key="activity-type-{{ $value }}" value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date From Filter --}}
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('staff.recent_activity.filters.date_from') }}
                </label>
                <input type="date"
                       wire:model.live="dateFrom"
                       id="dateFrom"
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-3 focus-visible:ring-primary-400 min-h-11">
            </div>

            {{-- Date To Filter --}}
            <div>
                <label for="dateTo" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('staff.recent_activity.filters.date_to') }}
                </label>
                <input type="date"
                       wire:model.live="dateTo"
                       id="dateTo"
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-3 focus-visible:ring-primary-400 min-h-11">
            </div>

            {{-- Search Filter --}}
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('staff.recent_activity.filters.search') }}
                </label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       id="search"
                       placeholder="{{ __('staff.recent_activity.filters.search_placeholder') }}"
                       class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 dark:focus-visible:ring-3 focus-visible:ring-primary-400 min-h-11">
            </div>
        </div>
    </div>

    {{-- Activities Timeline --}}
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        @if($activities->isEmpty())
            {{-- Empty State --}}
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-sm font-medium text-slate-900 dark:text-white">
                    {{ __('staff.recent_activity.empty.title') }}
                </h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    @if($activityType !== 'all' || $dateFrom || $dateTo || $search)
                        {{ __('staff.recent_activity.empty.filtered') }}
                    @else
                        {{ __('staff.recent_activity.empty.default') }}
                    @endif
                </p>
            </div>
        @else
            {{-- Activity Items --}}
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach($activities as $activity)
                    <div wire:key="activity-{{ $activity->id }}" class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-start space-x-3">
                            {{-- Activity Icon --}}
                            <div class="shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full
                                    @switch($activity->activity_type)
                                        @case('submission')
                                            bg-primary-100 dark:bg-primary-900/30
                                            @break
                                        @case('login')
                                            bg-success-100 dark:bg-success-900/30
                                            @break
                                        @case('update')
                                            bg-warning-100 dark:bg-warning-900/30
                                            @break
                                        @case('export')
                                            bg-secondary-100 dark:bg-secondary-900/30
                                            @break
                                        @case('claim')
                                            bg-secondary-100 dark:bg-secondary-900/30
                                            @break
                                        @case('approval')
                                            bg-success-100 dark:bg-success-900/30
                                            @break
                                        @case('comment')
                                            bg-slate-100 dark:bg-slate-700
                                            @break
                                        @default
                                            bg-slate-100 dark:bg-slate-700
                                    @endswitch
                                ">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5
                                        @switch($activity->activity_type)
                                            @case('submission')
                                                text-primary-600 dark:text-primary-400
                                                @break
                                            @case('login')
                                                text-success-600 dark:text-success-400
                                                @break
                                            @case('update')
                                                text-warning-600 dark:text-warning-400
                                                @break
                                            @case('export')
                                                text-secondary-600 dark:text-secondary-400
                                                @break
                                            @case('claim')
                                                text-secondary-600 dark:text-secondary-400
                                                @break
                                            @case('approval')
                                                text-success-600 dark:text-success-400
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
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                </p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    @if($activity->subject)
                                        {{ class_basename($activity->subject_type) }}:
                                        <span class="font-medium">
                                            {{ $activity->subject->ticket_no ?? $activity->subject->loan_id ?? $activity->subject->id }}
                                        </span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
