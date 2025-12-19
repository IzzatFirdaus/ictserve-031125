<x-filament-panels::page>
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Compliance Audits --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
                    <x-heroicon-o-shield-check class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Compliance Audits') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_audits'] ?? 0) }}</p>
                    <p class="text-xs text-gray-400">{{ __('Today: :count', ['count' => $stats['audits_today'] ?? 0]) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Activity Logs --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-success-100 dark:bg-success-900 rounded-lg">
                    <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Activity Logs') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_activities'] ?? 0) }}</p>
                    <p class="text-xs text-gray-400">{{ __('Today: :count', ['count' => $stats['activities_today'] ?? 0]) }}</p>
                </div>
            </div>
        </div>

        {{-- Last 7 Days Audits --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-secondary-100 dark:bg-secondary-900 rounded-lg">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-secondary-600 dark:text-secondary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Audits (7 Days)') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['audits_last_7_days'] ?? 0) }}</p>
                </div>
            </div>
        </div>

        {{-- Last 7 Days Activities --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-warning-100 dark:bg-warning-900 rounded-lg">
                    <x-heroicon-o-clock class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Activities (7 Days)') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['activities_last_7_days'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>


    {{-- Tab Navigation --}}
    <div class="mb-6">
        <nav class="flex space-x-4" aria-label="{{ __('Audit Log Tabs') }}">
            <button
                wire:click="setTab('all')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $activeTab === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                aria-current="{{ $activeTab === 'all' ? 'page' : 'false' }}"
            >
                <x-heroicon-o-squares-2x2 class="w-4 h-4 inline-block mr-1" />
                {{ __('All Records') }}
            </button>

            <button
                wire:click="setTab('compliance')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $activeTab === 'compliance' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                aria-current="{{ $activeTab === 'compliance' ? 'page' : 'false' }}"
            >
                <x-heroicon-o-shield-check class="w-4 h-4 inline-block mr-1" />
                {{ __('Compliance Audits') }}
                <span class="ml-1 px-2 py-0.5 text-xs bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full">
                    owen-it
                </span>
            </button>

            <button
                wire:click="setTab('activity')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $activeTab === 'activity' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                aria-current="{{ $activeTab === 'activity' ? 'page' : 'false' }}"
            >
                <x-heroicon-o-clipboard-document-list class="w-4 h-4 inline-block mr-1" />
                {{ __('Activity Logs') }}
                <span class="ml-1 px-2 py-0.5 text-xs bg-success-100 dark:bg-success-900 text-success-800 dark:text-success-200 rounded-full">
                    spatie
                </span>
            </button>
        </nav>
    </div>

    {{-- Tab Description --}}
    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        @if($activeTab === 'all')
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <x-heroicon-o-information-circle class="w-4 h-4 inline-block mr-1" />
                {{ __('Showing combined view of compliance audits (field-level changes) and activity logs (user actions). Use tabs to filter by source.') }}
            </p>
        @elseif($activeTab === 'compliance')
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <x-heroicon-o-shield-check class="w-4 h-4 inline-block mr-1" />
                {{ __('Compliance Audits (owen-it/laravel-auditing): Field-level change tracking for PDPA 2010 compliance. Records old/new values for all auditable models.') }}
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <x-heroicon-o-clipboard-document-list class="w-4 h-4 inline-block mr-1" />
                {{ __('Activity Logs (spatie/laravel-activitylog): User activity tracking for operational dashboards. Records user actions with descriptions and context.') }}
            </p>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        {{ $this->table }}
    </div>

    {{-- Retention Notice --}}
    <div class="mt-4 p-3 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-800">
        <p class="text-sm text-warning-800 dark:text-warning-200">
            <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline-block mr-1" />
            {{ __('Audit records are retained for 7 years per PDPA 2010 and Arkib Negara requirements. Records are immutable and cannot be modified or deleted.') }}
        </p>
    </div>
</x-filament-panels::page>
