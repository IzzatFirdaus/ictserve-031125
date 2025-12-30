{{--
    ICTServe Stats Card Component - MyDS Design System v2025.2
    
    Displays metric numbers with proper styling:
    - text-3xl (32px) for metric values
    - Color coding for success/warning/danger
    - WCAG 2.2 AA compliant contrast
    - Proper ARIA labels for screen readers
    
    @props
    - value: The metric value to display
    - label: Description label
    - icon: Heroicon name (optional)
    - color: Card accent color (primary, success, warning, danger)
    - trend: Trend direction (up, down, neutral)
    - trendValue: Trend percentage or value
    - url: Link URL (optional)
    
    @trace D13 §2.4 (Typography), D14 §4.1 (Color System)
    @version 3.6.1
--}}

@props([
    'value' => 0,
    'label' => '',
    'icon' => null,
    'color' => 'primary',
    'trend' => null,
    'trendValue' => null,
    'url' => null,
])

@php
    $colorClasses = match ($color) {
        'success' => 'text-success-600 dark:text-success-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'info' => 'text-info-600 dark:text-info-400',
        'gray' => 'text-gray-600 dark:text-gray-400',
        default => 'text-primary-600 dark:text-primary-400',
    };

    $iconBgClasses = match ($color) {
        'success' => 'bg-success-50 dark:bg-success-900/20',
        'warning' => 'bg-warning-50 dark:bg-warning-900/20',
        'danger' => 'bg-danger-50 dark:bg-danger-900/20',
        'info' => 'bg-info-50 dark:bg-info-900/20',
        'gray' => 'bg-gray-50 dark:bg-gray-900/20',
        default => 'bg-primary-50 dark:bg-primary-900/20',
    };

    $trendClasses = match ($trend) {
        'up' => 'text-success-600 dark:text-success-400',
        'down' => 'text-danger-600 dark:text-danger-400',
        default => 'text-gray-500 dark:text-gray-400',
    };

    $trendIcon = match ($trend) {
        'up' => 'arrow-trending-up',
        'down' => 'arrow-trending-down',
        default => 'minus',
    };
@endphp

@php
    $tag = $url ? 'a' : 'div';
    $linkAttrs = $url ? ['href' => $url] : [];
@endphp

<{{ $tag }}
    {{ $attributes->merge([
        'class' =>
            'bg-white dark:bg-gray-800 rounded-lg p-6 theme-transition group' .
            ($url
                ? ' hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2'
                : ''),
        'style' => 'box-shadow: var(--shadow-card);',
        'role' => 'article',
    ]) }}
    @foreach ($linkAttrs as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
    aria-label="{{ $label }}: {{ $value }}">

    <div class="flex items-start justify-between">
        {{-- Icon --}}
        @if ($icon)
            <div class="p-3 rounded-lg {{ $iconBgClasses }} transition-colors duration-200 group-hover:scale-105">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 {{ $colorClasses }}" aria-hidden="true" />
            </div>
        @endif

        {{-- Trend Indicator --}}
        @if ($trend && $trendValue)
            <div class="flex items-center gap-1 {{ $trendClasses }}">
                <x-dynamic-component :component="'heroicon-m-' . $trendIcon" class="w-4 h-4" aria-hidden="true" />
                <span class="text-sm font-medium">{{ $trendValue }}</span>
            </div>
        @endif
    </div>

    {{-- Value --}}
    <div class="mt-4">
        <p class="text-3xl font-bold {{ $colorClasses }} font-heading"
            aria-label="{{ __('Nilai') }}: {{ $value }}">
            {{ $value }}
        </p>

        {{-- Label --}}
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 font-body">
            {{ $label }}
        </p>
    </div>

    {{-- Link Indicator --}}
    @if ($url)
        <div
            class="mt-4 flex items-center text-sm {{ $colorClasses }} opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <span>{{ __('Lihat butiran') }}</span>
            <x-heroicon-m-arrow-right class="w-4 h-4 ml-1" aria-hidden="true" />
        </div>
    @endif
    </{{ $tag }}>
