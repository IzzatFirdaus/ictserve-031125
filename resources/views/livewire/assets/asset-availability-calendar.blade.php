<div class="space-y-4">
    <div class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <button wire:click="previousMonth" type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span class="ml-2">{{ __('calendar.previous') }}</span>
        </button>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $monthName }}</h2>
        <button wire:click="nextMonth" type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 min-h-11 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
            <span class="mr-2">{{ __('calendar.next') }}</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="flex flex-wrap gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-success-500 rounded mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300">{{ __('calendar.available') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-danger-500 rounded mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300">{{ __('calendar.loaned') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-warning-500 rounded mr-2"></div>
                <span class="text-gray-700 dark:text-gray-300">{{ __('calendar.maintenance') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div
                    class="bg-gray-50 dark:bg-gray-800 p-2 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                    {{ __('calendar.days.' . strtolower($day)) }}
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
            @foreach ($calendarData as $day)
                @if ($day['isEmpty'])
                    <div class="bg-gray-50 dark:bg-gray-900 p-2 min-h-[100px]"></div>
                @else
                    <div
                        class="bg-white dark:bg-gray-800 p-2 min-h-[100px] {{ $day['isToday'] ? 'ring-2 ring-primary-500' : '' }} {{ $day['isPast'] ? 'opacity-60' : '' }}">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ $day['day'] }}</div>
                        @if ($day['totalAssets'] > 0)
                            <div class="space-y-1 text-xs">
                                @if ($day['availableCount'] > 0)
                                    <div class="flex items-center text-success-600 dark:text-success-400">
                                        <div class="w-2 h-2 bg-success-500 rounded-full mr-1"></div>
                                        <span>{{ $day['availableCount'] }} {{ __('calendar.available_count') }}</span>
                                    </div>
                                @endif
                                @if ($day['loanedCount'] > 0)
                                    <div class="flex items-center text-danger-600 dark:text-danger-400">
                                        <div class="w-2 h-2 bg-danger-500 rounded-full mr-1"></div>
                                        <span>{{ $day['loanedCount'] }} {{ __('calendar.loaned_count') }}</span>
                                    </div>
                                @endif
                                @if ($day['maintenanceCount'] > 0)
                                    <div class="flex items-center text-warning-600 dark:text-warning-400">
                                        <div class="w-2 h-2 bg-warning-500 rounded-full mr-1"></div>
                                        <span>{{ $day['maintenanceCount'] }}
                                            {{ __('calendar.maintenance_count') }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div wire:loading class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">{{ __('calendar.loading') }}</span>
            </div>
        </div>
    </div>
</div>
