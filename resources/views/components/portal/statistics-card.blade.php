{{--
/**
 * Statistics Card Component
 *
 * Displays dashboard statistics with icon, count, label, and optional trend indicator.
 * WCAG 2.2 AA compliant with proper color contrast and ARIA attributes.
 *
 * @props
 * - icon: string (heroicon name)
 * - count: int|string (statistic value)
 * - label: string (translated label)
 * - color: string (primary|success|warning|danger) - default: primary
 * - trend: string|null (up|down|neutral) - optional
 * - trendValue: string|null (e.g., "+12%") - optional
 * - href: string|null (optional link) - default: null
 *
 * @trace Requirements 1.1, 14.1
 * @wcag-level AA (SC 1.4.3 Color Contrast, 1.4.11 Non-text Contrast)
 */
--}}

@props([
    'icon' => 'chart-bar',
    'count' => 0,
    'label' => '',
    'color' => 'primary',
    'trend' => null,
    'trendValue' => null,
    'href' => null,
])

@php
    $colorClasses = [
        'primary' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
        'success' => 'text-green-400 bg-green-500/10 border-green-500/20',
        'warning' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
        'danger' => 'text-red-400 bg-red-500/10 border-red-500/20',
    ];

    $trendClasses = [
        'up' => 'text-green-400',
        'down' => 'text-red-400',
        'neutral' => 'text-slate-400',
    ];

    $cardClasses = $colorClasses[$color] ?? $colorClasses['primary'];
    $trendClass = $trend ? ($trendClasses[$trend] ?? $trendClasses['neutral']) : '';

    $containerTag = $href ? 'a' : 'div';
@endphp

<{{ $containerTag }}
    @if($href)
        href="{{ $href }}"
        wire:navigate
        class="group"
    @endif
    {{ $attributes->merge(['class' => 'block']) }}>

    <div class="bg-slate-900 border {{ $cardClasses }} rounded-lg p-6 transition-all duration-200 {{ $href ? 'hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950' : '' }}"
         role="article"
         aria-label="{{ $label }}: {{ $count }}">

        <div class="flex items-start justify-between">
            <div class="flex-1">
                {{-- Icon --}}
                <div class="flex items-center justify-center w-12 h-12 rounded-lg {{ $cardClasses }} mb-4">
                    <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6" aria-hidden="true" />
                </div>

                {{-- Count --}}
                <div class="text-3xl font-bold text-white mb-1" aria-live="polite">
                    {{ $count }}
                </div>

                {{-- Label --}}
                <div class="text-sm text-slate-400">
                    {{ $label }}
                </div>
            </div>

            {{-- Trend Indicator (Optional) --}}
            @if($trend && $trendValue)
                <div class="flex items-center gap-1 {{ $trendClass }} text-sm font-medium"
                     aria-label="{{ __('common.trend') }}: {{ $trendValue }}">
                    @if($trend === 'up')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    @elseif($trend === 'down')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    @endif
                    <span>{{ $trendValue }}</span>
                </div>
            @endif
        </div>

        {{-- Link Indicator --}}
        @if($href)
            <div class="mt-4 flex items-center text-sm {{ str_replace('bg-', 'text-', $cardClasses) }} opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <span>{{ __('common.view_details') }}</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        @endif
    </div>
</{{ $containerTag }}>
