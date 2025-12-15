{{--
/**
 * Web Vitals Pulse Card View
 *
 * Displays Core Web Vitals metrics with visual indicators
 *
 * @requirements P2 Performance & UX polish
 * @trace 13.5 Core Web Vitals optimization
 *
 * @version 1.0.0
 * @created 2025-12-07
 */
--}}
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header name="Web Vitals" title="Core Web Vitals Metrics" details="Real-time performance monitoring">
        <x-slot:icon>
            <x-pulse::icons.sparkles />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if (empty($metrics))
            <x-pulse::no-results />
        @else
            <div class="grid gap-4 mx-6 mb-6">
                @foreach ($metrics as $metric)
                    <div class="flex flex-col gap-2">
                        {{-- Metric Header --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $metric['label'] }} ({{ $metric['name'] }})
                                </span>
                                @if ($metric['rating'] === 'good')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Good
                                    </span>
                                @elseif ($metric['rating'] === 'needs-improvement')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        Needs Improvement
                                    </span>
                                @elseif ($metric['rating'] === 'poor')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Poor
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                {{ $metric['name'] === 'CLS' ? number_format($metric['value'], 3) : number_format($metric['value'], 0) }}{{ $metric['unit'] }}
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full transition-all duration-300 @if ($metric['rating'] === 'good') bg-green-500
                                @elseif ($metric['rating'] === 'needs-improvement')
                                    bg-yellow-500
                                @else
                                    bg-red-500 @endif
                                "
                                style="width: {{ min(100, $metric['percentage']) }}%">
                            </div>
                        </div>

                        {{-- Target Information --}}
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Target: {{ $metric['name'] === 'CLS' ? number_format($metric['target'], 1) : number_format($metric['target'], 0) }}{{ $metric['unit'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Performance Insights --}}
            <div class="mx-6 mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="text-xs text-blue-800 dark:text-blue-200">
                        <p class="font-medium mb-1">Core Web Vitals Targets</p>
                        <ul class="space-y-0.5">
                            <li>• LCP: &lt;2.5s (good), &lt;4s (needs improvement)</li>
                            <li>• FID: &lt;100ms (good), &lt;300ms (needs improvement)</li>
                            <li>• CLS: &lt;0.1 (good), &lt;0.25 (needs improvement)</li>
                            <li>• TTFB: &lt;600ms (good), &lt;1s (needs improvement)</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
