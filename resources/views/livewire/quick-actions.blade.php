{{--
    name: quick-actions
    description: Quick action shortcuts blade view
    trace: D03 SRS-FR-001 §2; D12 §3; D14 §9
--}}

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    {{-- Section Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('staff.quick_actions.title') }} {{-- Quick Actions --}}
        </h2>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

        {{-- Action 1: New Helpdesk Ticket --}}
        <a href="{{ route('helpdesk.create') }}"
           class="flex flex-col items-center p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors duration-150 border border-blue-200 dark:border-blue-800 group focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800/50"
           aria-label="{{ __('staff.quick_actions.actions.helpdesk.aria') }}">
            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-blue-100 dark:bg-blue-900/40 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/60 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                {{ __('staff.quick_actions.actions.helpdesk.title') }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                {{ __('staff.quick_actions.actions.helpdesk.subtitle') }}
            </span>
        </a>

        {{-- Action 2: New Loan Application --}}
        <a href="{{ route('loan.authenticated.create') }}"
           class="flex flex-col items-center p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors duration-150 border border-green-200 dark:border-green-800 group focus:outline-none focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800/50"
           aria-label="{{ __('staff.quick_actions.actions.loan.aria') }}">
            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-green-100 dark:bg-green-900/40 group-hover:bg-green-200 dark:group-hover:bg-green-900/60 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                {{ __('staff.quick_actions.actions.loan.title') }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                {{ __('staff.quick_actions.actions.loan.subtitle') }}
            </span>
        </a>

        {{-- Action 3: My Profile --}}
        <a href="{{ route('staff.profile') }}"
           class="flex flex-col items-center p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition-colors duration-150 border border-purple-200 dark:border-purple-800 group focus:outline-none focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800/50"
           aria-label="{{ __('staff.quick_actions.actions.profile.aria') }}">
            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-purple-100 dark:bg-purple-900/40 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/60 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                {{ __('staff.quick_actions.actions.profile.title') }}
            </span>
            @if(auth()->user()->profile_completeness < 100)
                <span class="text-xs text-amber-600 dark:text-amber-400 text-center mt-1">
                    {{ auth()->user()->profile_completeness }}% {{ __('staff.quick_actions.actions.profile.incomplete_suffix') }}
                </span>
            @else
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                    {{ __('staff.quick_actions.actions.profile.complete_subtitle') }}
                </span>
            @endif
        </a>

        {{-- Action 4: Notifications --}}
        <a href="{{ route('staff.dashboard') }}"
           class="flex flex-col items-center p-4 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 rounded-lg transition-colors duration-150 border border-amber-200 dark:border-amber-800 group focus:outline-none focus:ring-4 focus:ring-amber-300 dark:focus:ring-amber-800/50 relative"
           aria-label="{{ __('staff.quick_actions.actions.notifications.aria') }}">
            @if($pendingNotificationsCount > 0)
                <span class="absolute top-2 right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
                      aria-label="{{ trans_choice('staff.quick_actions.actions.notifications.badge_aria', $pendingNotificationsCount, ['count' => $pendingNotificationsCount]) }}">
                    {{ $pendingNotificationsCount }}
                </span>
            @endif
            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-amber-100 dark:bg-amber-900/40 group-hover:bg-amber-200 dark:group-hover:bg-amber-900/60 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                {{ __('staff.quick_actions.actions.notifications.title') }}
            </span>
            @if($pendingNotificationsCount > 0)
                <span class="text-xs text-red-600 dark:text-red-400 text-center mt-1">
                    {{ $pendingNotificationsCount }} {{ __('staff.quick_actions.actions.notifications.badge_suffix') }}
                </span>
            @else
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                    {{ __('staff.quick_actions.actions.notifications.empty_subtitle') }}
                </span>
            @endif
        </a>

        {{-- Action 5: Export Data --}}
        <a href="{{ route('staff.history') }}"
           class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors duration-150 border border-gray-200 dark:border-gray-600 group focus:outline-none focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-700"
           aria-label="{{ __('staff.quick_actions.actions.export.aria') }}">
            <div class="flex items-center justify-center w-12 h-12 mb-3 rounded-full bg-gray-100 dark:bg-gray-600 group-hover:bg-gray-200 dark:group-hover:bg-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                {{ __('staff.quick_actions.actions.export.title') }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">
                {{ __('staff.quick_actions.actions.export.subtitle') }}
            </span>
        </a>

    </div>

    {{-- Optional: Claim Guest Submissions Banner --}}
    @if($hasClaimableSubmissions)
        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-200">
                        {{ __('staff.quick_actions.banner.title') }}
                    </p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        {{ __('staff.quick_actions.banner.message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
