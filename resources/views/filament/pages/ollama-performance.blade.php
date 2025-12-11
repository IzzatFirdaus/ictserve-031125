<x-filament-panels::page>
    @php
        $performanceStats = $this->getPerformanceStats();
        $health = $this->getSystemHealth();
    @endphp

    <div class="space-y-6">
        {{-- System Health Overview --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('ollama.performance.system_health') }}
            </x-slot>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                {{-- Ollama Server Status --}}
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        @if ($health['ollama_status'] === 'healthy')
                            <x-heroicon-o-check-circle class="h-5 w-5 text-success-500" />
                        @elseif($health['ollama_status'] === 'degraded')
                            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-500" />
                        @else
                            <x-heroicon-o-x-circle class="h-5 w-5 text-danger-500" />
                        @endif
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ollama</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('ollama.performance.status_' . $health['ollama_status']) }}
                    </p>
                </div>

                {{-- Database Status --}}
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        @if ($health['database_status'] === 'healthy')
                            <x-heroicon-o-check-circle class="h-5 w-5 text-success-500" />
                        @else
                            <x-heroicon-o-x-circle class="h-5 w-5 text-danger-500" />
                        @endif
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pangkalan Data</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('ollama.performance.status_' . $health['database_status']) }}
                    </p>
                </div>

                {{-- Cache Status --}}
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        @if ($health['cache_status'] === 'healthy')
                            <x-heroicon-o-check-circle class="h-5 w-5 text-success-500" />
                        @else
                            <x-heroicon-o-x-circle class="h-5 w-5 text-danger-500" />
                        @endif
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cache</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('ollama.performance.status_' . $health['cache_status']) }}
                    </p>
                </div>

                {{-- Queue Status --}}
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        @if ($health['queue_status'] === 'healthy')
                            <x-heroicon-o-check-circle class="h-5 w-5 text-success-500" />
                        @else
                            <x-heroicon-o-x-circle class="h-5 w-5 text-danger-500" />
                        @endif
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Queue</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('ollama.performance.status_' . $health['queue_status']) }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Response Time Metrics --}}
        <x-filament::section>
            <x-slot name="heading">
                {{ __('ollama.performance.response_time') }}
            </x-slot>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- P50 --}}
                <div class="rounded-lg bg-primary-50 p-4 dark:bg-primary-900/20">
                    <dt class="text-sm font-medium text-primary-600 dark:text-primary-400">
                        {{ __('ollama.performance.response_time_p50') }}
                    </dt>
                    <dd class="mt-2 text-3xl font-bold text-primary-900 dark:text-primary-100">
                        {{ number_format($performanceStats['response_time_p50']) }}ms
                    </dd>
                    <p class="mt-1 text-xs text-primary-600 dark:text-primary-400">
                        Median (50th percentile)
                    </p>
                </div>

                {{-- P95 --}}
                <div class="rounded-lg bg-warning-50 p-4 dark:bg-warning-900/20">
                    <dt class="text-sm font-medium text-warning-600 dark:text-warning-400">
                        {{ __('ollama.performance.response_time_p95') }}
                    </dt>
                    <dd class="mt-2 text-3xl font-bold text-warning-900 dark:text-warning-100">
                        {{ number_format($performanceStats['response_time_p95']) }}ms
                    </dd>
                    <p class="mt-1 text-xs text-warning-600 dark:text-warning-400">
                        95th percentile
                    </p>
                </div>

                {{-- P99 --}}
                <div class="rounded-lg bg-danger-50 p-4 dark:bg-danger-900/20">
                    <dt class="text-sm font-medium text-danger-600 dark:text-danger-400">
                        {{ __('ollama.performance.response_time_p99') }}
                    </dt>
                    <dd class="mt-2 text-3xl font-bold text-danger-900 dark:text-danger-100">
                        {{ number_format($performanceStats['response_time_p99']) }}ms
                    </dd>
                    <p class="mt-1 text-xs text-danger-600 dark:text-danger-400">
                        99th percentile
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Operations Statistics --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Operations by Type --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('ollama.performance.operations_by_type') }}
                </x-slot>

                <div class="space-y-3">
                    @forelse($performanceStats['operations_by_type'] as $type => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @switch($type)
                                    @case('faq_query')
                                        <x-filament::badge color="primary">
                                            {{ __('ollama.message_log.operation_faq_query') }}
                                        </x-filament::badge>
                                    @break

                                    @case('document_analysis')
                                        <x-filament::badge color="info">
                                            {{ __('ollama.message_log.operation_document_analysis') }}
                                        </x-filament::badge>
                                    @break

                                    @case('auto_reply_generation')
                                        <x-filament::badge color="success">
                                            {{ __('ollama.message_log.operation_auto_reply_generation') }}
                                        </x-filament::badge>
                                    @break

                                    @default
                                        <x-filament::badge color="gray">
                                            {{ $type }}
                                        </x-filament::badge>
                                @endswitch
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ number_format($count) }}
                            </span>
                        </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tiada data operasi
                            </p>
                        @endforelse
                    </div>
                </x-filament::section>

                {{-- Cache Performance --}}
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('ollama.performance.cache_performance') }}
                    </x-slot>

                    <div class="space-y-4">
                        {{-- Cache Hit Rate --}}
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('ollama.performance.cache_hit_rate') }}
                                </span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $performanceStats['cache_hit_rate'] }}%
                                </span>
                            </div>
                            <div class="mt-2 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-2 rounded-full {{ $performanceStats['cache_hit_rate'] > 80 ? 'bg-success-500' : ($performanceStats['cache_hit_rate'] > 50 ? 'bg-warning-500' : 'bg-danger-500') }}"
                                    style="width: {{ min($performanceStats['cache_hit_rate'], 100) }}%"></div>
                            </div>
                        </div>

                        {{-- Cache Hits/Misses --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-lg bg-success-50 p-3 dark:bg-success-900/20">
                                <dt class="text-xs font-medium text-success-600 dark:text-success-400">
                                    Cache Hits
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-success-900 dark:text-success-100">
                                    {{ number_format($performanceStats['cache_hits']) }}
                                </dd>
                            </div>
                            <div class="rounded-lg bg-danger-50 p-3 dark:bg-danger-900/20">
                                <dt class="text-xs font-medium text-danger-600 dark:text-danger-400">
                                    Cache Misses
                                </dt>
                                <dd class="mt-1 text-lg font-semibold text-danger-900 dark:text-danger-100">
                                    {{ number_format($performanceStats['cache_misses']) }}
                                </dd>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Operations Summary --}}
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('ollama.performance.total_operations') }}
                </x-slot>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Hari Ini
                        </dt>
                        <dd class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($performanceStats['total_operations_today']) }}
                        </dd>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Minggu Ini
                        </dt>
                        <dd class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($performanceStats['total_operations_week']) }}
                        </dd>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </x-filament-panels::page>
