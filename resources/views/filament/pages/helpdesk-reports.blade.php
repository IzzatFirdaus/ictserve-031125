<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Form --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('admin_pages.helpdesk_reports.filters_heading') }}
            </x-slot>
            <x-slot name="description">
                {{ __('admin_pages.helpdesk_reports.filters_description') }}
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        @php
            $hasReport = $this->hasReport();
            $totalTickets = $this->reportData['total_tickets'] ?? 0;
        @endphp

        {{-- State 1: Not Generated Yet --}}
        @if (!$hasReport)
            <x-filament::section>
                <div class="text-center py-8">
                    <x-heroicon-o-document-chart-bar class="mx-auto h-12 w-12 text-gray-400" />
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('admin_pages.helpdesk_reports.empty_state') }}
                    </p>
                </div>
            </x-filament::section>

            {{-- State 2: Generated but No Data --}}
        @elseif($totalTickets === 0)
            <x-filament::section>
                <div class="text-center py-8">
                    <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-gray-400" />
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('admin_pages.helpdesk_reports.no_data') }}
                    </p>
                </div>
            </x-filament::section>

            {{-- State 3: Generated with Data --}}
        @else
            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Tickets --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">
                            {{ $this->reportData['total_tickets'] ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('admin_pages.helpdesk_reports.kpi_total_tickets') }}
                        </div>
                    </div>
                </x-filament::section>

                {{-- Guest Submissions --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-info-600">
                            {{ $this->reportData['guest_submissions'] ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('admin_pages.helpdesk_reports.kpi_guest_submissions') }}
                        </div>
                    </div>
                </x-filament::section>

                {{-- Average Resolution Time --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success-600">
                            {{ number_format($this->reportData['avg_resolution_time'] ?? 0, 1) }}j
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('admin_pages.helpdesk_reports.kpi_avg_resolution_time') }}
                        </div>
                    </div>
                </x-filament::section>

                {{-- SLA Compliance --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-warning-600">
                            {{ number_format($this->reportData['sla_compliance_rate'] ?? 0, 1) }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('admin_pages.helpdesk_reports.kpi_sla_compliance') }}
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Status Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('admin_pages.helpdesk_reports.by_status') }}
                </x-slot>

                @forelse($this->reportData['status_breakdown'] ?? [] as $status => $count)
                    @if ($loop->first)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @endif
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-2xl font-semibold">{{ $count }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ ucfirst($status) }}
                        </div>
                    </div>
                    @if ($loop->last)
    </div>
    @endif
@empty
    <div class="text-center py-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('admin_pages.helpdesk_reports.no_chart_data') }}
        </p>
    </div>
    @endforelse
    </x-filament::section>

    {{-- Priority Breakdown --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ __('admin_pages.helpdesk_reports.by_priority') }}
        </x-slot>

        @forelse($this->reportData['priority_breakdown'] ?? [] as $priority => $count)
            @if ($loop->first)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @endif
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-2xl font-semibold">{{ $count }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ ucfirst($priority) }}
                </div>
            </div>
            @if ($loop->last)
                </div>
            @endif
        @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.no_chart_data') }}
                </p>
            </div>
        @endforelse
    </x-filament::section>

    {{-- Category Breakdown --}}
    <x-filament::section>
        <x-slot name="heading">
            {{ __('admin_pages.helpdesk_reports.by_category') }}
        </x-slot>

        @forelse($this->reportData['category_breakdown'] ?? [] as $category)
            <div
                class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg @if (!$loop->last) mb-2 @endif">
                <span class="font-medium">{{ $category['name'] }}</span>
                <span class="text-lg font-semibold text-primary-600">{{ $category['count'] }}</span>
            </div>
        @empty
            <div class="text-center py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('admin_pages.helpdesk_reports.no_chart_data') }}
                </p>
            </div>
        @endforelse
    </x-filament::section>
    @endif
    </div>
</x-filament-panels::page>
