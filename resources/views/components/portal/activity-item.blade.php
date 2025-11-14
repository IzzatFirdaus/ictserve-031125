{{--
/**
 * Activity Item Component
 *
 * Displays a single activity entry in timeline format with icon, timestamp, and description.
 * WCAG 2.2 AA compliant with semantic HTML and proper ARIA attributes.
 *
 * @props
 * - type: string (ticket_submitted|status_changed|loan_applied|loan_approved|loan_rejected|asset_returned)
 * - timestamp: string|Carbon (activity timestamp)
 * - description: string (activity description)
 * - metadata: array|null (additional context) - optional
 * - href: string|null (link to related item) - optional
 *
 * @trace Requirements 1.2
 * @wcag-level AA (SC 1.4.3, 2.4.4)
 */
--}}

@props([
    'type' => 'info',
    'timestamp' => null,
    'description' => '',
    'metadata' => null,
    'href' => null,
])

@php
    $activityConfig = [
        'ticket_submitted' => [
            'icon' => 'ticket',
            'color' => 'text-blue-400 bg-blue-500/10',
            'label' => __('activity.ticket_submitted'),
        ],
        'status_changed' => [
            'icon' => 'arrow-path',
            'color' => 'text-purple-400 bg-purple-500/10',
            'label' => __('activity.status_changed'),
        ],
        'loan_applied' => [
            'icon' => 'cube',
            'color' => 'text-amber-400 bg-amber-500/10',
            'label' => __('activity.loan_applied'),
        ],
        'loan_approved' => [
            'icon' => 'check-circle',
            'color' => 'text-green-400 bg-green-500/10',
            'label' => __('activity.loan_approved'),
        ],
        'loan_rejected' => [
            'icon' => 'x-circle',
            'color' => 'text-red-400 bg-red-500/10',
            'label' => __('activity.loan_rejected'),
        ],
        'asset_returned' => [
            'icon' => 'arrow-uturn-left',
            'color' => 'text-teal-400 bg-teal-500/10',
            'label' => __('activity.asset_returned'),
        ],
    ];

    $config = $activityConfig[$type] ?? $activityConfig['status_changed'];
    $containerTag = $href ? 'a' : 'div';
@endphp

<{{ $containerTag }}
    @if($href)
        href="{{ $href }}"
        wire:navigate
        class="group"
    @endif
    {{ $attributes->merge(['class' => 'flex gap-4 p-4 rounded-lg bg-slate-900 border border-slate-800 transition-all duration-200' . ($href ? ' hover:bg-slate-800 hover:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-950' : '')]) }}>

    {{-- Icon --}}
    <div class="flex-shrink-0">
        <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ $config['color'] }}">
            <x-dynamic-component
                :component="'heroicon-o-' . $config['icon']"
                class="w-5 h-5"
                aria-hidden="true" />
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        {{-- Description --}}
        <p class="text-sm font-medium text-white mb-1">
            {{ $description }}
        </p>

        {{-- Metadata (Optional) --}}
        @if($metadata && is_array($metadata))
            <div class="flex flex-wrap gap-2 text-xs text-slate-400 mb-2">
                @foreach($metadata as $key => $value)
                    @if($value)
                        <span class="inline-flex items-center gap-1">
                            <span class="font-medium">{{ $key }}:</span>
                            <span>{{ $value }}</span>
                        </span>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Timestamp --}}
        @if($timestamp)
            <time datetime="{{ $timestamp instanceof \Carbon\Carbon ? $timestamp->toIso8601String() : $timestamp }}"
                  class="text-xs text-slate-400">
                {{ $timestamp instanceof \Carbon\Carbon ? $timestamp->diffForHumans() : $timestamp }}
            </time>
        @endif
    </div>

    {{-- Link Indicator --}}
    @if($href)
        <div class="flex-shrink-0 flex items-center">
            <svg class="w-5 h-5 text-slate-600 group-hover:text-slate-400 transition-colors duration-200"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    @endif
</{{ $containerTag }}>
