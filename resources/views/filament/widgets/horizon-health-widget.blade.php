{{-- 
    Horizon Health Widget View
    
    Displays Laravel Horizon health status in Filament admin dashboard.
    Shows queue metrics, supervisor status, and failed job counts.
    
    Requirements: 23.1, 23.4, 23.8
    WCAG 2.2 AA Compliant: Proper contrast ratios, ARIA labels, keyboard navigation
--}}

<x-filament-widgets::widget>
    <x-filament.components::widget-card title="Laravel Horizon - Status Queue"
        description="Status kesihatan Laravel Horizon dan queue" icon="heroicon-o-queue-list" color="primary"
        :interactive="false">

        @if (isset($last_updated))
            <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                Dikemaskini: {{ $last_updated }}
            </div>
        @endif

        <div class="space-y-6">
            @if (isset($error))
                {{-- Error State --}}
                <div class="rounded-lg bg-danger-50 dark:bg-danger-900/20 p-4 border border-danger-200 dark:border-danger-800"
                    role="alert" aria-live="polite">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                        <h3 class="text-sm font-medium text-danger-800 dark:text-danger-200">
                            Ralat Memuat Status Horizon
                        </h3>
                    </div>
                    <p class="mt-2 text-sm text-danger-700 dark:text-danger-300">
                        {{ $error }}
                    </p>
                </div>
            @else
                {{-- Health Status Overview --}}
                @if (isset($health_status) && !empty($health_status))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Supervisors Status --}}
                        @php
                            $supervisors = $health_status['supervisors'] ?? [
                                'healthy' => false,
                                'total_supervisors' => 0,
                                'unhealthy_supervisors' => 0,
                            ];
                            $supervisorColor = $supervisors['healthy'] ? 'green' : 'red';
                        @endphp
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Supervisors</p>
                                    <p
                                        class="text-2xl font-bold text-{{ $supervisorColor }}-600 dark:text-{{ $supervisorColor }}-400">
                                        {{ $supervisors['total_supervisors'] - $supervisors['unhealthy_supervisors'] }}/{{ $supervisors['total_supervisors'] }}
                                    </p>
                                </div>
                                <div
                                    class="rounded-full p-2 bg-{{ $supervisorColor }}-100 dark:bg-{{ $supervisorColor }}-900/20">
                                    @if ($supervisors['healthy'])
                                        <x-heroicon-o-check-circle
                                            class="h-6 w-6 text-{{ $supervisorColor }}-600 dark:text-{{ $supervisorColor }}-400" />
                                    @else
                                        <x-heroicon-o-x-circle
                                            class="h-6 w-6 text-{{ $supervisorColor }}-600 dark:text-{{ $supervisorColor }}-400" />
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $supervisors['healthy'] ? 'Semua berfungsi' : $supervisors['unhealthy_supervisors'] . ' tidak sihat' }}
                            </p>
                        </div>

                        {{-- Queue Wait Times --}}
                        @php
                            $queues = $health_status['queues'] ?? ['healthy' => false, 'issues' => []];
                            $queueColor = $queues['healthy'] ? 'green' : 'red';
                        @endphp
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Masa Tunggu Queue
                                    </p>
                                    <p
                                        class="text-2xl font-bold text-{{ $queueColor }}-600 dark:text-{{ $queueColor }}-400">
                                        @if ($queues['healthy'])
                                            Normal
                                        @else
                                            {{ count($queues['issues']) }} Isu
                                        @endif
                                    </p>
                                </div>
                                <div
                                    class="rounded-full p-2 bg-{{ $queueColor }}-100 dark:bg-{{ $queueColor }}-900/20">
                                    @if ($queues['healthy'])
                                        <x-heroicon-o-clock
                                            class="h-6 w-6 text-{{ $queueColor }}-600 dark:text-{{ $queueColor }}-400" />
                                    @else
                                        <x-heroicon-o-exclamation-triangle
                                            class="h-6 w-6 text-{{ $queueColor }}-600 dark:text-{{ $queueColor }}-400" />
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $queues['healthy'] ? 'Dalam had normal' : 'Melebihi threshold' }}
                            </p>
                        </div>

                        {{-- Failed Jobs --}}
                        @php
                            $failedJobs = $health_status['failed_jobs'] ?? [
                                'healthy' => false,
                                'failed_count' => 0,
                                'threshold' => 10,
                            ];
                            $failedColor = $failedJobs['healthy'] ? 'green' : 'red';
                        @endphp
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Job Gagal</p>
                                    <p
                                        class="text-2xl font-bold text-{{ $failedColor }}-600 dark:text-{{ $failedColor }}-400">
                                        {{ $failedJobs['failed_count'] }}
                                    </p>
                                </div>
                                <div
                                    class="rounded-full p-2 bg-{{ $failedColor }}-100 dark:bg-{{ $failedColor }}-900/20">
                                    @if ($failedJobs['healthy'])
                                        <x-heroicon-o-check-badge
                                            class="h-6 w-6 text-{{ $failedColor }}-600 dark:text-{{ $failedColor }}-400" />
                                    @else
                                        <x-heroicon-o-exclamation-circle
                                            class="h-6 w-6 text-{{ $failedColor }}-600 dark:text-{{ $failedColor }}-400" />
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Had: {{ $failedJobs['threshold'] }}
                            </p>
                        </div>

                        {{-- Worker Processes --}}
                        @php
                            $workers = $health_status['worker_processes'] ?? [
                                'healthy' => false,
                                'active_processes' => 0,
                                'total_processes' => 0,
                                'healthy_ratio' => 0,
                            ];
                            $workerColor = $workers['healthy'] ? 'green' : 'red';
                            $ratio = round($workers['healthy_ratio'] * 100, 1);
                        @endphp
                        <div
                            class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Worker Processes</p>
                                    <p
                                        class="text-2xl font-bold text-{{ $workerColor }}-600 dark:text-{{ $workerColor }}-400">
                                        {{ $workers['active_processes'] }}/{{ $workers['total_processes'] }}
                                    </p>
                                </div>
                                <div
                                    class="rounded-full p-2 bg-{{ $workerColor }}-100 dark:bg-{{ $workerColor }}-900/20">
                                    @if ($workers['healthy'])
                                        <x-heroicon-o-cpu-chip
                                            class="h-6 w-6 text-{{ $workerColor }}-600 dark:text-{{ $workerColor }}-400" />
                                    @else
                                        <x-heroicon-o-exclamation-triangle
                                            class="h-6 w-6 text-{{ $workerColor }}-600 dark:text-{{ $workerColor }}-400" />
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $ratio }}% aktif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Queue Statistics Table --}}
                @if (isset($queue_statistics) && !empty($queue_statistics))
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Statistik Queue Terperinci
                        </h3>

                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" role="table"
                                aria-label="Statistik queue Laravel Horizon">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Queue
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Masa Tunggu (s)
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Throughput
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Pending
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Gagal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($queue_statistics as $queueName => $stats)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-900/20 text-primary-800 dark:text-primary-200">
                                                    {{ $queueName }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @php
                                                    $waitTime = $stats['wait_time'] ?? 0;
                                                    $waitColor =
                                                        $waitTime > 60
                                                            ? 'text-danger-600 dark:text-danger-400'
                                                            : 'text-success-600 dark:text-success-400';
                                                @endphp
                                                <span
                                                    class="{{ $waitColor }}">{{ number_format($waitTime, 1) }}</span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($stats['throughput'] ?? 0, 2) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ number_format($stats['pending'] ?? 0) }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @php
                                                    $failed = $stats['failed'] ?? 0;
                                                    $failedColor =
                                                        $failed > 0
                                                            ? 'text-danger-600 dark:text-danger-400'
                                                            : 'text-gray-500 dark:text-gray-400';
                                                @endphp
                                                <span class="{{ $failedColor }}">{{ number_format($failed) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ url('/horizon') }}" target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800"
                        aria-label="Buka dashboard Laravel Horizon dalam tab baharu">
                        <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4 mr-2" />
                        Buka Dashboard Horizon
                    </a>

                    <button type="button" onclick="window.location.reload()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800"
                        aria-label="Muat semula widget untuk data terkini">
                        <x-heroicon-o-arrow-path class="h-4 w-4 mr-2" />
                        Muat Semula
                    </button>
                </div>
            @endif
        </div>
    </x-filament.components::widget-card>
</x-filament-widgets::widget>
