<x-filament-panels::page>
    <div class="space-y-6" wire:poll.60s>
        <!-- Performance Alerts -->
        @if(!empty($this->performanceAlerts))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Performance Alerts ({{ count($this->performanceAlerts) }})
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($this->performanceAlerts as $alert)
                                    <li>{{ $alert['message'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- System Metrics -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Real-time System Metrics
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($this->systemMetrics as $metric => $value)
                    @php
                        $color = $this->getMetricColor($metric, $value);
                        $formattedValue = $this->formatMetricValue($metric, $value);
                        $colorClasses = match($color) {
                            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                        };
                    @endphp
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 capitalize">
                                    {{ str_replace('_', ' ', $metric) }}
                                </p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $formattedValue }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClasses }}">
                                    {{ ucfirst($color) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Performance Trends -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Performance Trends
                </h3>
                
                <div class="flex space-x-2">
                    @foreach(['1h' => '1 Hour', '24h' => '24 Hours', '7d' => '7 Days', '30d' => '30 Days'] as $period => $label)
                        <button 
                            wire:click="setPeriod('{{ $period }}')"
                            class="px-3 py-1 text-sm rounded-md {{ $this->selectedPeriod === $period ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Response Time Chart -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Response Time (ms)</h4>
                    <div class="h-32 flex items-end space-x-1">
                        @foreach(array_slice($this->performanceTrends['response_times'], -12) as $point)
                            <div class="flex-1 bg-blue-500 rounded-t" style="height: {{ ($point['value'] / 2000) * 100 }}%"></div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Cache Hit Rate Chart -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Cache Hit Rate (%)</h4>
                    <div class="h-32 flex items-end space-x-1">
                        @foreach(array_slice($this->performanceTrends['cache_rates'], -12) as $point)
                            <div class="flex-1 bg-green-500 rounded-t" style="height: {{ $point['value'] }}%"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Integration Health -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Integration Health Status
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($this->integrationHealth as $service => $health)
                    @php
                        $statusColor = match($health['status']) {
                            'healthy' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                            'unhealthy' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        };
                    @endphp
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900 dark:text-white capitalize">
                                {{ $service }}
                            </h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                {{ ucfirst($health['status']) }}
                            </span>
                        </div>
                        
                        @if(isset($health['response_time']))
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Response: {{ number_format($health['response_time'], 2) }}ms
                            </p>
                        @endif
                        
                        @if(isset($health['failed_jobs']))
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Failed Jobs: {{ $health['failed_jobs'] }}
                            </p>
                        @endif
                        
                        @if(isset($health['error']))
                            <p class="text-sm text-red-600 dark:text-red-400">
                                Error: {{ $health['error'] }}
                            </p>
                        @endif
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Last check: {{ $health['last_check'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Slow Queries -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Slow Database Queries
            </h3>
            
            @if(empty($this->slowQueries))
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">No slow queries detected</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Query
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Execution Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Rows Examined
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Timestamp
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($this->slowQueries as $query)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                        <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">
                                            {{ Str::limit($query['query'], 60) }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format($query['execution_time'], 2) }}s
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format($query['rows_examined']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $query['timestamp']->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>