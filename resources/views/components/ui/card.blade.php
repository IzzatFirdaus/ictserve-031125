{{--
/**
 * UI Card Component - MyDS Design System
 *
 * @component ui.card
 * @description WCAG 2.2 AA compliant card with MyDS shadow-card token
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D14 §7.5 (Shadow System)
 * @version 2.1.0
 * @updated 2025-12-30
 */
--}}
@props([
    'header' => null,
    'footer' => null,
    'title' => null,
    'subtitle' => null,
    'noPadding' => false,
    'variant' => 'default',
])

@php
    $baseClasses = match ($variant) {
        'portal' => 'bg-slate-900/70 backdrop-blur-sm rounded-lg shadow-card border border-slate-800 overflow-hidden',
        default
            => 'bg-white dark:bg-gray-800 rounded-lg shadow-card border border-gray-200 dark:border-gray-700 overflow-hidden',
    };

    $headerBorderClasses = match ($variant) {
        'portal' => 'border-b border-slate-800',
        default => 'border-b border-gray-200 dark:border-gray-700',
    };

    $titleClasses = match ($variant) {
        'portal' => 'text-lg font-semibold text-slate-100',
        default => 'text-lg font-semibold text-gray-900 dark:text-gray-100',
    };

    $subtitleClasses = match ($variant) {
        'portal' => 'mt-1 text-sm text-slate-400',
        default => 'mt-1 text-sm text-gray-500 dark:text-gray-400',
    };

    $footerClasses = match ($variant) {
        'portal' => 'bg-slate-800/50 px-6 py-4 border-t border-slate-800',
        default => 'bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700',
    };
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>

    @if (isset($header) || $title)
        <div class="px-6 py-4 {{ $headerBorderClasses }}">
            @if (isset($header))
                {{ $header }}
            @else
                <h3 class="{{ $titleClasses }}">
                    {{ $title }}
                </h3>
                @if ($subtitle)
                    <p class="{{ $subtitleClasses }}">
                        {{ $subtitle }}
                    </p>
                @endif
            @endif
        </div>
    @endif

    <div class="px-6 py-6 {{ $noPadding ? 'p-0' : '' }}">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="{{ $footerClasses }}">
            {{ $footer }}
        </div>
    @endif
</div>
