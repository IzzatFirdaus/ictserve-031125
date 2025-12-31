<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filters Form --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Report Filters') }}
            </x-slot>
            <x-slot name="description">
                {{ __('Select date range for report generation') }}
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        {{-- Report Statistics --}}
        @if(!empty($this->reportData))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Tickets --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">
                            {{ $this->reportData['total_tickets'] ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('Total Tickets') }}
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
                            {{ __('Guest Submissions') }}
                        </div>
                    </div>
                </x-filament::section>

                {{-- Average Resolution Time --}}
                <x-filament::section>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success-600">
                            {{ number_format($this->reportData['avg_resolution_time'] ?? 0, 1) }}h
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ __('Avg Resolution Time') }}
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
                            {{ __('SLA Compliance') }}
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Status Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Tickets by Status') }}
                </x-slot>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($this->reportData['status_breakdown'] ?? [] as $status => $count)
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="text-2xl font-semibold">{{ $count }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ ucfirst($status) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Priority Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Tickets by Priority') }}
                </x-slot>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($this->reportData['priority_breakdown'] ?? [] as $priority => $count)
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="text-2xl font-semibold">{{ $count }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ ucfirst($priority) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Category Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Tickets by Category') }}
                </x-slot>

                <div class="space-y-2">
                    @foreach($this->reportData['category_breakdown'] ?? [] as $category)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="font-medium">{{ $category['name'] }}</span>
                            <span class="text-lg font-semibold text-primary-600">{{ $category['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
