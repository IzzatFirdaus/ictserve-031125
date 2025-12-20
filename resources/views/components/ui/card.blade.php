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
'header' => null,
'footer' => null,
'title' => null,
'subtitle' => null,
'noPadding' => false,
])

<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-lg shadow-card border border-slate-200 dark:border-slate-700 overflow-hidden']) }}>

    @if (isset($header) || $title)
    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
        @if (isset($header))
        {{ $header }}
        @else
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
            {{ $title }}
        </h3>
        @if ($subtitle)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
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
    <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-t border-slate-200 dark:border-slate-700">
        {{ $footer }}
    </div>
    @endif
</div>
