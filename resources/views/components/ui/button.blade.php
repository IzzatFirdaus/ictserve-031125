@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] min-w-[44px]';

$variants = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white focus:ring-blue-300 dark:focus:ring-blue-800/50',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white focus:ring-gray-300 dark:focus:ring-gray-600',
    'success' => 'bg-green-600 hover:bg-green-700 active:bg-green-800 text-white focus:ring-green-300 dark:focus:ring-green-800/50',
    'warning' => 'bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white focus:ring-amber-300 dark:focus:ring-amber-800/50',
    'danger' => 'bg-red-600 hover:bg-red-700 active:bg-red-800 text-white focus:ring-red-300 dark:focus:ring-red-800/50',
    'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-300 dark:focus:ring-blue-800/50',
    'ghost' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-gray-300 dark:focus:ring-gray-600'
];

$sizes = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg'
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled || $loading) disabled @endif
    @if($loading) aria-busy="true" @endif
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="sr-only">{{ __('common.loading') }}</span>
    @elseif($icon && $iconPosition === 'left')
        <x-dynamic-component :component="$icon" class="h-4 w-4 mr-2" />
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <x-dynamic-component :component="$icon" class="h-4 w-4 ml-2" />
    @endif
</button>