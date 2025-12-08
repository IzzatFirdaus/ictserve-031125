{{--
/**
 * UI Card Component - MyDS Design System
 *
 * @component ui.card
 * @description WCAG 2.2 AA compliant card with MyDS shadow-card token
 * @author Pasukan BPM MOTAC
 * @trace D13 §2.2-2.7 (MyDS Design Tokens)
 * @trace D14 §7.5 (Shadow System)
 * @version 2.0.0
 * @updated 2025-12-06
 */
--}}
@props([
'padding' => 'p-6',
'header' => null,
'footer' => null,
])

<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-card rounded-(--radius-l) border border-gray-200 dark:border-gray-700']) }}>
    @if ($header)
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        {{ $header }}
    </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @if ($footer)
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
        {{ $footer }}
    </div>
    @endif
</div>