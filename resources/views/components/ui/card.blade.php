@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'shadow' => 'shadow-sm',
    'border' => true,
    'rounded' => 'rounded-lg'
])

@php
$classes = 'bg-white dark:bg-gray-800 ' . $shadow . ' ' . $rounded . ' ' . $padding;
if ($border) {
    $classes .= ' border border-gray-200 dark:border-gray-700';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>