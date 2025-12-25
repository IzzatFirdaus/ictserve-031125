{{--
/**
 * Alert Component
 * Description: Reusable alert/notification component with variants
 *
 * @author Pasukan BPM MOTAC
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (UI Components)
 * @trace D14 §8 (Style Guide)
 *
 * @wcag WCAG 2.2 Level AA
 * @version 1.0.0
 * @created 2025-01-15
 */
--}}

@props([
'variant' => 'info',
'icon' => null,
'dismissible' => false,
])

@php
$variantClasses = match($variant) {
'success' => 'bg-success-50 border-success-200 text-success-800',
'danger', 'error' => 'bg-danger-50 border-danger-200 text-danger-800',
'warning' => 'bg-warning-50 border-warning-200 text-warning-800',
'info' => 'bg-primary-50 border-primary-200 text-primary-800',
default => 'bg-gray-50 border-gray-200 text-gray-800',
};

$iconClasses = match($variant) {
'success' => 'text-success-500',
'danger', 'error' => 'text-danger-500',
'warning' => 'text-warning-500',
'info' => 'text-primary-500',
default => 'text-gray-500',
};
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-lg border {$variantClasses}"]) }} role="alert">
    @if($icon)
    <div class="shrink-0">
        <x-dynamic-component :component="$icon" class="w-5 h-5 {{ $iconClasses }}" />
    </div>
    @endif

    <div class="flex-1 text-sm">
        {{ $slot }}
    </div>

    @if($dismissible)
    <button
        type="button"
        class="shrink-0 min-h-11 min-w-11 flex items-center justify-center text-gray-400 hover:text-gray-600 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-3 focus-visible:ring-primary-500 rounded-lg"
        aria-label="Close alert"
        onclick="this.parentElement.remove()">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>
    @endif
</div>
