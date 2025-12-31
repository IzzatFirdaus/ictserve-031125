{{--
    Widget Card Component - MyDS v2025.2 Compliant
    
    @description Base widget card component with consistent styling
    @trace Task 5.1.2; R22.2.1-R22.2.5; D14 §5.3
    @version 3.6.1
    @since 2025-01-01
--}}

@props([
    'title' => null,
    'description' => null,
    'icon' => null,
    'color' => 'primary',
    'size' => 'default',
    'interactive' => false,
    'loading' => false,
])

@php
    $cardClasses = [
        'widget-card',
        'bg-white dark:bg-gray-800',
        'border border-gray-200 dark:border-gray-700',
        'rounded-lg', // 12px border-radius
        'p-6', // 24px internal padding
        'transition-all duration-200 ease-out',
        'theme-transition',
        'shadow-card', // MyDS shadow-card elevation
    ];

    // Interactive hover states
    if ($interactive) {
        $cardClasses[] = 'hover:shadow-lg hover:-translate-y-0.5 cursor-pointer';
    }

    // Size variations
    $sizeClasses = match ($size) {
        'small' => 'p-4',
        'large' => 'p-8',
        default => 'p-6',
    };

    // Color variations for accent
    $colorClasses = match ($color) {
        'success' => 'border-l-4 border-l-success-500',
        'warning' => 'border-l-4 border-l-warning-500',
        'danger' => 'border-l-4 border-l-danger-500',
        'info' => 'border-l-4 border-l-info-500',
        default => '',
    };

    $finalClasses = implode(' ', [...$cardClasses, $sizeClasses, $colorClasses]);
@endphp

<div {{ $attributes->merge(['class' => $finalClasses]) }} role="region"
    @if ($title) aria-labelledby="widget-title-{{ Str::slug($title) }}" @endif
    @if ($description) aria-describedby="widget-desc-{{ Str::slug($title ?? 'widget') }}" @endif>
    {{-- Loading State --}}
    @if ($loading)
        <div class="animate-pulse">
            <div class="flex items-center space-x-3 mb-4">
                @if ($icon)
                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                @endif
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            </div>
            <div class="space-y-2">
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
            </div>
        </div>
    @else
        {{-- Widget Header --}}
        @if ($title || $icon)
            <div class="flex items-center space-x-3 mb-4">
                @if ($icon)
                    <div class="shrink-0">
                        <div
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20">
                            @svg($icon, "w-5 h-5 text-{$color}-600 dark:text-{$color}-400")
                        </div>
                    </div>
                @endif

                @if ($title)
                    <h3 id="widget-title-{{ Str::slug($title) }}" class="widget-header">
                        {{ $title }}
                    </h3>
                @endif
            </div>
        @endif

        {{-- Widget Description --}}
        @if ($description)
            <div id="widget-desc-{{ Str::slug($title ?? 'widget') }}" class="widget-description mb-4">
                {{ $description }}
            </div>
        @endif

        {{-- Widget Content --}}
        <div class="widget-content">
            {{ $slot }}
        </div>
    @endif
</div>
