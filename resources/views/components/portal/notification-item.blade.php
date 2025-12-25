{{--
/**
 * Notification Item Component
 *
 * Displays a single notification with type-specific styling, icon, and action buttons.
 * WCAG 2.2 AA compliant with proper color contrast and keyboard accessibility.
 *
 * @props
 * - type: string (ticket_assigned|ticket_resolved|loan_approved|loan_rejected|asset_overdue|sla_breach)
 * - title: string (notification title)
 * - message: string (notification message)
 * - timestamp: string|Carbon (notification timestamp)
 * - read: bool (read status) - default: false
 * - actionUrl: string|null (link to related item) - optional
 * - actionLabel: string|null (action button label) - optional
 * - notificationId: int|null (for mark as read functionality) - optional
 *
 * @trace Requirements 6.3, 6.4, 6.5
 * @wcag-level AA (SC 1.4.3, 2.4.4, 2.5.5)
 */
--}}

@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'timestamp' => null,
    'read' => false,
    'actionUrl' => null,
    'actionLabel' => null,
    'notificationId' => null,
])

{{--
/**
 * MyDS Design System - Notification Item Component
 * @trace D13 §2.2-2.7, D14 §9.3
 * @requirements 6.3, 6.4, 6.5 (Notification accessibility)
 * WCAG 2.2 AA: 44px touch targets, 3px focus ring, aria-label
 */
--}}

@php
    // MyDS semantic color tokens
    $typeConfig = [
        'ticket_assigned' => [
            'icon' => 'user-plus',
            'color' => 'text-primary-400 bg-primary-500/10 border-primary-500/20',
            'iconColor' => 'text-primary-400',
        ],
        'ticket_resolved' => [
            'icon' => 'check-circle',
            'color' => 'text-success-400 bg-success-500/10 border-success-500/20',
            'iconColor' => 'text-success-400',
        ],
        'loan_approved' => [
            'icon' => 'check-badge',
            'color' => 'text-success-400 bg-success-500/10 border-success-500/20',
            'iconColor' => 'text-success-400',
        ],
        'loan_rejected' => [
            'icon' => 'x-circle',
            'color' => 'text-danger-400 bg-danger-500/10 border-danger-500/20',
            'iconColor' => 'text-danger-400',
        ],
        'asset_overdue' => [
            'icon' => 'exclamation-triangle',
            'color' => 'text-warning-400 bg-warning-500/10 border-warning-500/20',
            'iconColor' => 'text-warning-400',
        ],
        'sla_breach' => [
            'icon' => 'clock',
            'color' => 'text-danger-400 bg-danger-500/10 border-danger-500/20',
            'iconColor' => 'text-danger-400',
        ],
    ];

    $config = $typeConfig[$type] ?? [
        'icon' => 'bell',
        'color' => 'text-gray-400 bg-gray-500/10 border-gray-500/20',
        'iconColor' => 'text-gray-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'relative flex gap-4 p-4 rounded-lg bg-gray-900 dark:bg-gray-800 border shadow-card ' . ($read ? 'border-gray-700' : 'border-primary-500/30 bg-primary-500/5')]) }}
    role="article" aria-label="{{ $title }}">

    {{-- Unread Indicator --}}
    @if (!$read)
        <div class="absolute top-2 left-2 w-2 h-2 bg-primary-500 rounded-full"
            aria-label="{{ __('notifications.unread') }}">
        </div>
    @endif

    {{-- Icon with 44px touch target --}}
    <div class="shrink-0 ml-2">
        <div class="flex items-center justify-center w-10 h-10 min-h-11 min-w-11 rounded-lg {{ $config['color'] }}">
            <x-dynamic-component :component="'heroicon-o-' . $config['icon']" class="w-5 h-5 {{ $config['iconColor'] }}" aria-hidden="true" />
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        {{-- Title --}}
        <h3 class="text-sm font-semibold text-white mb-1">
            {{ $title }}
        </h3>

        {{-- Message --}}
        <p class="text-sm text-gray-300 mb-2">
            {{ $message }}
        </p>

        {{-- Timestamp --}}
        @if ($timestamp)
            <time datetime="{{ $timestamp instanceof \Carbon\Carbon ? $timestamp->toIso8601String() : $timestamp }}"
                class="text-xs text-gray-400">
                {{ $timestamp instanceof \Carbon\Carbon ? $timestamp->diffForHumans() : $timestamp }}
            </time>
        @endif

        {{-- Actions --}}
        @if ($actionUrl && $actionLabel)
            <div class="mt-3">
                <a href="{{ $actionUrl }}" wire:navigate
                    class="inline-flex items-center gap-2 text-sm font-medium text-primary-400 hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900 rounded-lg transition-colors duration-200">
                    {{ $actionLabel }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif
    </div>

    {{-- Mark as Read Button with 44px touch target --}}
    @if (!$read && $notificationId)
        <div class="shrink-0">
            <button type="button" wire:click="markAsRead({{ $notificationId }})"
                class="p-2 min-h-11 min-w-11 text-gray-400 hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900 rounded-lg transition-colors duration-200"
                aria-label="{{ __('notifications.mark_as_read') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        </div>
    @endif
</div>
