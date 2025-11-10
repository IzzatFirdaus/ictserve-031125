{{--
/**
 * Empty State Component
 *
 * @component x-ui.empty-state
 * @description Displays empty state with icon, message, and optional action button
 * @wcag WCAG 2.2 Level AA compliant - 4.5:1 text contrast, 44×44px touch targets
 * @trace Requirements 4, 6, 7 (Tailwind CSS 4.1, Component Library, WCAG 2.2 AA)
 * @version 2.0.0
 * @updated 2025-01-06
 */
--}}

@props([
    'message' => null,
    'actionText' => null,
    'actionUrl' => null,
    'icon' => 'inbox',
    'variant' => 'default',
])

@php
    $variantClasses = match ($variant) {
        'portal' => 'bg-slate-900/50 text-slate-100 backdrop-blur-sm border border-slate-800',
        default => 'bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700',
    };
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col items-center justify-center p-8 rounded-lg $variantClasses"]) }}
    role="status"
    aria-live="polite">
    
    {{-- Icon --}}
    <div class="w-16 h-16 mb-4 text-gray-400 dark:text-gray-600" aria-hidden="true">
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
            </path>
        </svg>
    </div>

    {{-- Message --}}
    @if ($message)
        <p class="text-center text-gray-600 dark:text-gray-400 mb-4">
            {{ $message }}
        </p>
    @endif

    {{-- Action Button - WCAG compliant with 44×44px minimum touch target --}}
    @if ($actionText && $actionUrl)
        <a href="{{ $actionUrl }}"
            class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] px-4 py-2 bg-[#0056b3] hover:bg-[#003d82] text-white text-sm font-medium rounded-md transition-colors duration-150 focus:outline-none focus:ring-4 focus:ring-[#0056b3] focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            aria-label="{{ $actionText }}">
            {{ $actionText }}
        </a>
    @endif

    {{-- Slot for custom content --}}
    @if ($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
