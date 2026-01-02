<x-filament-panels::page>
    {{-- Page Description --}}
    <div class="mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('admin_pages.asset_lifecycle_report.description') }}
        </p>
    </div>

    {{-- Summary KPI Cards --}}
    @php
        $kpis = $this->getSummaryKPIs();
    @endphp
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-primary-100 p-3 dark:bg-primary-900">
                    <x-heroicon-o-cube class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('admin_pages.asset_lifecycle_report.kpi_total') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($kpis['total_assets']) }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-info-100 p-3 dark:bg-info-900">
                    <x-heroicon-o-sparkles class="h-6 w-6 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('admin_pages.asset_lifecycle_report.kpi_new') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($kpis['new_assets']) }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-warning-100 p-3 dark:bg-warning-900">
                    <x-heroicon-o-wrench-screwdriver class="h-6 w-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('admin_pages.asset_lifecycle_report.kpi_maintenance') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($kpis['maintenance_due']) }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-danger-100 p-3 dark:bg-danger-900">
                    <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ __('admin_pages.asset_lifecycle_report.kpi_end_of_life') }}
                    </p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($kpis['end_of_life']) }}
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Filter Form --}}
    <x-filament::section class="mb-6">
        {{ $this->form }}
    </x-filament::section>

    {{-- Results Table --}}
    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
