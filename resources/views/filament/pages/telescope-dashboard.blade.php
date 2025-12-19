{{--
/**
 * View: Telescope Dashboard (Filament Page)
 * Description: Laravel Telescope debugging dashboard for superuser monitoring
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-NFR-003 (System Debugging)
 * @trace Requirements 17.1, 17.2, 17.3, 17.4, 17.5
 * @version 3.6.0
 * @created 2025-12-16
 */
--}}

<x-filament-panels::page>
    {{-- Introduction --}}
    <div class="mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('admin.telescope_dashboard_description') }}
        </p>
    </div>

    {{-- Telescope Categories Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
        {{-- Requests Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_requests') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_requests_description') }}
                    </p>
                </div>
                <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/requests" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_requests') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Exceptions Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_exceptions') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_exceptions_description') }}
                    </p>
                </div>
                <div class="p-3 bg-danger-100 dark:bg-danger-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/exceptions" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_exceptions') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Queries Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_queries') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_queries_description') }}
                    </p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/queries" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_queries') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Mail Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_mail') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_mail_description') }}
                    </p>
                </div>
                <div class="p-3 bg-success-100 dark:bg-success-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/mail" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_mail') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Jobs Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_jobs') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_jobs_description') }}
                    </p>
                </div>
                <div class="p-3 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-secondary-600 dark:text-secondary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/jobs" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_jobs') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Logs Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.telescope_logs') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.telescope_logs_description') }}
                    </p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('telescope') }}/logs" target="_blank"
                    class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded">
                    {{ __('admin.view_telescope_logs') }} →
                </a>
            </div>
        </x-filament::card>
    </div>

    {{-- Full Dashboard Link --}}
    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('admin.full_telescope_dashboard') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('admin.full_telescope_dashboard_description') }}
                </p>
            </div>
            <a href="{{ route('telescope') }}" target="_blank" class="btn-primary inline-flex items-center gap-2">
                {{ __('admin.open_telescope') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>

    {{-- ICTServe-Specific Tags Info --}}
    <div class="mt-6 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800">
        <h4 class="text-sm font-medium text-primary-800 dark:text-primary-200 mb-2">
            {{ __('admin.telescope_ictserve_tags') }}
        </h4>
        <p class="text-sm text-primary-700 dark:text-primary-300 mb-3">
            {{ __('admin.telescope_ictserve_tags_description') }}
        </p>
        <div class="flex flex-wrap gap-2">
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">helpdesk</span>
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">loan-approval</span>
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">asset-management</span>
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">email-delivery</span>
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">approval-workflow</span>
            <span
                class="px-2 py-1 text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-800 dark:text-primary-200 rounded">sla-tracking</span>
        </div>
    </div>
</x-filament-panels::page>
