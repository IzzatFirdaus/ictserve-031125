{{-- Chart Empty State View --}}
{{-- Displays when chart has no data to show --}}
{{-- @see Requirements 22.2, 22.3 --}}

<div
    class="flex flex-col items-center justify-center p-8 text-center min-h-[300px] bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
    {{-- Icon --}}
    <div class="mb-4">
        <x-heroicon-o-chart-bar class="w-16 h-16 text-gray-400 dark:text-gray-500" />
    </div>

    {{-- Message --}}
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
        {{ $message ?? 'Tiada data tersedia' }}
    </h3>

    {{-- Description --}}
    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
        {{ $description ?? 'Data carta tidak tersedia pada masa ini. Sila cuba semula kemudian atau semak tetapan penapis.' }}
    </p>

    {{-- Optional Action Button --}}
    @if (isset($actionUrl) && isset($actionLabel))
        <a href="{{ $actionUrl }}"
            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
            {{ $actionLabel }}
        </a>
    @endif
</div>
