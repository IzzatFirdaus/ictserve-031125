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
    {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-card border border-gray-200 dark:border-gray-700 overflow-hidden']) }}>

    @if (isset($header) || $title)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            @if (isset($header))
                {{ $header }}
            @else
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>
                @if ($subtitle)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
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
        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $footer }}
        </div>
    @endif
</div>
