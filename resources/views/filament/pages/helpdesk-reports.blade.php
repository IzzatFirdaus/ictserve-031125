<x-filament-panels::page>
    {{-- Report Filters Form --}}
    <x-filament-panels::form wire:submit="generateReport">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    @if($reportData)
        {{-- Report Period --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Report Period') }}
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Start Date') }}</p>
                    <p class="text-lg font-semibold">{{ $reportData['period']['start'] }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('End Date') }}</p>
                    <p class="text-lg font-semibold">{{ $reportData['period']['end'] }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Ticket Volume Statistics --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Ticket Volume Statistics') }}
            </x-slot>

            <div class="space-y-6">
                {{-- Total Tickets --}}
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Tickets') }}</p>
                    <p class="text-3xl font-bold text-primary-600">{{ number_format($reportData['volume']['total']) }}</p>
                </div>

                {{-- By Status --}}
                <div>
                    <h4 class="text-sm font-semibold mb-3">{{ __('By Status') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($reportData['volume']['by_status'] as $status => $count)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $status }}</p>
                                <p class="text-2xl font-bold">{{ number_format($count) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- By Priority --}}
                <div>
                    <h4 class="text-sm font-semibold mb-3">{{ __('By Priority') }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($reportData['volume']['by_priority'] as $priority => $count)
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $priority }}</p>
                                <p class="text-2xl font-bold">{{ number_format($count) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Guest vs Authenticated --}}
                <div>
                    <h4 class="text-sm font-semibold mb-3">{{ __('Submission Type') }}</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <p class="text-xs text-blue-600 dark:text-blue-400 uppercase">{{ __('Guest Submissions') }}</p>
                            <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ number_format($reportData['volume']['guest_vs_authenticated']['guest']) }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <p class="text-xs text-green-600 dark:text-green-400 uppercase">{{ __('Authenticated Submissions') }}</p>
                            <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ number_format($reportData['volume']['guest_vs_authenticated']['authenticated']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Resolution Time Statistics --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Resolution Time Statistics') }}
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Total Resolved') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($reportData['resolution_times']['total_resolved']) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Average (Hours)') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($reportData['resolution_times']['average_hours'], 1) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Median (Hours)') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($reportData['resolution_times']['median_hours'], 1) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Min (Hours)') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($reportData['resolution_times']['min_hours'], 1) }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Max (Hours)') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($reportData['resolution_times']['max_hours'], 1) }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- SLA Compliance Statistics --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('SLA Compliance Statistics') }}
            </x-slot>

            <div class="space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Total with SLA') }}</p>
                        <p class="text-2xl font-bold">{{ number_format($reportData['sla_compliance']['total_with_sla']) }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <p class="text-xs text-green-600 dark:text-green-400 uppercase">{{ __('Met SLA') }}</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ number_format($reportData['sla_compliance']['met_sla']) }}</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <p class="text-xs text-red-600 dark:text-red-400 uppercase">{{ __('Breached SLA') }}</p>
                        <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ number_format($reportData['sla_compliance']['breached_sla']) }}</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 uppercase">{{ __('At Risk') }}</p>
                        <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ number_format($reportData['sla_compliance']['at_risk']) }}</p>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
                        <p class="text-xs text-primary-600 dark:text-primary-400 uppercase">{{ __('Compliance Rate') }}</p>
                        <p class="text-2xl font-bold text-primary-700 dark:text-primary-300">{{ number_format($reportData['sla_compliance']['compliance_rate'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Agent Performance Statistics --}}
        @if(count($reportData['agent_performance']) > 0)
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Agent Performance Statistics') }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">{{ __('Agent Name') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ __('Total Assigned') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ __('Total Resolved') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ __('Resolution Rate') }}</th>
                                <th class="px-4 py-3 text-right font-semibold">{{ __('Avg Resolution (Hours)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($reportData['agent_performance'] as $agent)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3">{{ $agent['agent_name'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($agent['total_assigned']) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($agent['total_resolved']) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $agent['resolution_rate'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' }}">
                                            {{ number_format($agent['resolution_rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ number_format($agent['average_resolution_hours'], 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        {{-- Report Metadata --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Report Information') }}
            </x-slot>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                <p>{{ __('Generated at') }}: {{ $reportData['generated_at'] }}</p>
                <p class="mt-1">{{ __('Use the "Export Data" button above to download detailed ticket data in CSV or Excel format.') }}</p>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">{{ __('Click "Generate Report" to view analytics') }}</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
