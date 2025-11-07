{{--
    name: statistics-card.blade.php
    description: Reusable statistics card component for dashboard
    author: dev-team@motac.gov.my
    trace: D03 SRS-FR-006, D12 §3, D14 §9 (Requirement 1.1, WCAG 2.2 AA)
    last-updated: 2025-11-06

    @props:
    - title (string): Card title
    - value (string|int): Main statistic value
    - icon (string): Icon type (inbox, clock, check, user, alert)
    - color (string, default: 'blue'): Color theme (blue, amber, green, red, indigo)
    - trend (optional, string): Trend value (e.g., '+12%', '-5%')
    - trendDirection (optional, string): 'up' or 'down'
--}}

@props([
    'title',
    'value',
    'icon' => 'inbox',
    'color' => 'blue',
    'trend' => null,
    'trendDirection' => null,
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-500 text-white',
        'amber' => 'bg-amber-500 text-white',
        'green' => 'bg-green-500 text-white',
        'red' => 'bg-red-600 text-white',
        'indigo' => 'bg-indigo-500 text-white',
    ];

    $iconSvgs = [
        'inbox' => '<path d="M2 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V4zM8 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1H9a1 1 0 01-1-1V4zM15 3a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1h-2z" />',
        'clock' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />',
        'check' => '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />',
        'user' => '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />',
        'alert' => '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow p-6']) }}>
    <div class="flex items-center">
        <div class="flex-shrink-0 {{ $colorClasses[$color] ?? $colorClasses['blue'] }} rounded-md p-3">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                {!! $iconSvgs[$icon] ?? $iconSvgs['inbox'] !!}
            </svg>
        </div>
        <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</p>
            <div class="flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $value }}
                </p>
                @if($trend)
                    <span class="ml-2 text-sm font-medium {{ $trendDirection === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        @if($trendDirection === 'up')
                            ↑
                        @elseif($trendDirection === 'down')
                            ↓
                        @endif
                        {{ $trend }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
