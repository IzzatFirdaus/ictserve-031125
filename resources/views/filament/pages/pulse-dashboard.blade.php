{{--
/**
 * View: Pulse Dashboard (Filament Page)
 * Description: Laravel Pulse metrics dashboard for superuser monitoring
 *
 * @author Pasukan BPM MOTAC
 * @trace D03-NFR-003 (Performance Monitoring)
 * @trace Requirements 16.1, 16.2
 * @version 3.5.0
 * @created 2025-12-07
 */
--}}

<x-filament-panels::page>
    {{-- Introduction --}}
    <div class="mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('admin.pulse_dashboard_description') }}
        </p>
    </div>

    {{-- Pulse Metrics Grid --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 xl:grid-cols-3">
        {{-- Usage Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_usage') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_usage_description') }}
                    </p>
                </div>
                <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_usage') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Queues Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_queues') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_queues_description') }}
                    </p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_queues') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Cache Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_cache') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_cache_description') }}
                    </p>
                </div>
                <div class="p-3 bg-success-100 dark:bg-success-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_cache') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Slow Queries Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_slow_queries') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_slow_queries_description') }}
                    </p>
                </div>
                <div class="p-3 bg-danger-100 dark:bg-danger-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_queries') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Slow Requests Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_slow_requests') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_slow_requests_description') }}
                    </p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_requests') }} →
                </a>
            </div>
        </x-filament::card>

        {{-- Exceptions Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('admin.pulse_exceptions') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('admin.pulse_exceptions_description') }}
                    </p>
                </div>
                <div class="p-3 bg-secondary-100 dark:bg-secondary-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-secondary-600 dark:text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('pulse') }}" 
                   target="_blank"
                   class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded">
                    {{ __('admin.view_pulse_exceptions') }} →
                </a>
            </div>
        </x-filament::card>
    </div>

    {{-- Full Dashboard Link --}}
    <div class="mt-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('admin.full_pulse_dashboard') }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('admin.full_pulse_dashboard_description') }}
                </p>
            </div>
            <a href="{{ route('pulse') }}" 
               target="_blank"
               class="btn-primary inline-flex items-center gap-2">
                {{ __('admin.open_pulse') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>
</x-filament-panels::page>
