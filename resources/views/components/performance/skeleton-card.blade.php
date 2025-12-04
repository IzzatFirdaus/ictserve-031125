{{--
/**
 * Skeleton Card Component
 *
 * Placeholder component for cards to prevent CLS during loading.
 * Reserves exact space that the loaded content will occupy.
 *
 * @component performance.skeleton-card
 * @trace Requirements: 10.3 (CLS <0.1)
 * @see D12 §9 Performance optimization patterns
 */
--}}
@props([
    'height' => '140',
    'lines' => 3,
    'showAvatar' => false,
    'showChart' => false,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "skeleton-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 {$class}"]) }}
    style="min-height: {{ $height }}px;" aria-hidden="true" role="presentation">
    {{-- Header with optional avatar --}}
    <div class="flex items-center gap-4 mb-4">
        @if ($showAvatar)
            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 skeleton-pulse"></div>
        @endif
        <div class="flex-1">
            <div class="4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 skeleton-pulse"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mt-2 skeleton-pulse"></div>
        </div>
    </div>

    {{-- Content lines --}}
    <div class="space-y-3">
        @for ($i = 0; $i < $lines; $i++)
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded skeleton-pulse" style="width: {{ 100 - $i * 15 }}%;">
            </div>
        @endfor
    </div>

    {{-- Optional chart placeholder --}}
    @if ($showChart)
        <div class="mt-4 h-24 bg-gray-200 dark:bg-gray-700 rounded skeleton-pulse"></div>
    @endif
</div>
