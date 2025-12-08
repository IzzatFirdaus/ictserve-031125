{{--
/**
 * Empty State Component
 *
 * Displays empty state with illustration, bilingual messages, and call-to-action.
 *
 * Features:
 * - Illustration support with aria-hidden="true" per D14 §8.2
 * - Bilingual messages (MS/EN) per D15 guidelines
 * - Call-to-action button with primary styling per D14 §6.5
 * - Proper ARIA labels per D14 §10.3
 * - 44×44px minimum touch targets per D12 §4.1
 * - Multiple icon presets for common scenarios
 *
 * @props message: string - Primary message to display
 * @props description: string - Secondary description text
 * @props actionText: string - CTA button text
 * @props actionUrl: string - CTA button URL
 * @props actionWire: string - Livewire action (alternative to URL)
 * @props icon: string - Icon preset (inbox, search, document, ticket, loan, user)
 * @props illustration: string - Custom illustration path
 * @props variant: default|portal|compact - Visual variant
 * @props size: sm|md|lg - Size variant
 *
 * @see D14 §8.2 Illustration guidelines
 * @see D14 §10.3 ARIA labels
 * @see D15 Bilingual support guidelines
 *
 * @requirements 27.1-27.5 Empty state components
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */
--}}

@props([
'message' => null,
'description' => null,
'actionText' => null,
'actionUrl' => null,
'actionWire' => null,
'icon' => 'inbox',
'illustration' => null,
'variant' => 'default',
'size' => 'md',
])

@php
$variantClasses = match ($variant) {
'portal' => 'bg-slate-900/50 text-slate-100 backdrop-blur-sm border border-slate-800',
'compact' => 'bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white',
default
=> 'bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700',
};

$sizeClasses = match ($size) {
'sm' => 'p-4',
'lg' => 'p-12',
default => 'p-8',
};

$iconSize = match ($size) {
'sm' => 'w-12 h-12',
'lg' => 'w-24 h-24',
default => 'w-16 h-16',
};

$textSize = match ($size) {
'sm' => 'text-sm',
'lg' => 'text-lg',
default => 'text-base',
};
@endphp

<div {{ $attributes->merge(['class' => "flex flex-col items-center justify-center rounded-(--radius-l) $variantClasses $sizeClasses"]) }}
    role="status" aria-live="polite">

    {{-- Illustration or Icon --}}
    @if ($illustration)
    <img src="{{ $illustration }}" alt="" aria-hidden="true" class="{{ $iconSize }} mb-4 object-contain" />
    @else
    <div class="{{ $iconSize }} mb-4 text-gray-400 dark:text-gray-600" aria-hidden="true">
        @switch($icon)
        @case('search')
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        @break

        @case('document')
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
        </svg>
        @break

        @case('ticket')
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
        </svg>
        @break

        @case('loan')
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
        </svg>
        @break

        @case('user')
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        @break

        @case('inbox')

        @default
        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        @endswitch
    </div>
    @endif

    {{-- Primary Message --}}
    @if ($message)
    <h3 class="text-center font-medium text-gray-900 dark:text-white {{ $textSize }} mb-2">
        {{ $message }}
    </h3>
    @endif

    {{-- Description --}}
    @if ($description)
    <p class="text-center text-gray-600 dark:text-gray-400 text-sm max-w-sm mb-4">
        {{ $description }}
    </p>
    @endif

    {{-- Call-to-Action Button - 44×44px minimum touch target per D12 §4.1 --}}
    @if ($actionText && ($actionUrl || $actionWire))
    @if ($actionUrl)
    <a href="{{ $actionUrl }}"
        class="inline-flex items-center justify-center min-h-11 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-m transition-colors duration-200 focus:outline-none shadow-sm"
        aria-label="{{ $actionText }}">
        {{ $actionText }}
    </a>
    @elseif ($actionWire)
    <button type="button" wire:click="{{ $actionWire }}"
        class="inline-flex items-center justify-center min-h-11 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-m transition-colors duration-200 focus:outline-none shadow-sm"
        aria-label="{{ $actionText }}">
        {{ $actionText }}
    </button>
    @endif
    @endif

    {{-- Slot for custom content --}}
    @if ($slot->isNotEmpty())
    <div class="mt-4">
        {{ $slot }}
    </div>
    @endif
</div>