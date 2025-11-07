{{--
/**
 * Contextual Help Icon Component
 *
 * Displays question mark icon with tooltip explanation and optional "Learn More" link.
 * WCAG 2.2 AA compliant with keyboard navigation and ARIA support.
 *
 * @package Resources\Views\Components\Portal
 * @version 1.0.0
 * @since 2025-11-06
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.2: Contextual help icons with tooltips
 * - WCAG 2.2 AA: Keyboard accessible, ARIA labels, focus indicators
 * - D12 §4: Unified component library integration
 *
 * Traceability:
 * - D03 SRS-FR-012.2: Contextual help system
 * - D04 §8.1: Help and onboarding design
 * - D12 §4.3: Interactive tooltip patterns
 *
 * @props
 * - tooltip: string (required) - Tooltip text (max 100 characters)
 * - learnMoreUrl: string (optional) - URL for "Learn More" link
 * - position: string (optional) - Tooltip position: top|right|bottom|left (default: top)
 * - size: string (optional) - Icon size: sm|md|lg (default: md)
 */
--}}

@props([
    'tooltip' => '',
    'learnMoreUrl' => null,
    'position' => 'top',
    'size' => 'md',
])

@php
    $sizeClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
    ];

    $iconSize = $sizeClasses[$size] ?? $sizeClasses['md'];

    $positionClasses = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
    ];

    $tooltipPosition = $positionClasses[$position] ?? $positionClasses['top'];
@endphp

<div x-data="{ showTooltip: false }" class="relative inline-flex items-center" @mouseenter="showTooltip = true"
    @mouseleave="showTooltip = false" @focusin="showTooltip = true" @focusout="showTooltip = false">
    {{-- Help Icon Button --}}
    <button type="button"
        class="inline-flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
        aria-label="{{ __('portal.contextual_help.help_icon_label') }}"
        aria-describedby="help-tooltip-{{ Str::random(8) }}" @click="showTooltip = !showTooltip">
        <x-heroicon-o-question-mark-circle class="{{ $iconSize }}" />
    </button>

    {{-- Tooltip --}}
    <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="absolute z-50 w-64 {{ $tooltipPosition }}" role="tooltip"
        id="help-tooltip-{{ Str::random(8) }}" @click.away="showTooltip = false">
        <div class="rounded-lg bg-gray-900 px-4 py-3 text-sm text-white shadow-lg">
            {{-- Tooltip Arrow --}}
            <div
                class="absolute {{ $position === 'top' ? 'top-full left-1/2 -translate-x-1/2 -mt-1' : '' }} {{ $position === 'right' ? 'right-full top-1/2 -translate-y-1/2 -mr-1' : '' }} {{ $position === 'bottom' ? 'bottom-full left-1/2 -translate-x-1/2 -mb-1' : '' }} {{ $position === 'left' ? 'left-full top-1/2 -translate-y-1/2 -ml-1' : '' }}">
                <div class="h-2 w-2 rotate-45 bg-gray-900"></div>
            </div>

            {{-- Tooltip Content --}}
            <p class="mb-0">{{ Str::limit($tooltip, 100) }}</p>

            {{-- Learn More Link --}}
            @if ($learnMoreUrl)
                <a href="{{ $learnMoreUrl }}"
                    class="mt-2 inline-flex items-center text-xs text-primary-300 hover:text-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 focus:ring-offset-gray-900 rounded"
                    target="_blank" rel="noopener noreferrer">
                    {{ __('portal.contextual_help.learn_more') }}
                    <x-heroicon-o-arrow-top-right-on-square class="ml-1 h-3 w-3" />
                </a>
            @endif
        </div>
    </div>
</div>
