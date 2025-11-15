<x-filament-panels::page>
    <!-- Report Filters Form -->
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Report Filters') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Select date range for report generation') }}
        </x-slot>

        <form wire:submit="generateReport" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            {{ __('Start Date') }}
                        </span>
                    </label>
                    <input
                        type="date"
                        id="start_date"
                        wire:model="data.start_date"
                        class="fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:placeholder:text-gray-500 dark:focus:ring-primary-500 disabled:dark:bg-transparent disabled:dark:ring-white/10 sm:text-sm sm:leading-6"
                    >
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                            {{ __('End Date') }}
                        </span>
                    </label>
                    <input
                        type="date"
                        id="end_date"
                        wire:model="data.end_date"
                        class="fi-input block w-full rounded-lg border-none bg-white shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:bg-gray-50 disabled:text-gray-500 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:placeholder:text-gray-500 dark:focus:ring-primary-500 disabled:dark:bg-transparent disabled:dark:ring-white/10 sm:text-sm sm:leading-6"
                    >
                </div>
            </div>

            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-arrow-path">
                    {{ __('Generate Report') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <!-- Report Data Display -->
    @if ($reportData)
        <!-- Report Period -->
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Report Period') }}: {{ $reportData['period']['start'] }} - {{ $reportData['period']['end'] }}
            <span class="ml-4">{{ __('Generated') }}: {{ $reportData['generated_at'] }}</span>
        </div>

        <!-- Ticket Volume Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Ticket Volume') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <x-filament::section>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ __('Total Tickets') }}
                            </p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">
                                {{ $reportData['volume']['total'] }}
                            </p>
                        </div>
                        <div class="rounded-full bg-primary-500/10 p-3">
                            <x-heroicon-o-clipboard-document-list class="h-5 w-5 text-primary-500" />
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                            {{ __('By Status') }}
                        </p>
                        <div class="space-y-2">
                            @forelse ($reportData['volume']['by_status'] as $status => $count)
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium">{{ ucfirst($status) }}:</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('No data') }}</p>
                            @endforelse
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                            {{ __('By Priority') }}
                        </p>
                        <div class="space-y-2">
                            @forelse ($reportData['volume']['by_priority'] as $priority => $count)
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium">{{ ucfirst($priority) }}:</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('No data') }}</p>
                            @endforelse
                        </div>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                            {{ __('User Type') }}
                        </p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ __('Guest') }}:</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $reportData['volume']['guest_vs_authenticated']['guest'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ __('Authenticated') }}:</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $reportData['volume']['guest_vs_authenticated']['authenticated'] }}</span>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- By Category -->
            @if($reportData['volume']['by_category'])
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-white/10">
                    <p class="text-sm font-semibold mb-4">
                        {{ __('By Category') }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($reportData['volume']['by_category'] as $category => $count)
                            <div class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $category }}</p>
                                <p class="mt-1 text-2xl font-semibold">{{ $count }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-filament::section>

        <!-- Resolution Time Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Resolution Time Statistics') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <x-filament::section class="bg-blue-50 dark:bg-blue-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Average Hours') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">
                            {{ $reportData['resolution_times']['average_hours'] }}h
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-green-50 dark:bg-green-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Median Hours') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">
                            {{ $reportData['resolution_times']['median_hours'] }}h
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-yellow-50 dark:bg-yellow-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Minimum Hours') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">
                            {{ $reportData['resolution_times']['min_hours'] }}h
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-red-50 dark:bg-red-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Maximum Hours') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-red-600 dark:text-red-400">
                            {{ $reportData['resolution_times']['max_hours'] }}h
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-purple-50 dark:bg-purple-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Resolved') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-purple-600 dark:text-purple-400">
                            {{ $reportData['resolution_times']['total_resolved'] }}
                        </p>
                    </div>
                </x-filament::section>
            </div>
        </x-filament::section>

        <!-- SLA Compliance Statistics -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('SLA Compliance') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <x-filament::section>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('With SLA') }}</p>
                        <p class="mt-2 text-3xl font-semibold">
                            {{ $reportData['sla_compliance']['total_with_sla'] }}
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Resolved') }}</p>
                        <p class="mt-2 text-3xl font-semibold">
                            {{ $reportData['sla_compliance']['total_resolved'] }}
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-green-50 dark:bg-green-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Met SLA') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">
                            {{ $reportData['sla_compliance']['met_sla'] }}
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-red-50 dark:bg-red-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Breached') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-red-600 dark:text-red-400">
                            {{ $reportData['sla_compliance']['breached_sla'] }}
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-yellow-50 dark:bg-yellow-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('At Risk') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">
                            {{ $reportData['sla_compliance']['at_risk'] }}
                        </p>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-blue-50 dark:bg-blue-500/10">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Compliance Rate') }}</p>
                        <p class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">
                            {{ $reportData['sla_compliance']['compliance_rate'] }}%
                        </p>
                    </div>
                </x-filament::section>
            </div>
        </x-filament::section>

        <!-- Agent Performance Statistics -->
        @if ($reportData['agent_performance'])
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Agent Performance') }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="fi-ta-header-cell px-3 py-3.5 text-start sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ __('Agent') }}
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ __('Total Assigned') }}
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ __('Resolved') }}
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ __('Resolution Rate') }}
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-3 py-3.5 text-start">
                                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                        {{ __('Avg Resolution Hours') }}
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($reportData['agent_performance'] as $agent)
                                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                                        <div class="text-sm text-gray-950 dark:text-white">
                                            {{ $agent['agent_name'] }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-3 py-4">
                                        <div class="text-sm text-gray-950 dark:text-white">
                                            {{ $agent['total_assigned'] }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-3 py-4">
                                        <div class="text-sm text-gray-950 dark:text-white">
                                            {{ $agent['total_resolved'] }}
                                        </div>
                                    </td>
                                    <td class="fi-ta-cell px-3 py-4">
                                        @php
                                            $rate = $agent['resolution_rate'] ?? 0;
                                            $badgeColor = match(true) {
                                                $rate >= 80 => 'success',
                                                $rate >= 50 => 'warning',
                                                default => 'danger',
                                            };
                                        @endphp
                                        <x-filament::badge :color="$badgeColor">
                                            {{ number_format($rate, 1) }}%
                                        </x-filament::badge>
                                    </td>
                                    <td class="fi-ta-cell px-3 py-4">
                                        <div class="text-sm text-gray-950 dark:text-white">
                                            {{ $agent['average_resolution_hours'] }}h
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    @else
        <x-filament::section>
            <div class="text-center py-12">
                <x-heroicon-o-document-chart-bar class="mx-auto h-10 w-10 text-gray-400" />
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Generate a report to view analytics and statistics') }}
                </p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
