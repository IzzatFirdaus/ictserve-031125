{{--
/**
 * Skeleton Table Component
 *
 * Placeholder component for tables to prevent CLS during loading.
 * Reserves exact space that the loaded table will occupy.
 *
 * @component performance.skeleton-table
 * @trace Requirements: 10.3 (CLS <0.1)
 * @see D12 §9 Performance optimization patterns
 */
--}}
@props([
    'rows' => 5,
    'columns' => 4,
    'showHeader' => true,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "skeleton-table bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden {$class}"]) }}
    aria-hidden="true" role="presentation">
    {{-- Table header --}}
    @if ($showHeader)
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-4">
                @for ($i = 0; $i < $columns; $i++)
                    <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded skeleton-pulse"></div>
                @endfor
            </div>
        </div>
    @endif

    {{-- Table rows --}}
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @for ($row = 0; $row < $rows; $row++)
            <div class="px-6 py-4">
                <div class="flex gap-4 items-center">
                    @for ($col = 0; $col < $columns; $col++)
                        <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded skeleton-pulse"
                            style="animation-delay: {{ $row * 100 + $col * 50 }}ms;"></div>
                    @endfor
                </div>
            </div>
        @endfor
    </div>
</div>
