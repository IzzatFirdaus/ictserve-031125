{{--
    Accessible Notification Component for Ollama AI
    WCAG 2.2 AA Compliant (Req 5.7)

    Usage:
    <x-ollama.notification type="success" message="Operasi berjaya" />
    <x-ollama.notification type="error" message="Ralat berlaku" dismissible />
--}}
@props([
    'type' => 'info', // success, error, warning, info
    'message' => '',
    'title' => null,
    'dismissible' => false,
    'autoDismiss' => false,
    'autoDismissDelay' => 5000,
])

@php
    $config = match ($type) {
        'success' => [
            'role' => 'status',
            'aria_live' => 'polite',
            'bg' => 'bg-success-50 dark:bg-success-900/20',
            'border' => 'border-success-200 dark:border-success-800',
            'text' => 'text-success-800 dark:text-success-200',
            'icon_color' => 'text-success-600 dark:text-success-400',
            'icon' => 'heroicon-o-check-circle',
        ],
        'error' => [
            'role' => 'alert',
            'aria_live' => 'assertive',
            'bg' => 'bg-danger-50 dark:bg-danger-900/20',
            'border' => 'border-danger-200 dark:border-danger-800',
            'text' => 'text-danger-800 dark:text-danger-200',
            'icon_color' => 'text-danger-600 dark:text-danger-400',
            'icon' => 'heroicon-o-exclamation-circle',
        ],
        'warning' => [
            'role' => 'alert',
            'aria_live' => 'polite',
            'bg' => 'bg-warning-50 dark:bg-warning-900/20',
            'border' => 'border-warning-200 dark:border-warning-800',
            'text' => 'text-warning-800 dark:text-warning-200',
            'icon_color' => 'text-warning-600 dark:text-warning-400',
            'icon' => 'heroicon-o-exclamation-triangle',
        ],
        default => [
            'role' => 'status',
            'aria_live' => 'polite',
            'bg' => 'bg-primary-50 dark:bg-primary-900/20',
            'border' => 'border-primary-200 dark:border-primary-800',
            'text' => 'text-primary-800 dark:text-primary-200',
            'icon_color' => 'text-primary-600 dark:text-primary-400',
            'icon' => 'heroicon-o-information-circle',
        ],
    };
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-lg border {$config['bg']} {$config['border']}"]) }}
    role="{{ $config['role'] }}" aria-live="{{ $config['aria_live'] }}" aria-atomic="true"
    @if ($autoDismiss) x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, {{ $autoDismissDelay }})"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @endif>
    <div class="flex items-start gap-3">
        {{-- Icon --}}
        <x-dynamic-component :component="$config['icon']" class="w-5 h-5 shrink-0 mt-0.5 {{ $config['icon_color'] }}"
            aria-hidden="true" />

        {{-- Content --}}
        <div class="flex-1">
            @if ($title)
                <h4 class="font-medium {{ $config['text'] }}">
                    {{ $title }}
                </h4>
            @endif
            <p class="text-sm {{ $config['text'] }}">
                {{ $message }}
            </p>
            {{ $slot }}
        </div>

        {{-- Dismiss Button --}}
        @if ($dismissible)
            <button type="button"
                @if ($autoDismiss) @click="show = false"
                @else
                wire:click="$dispatch('dismiss-notification')" @endif
                class="min-h-11 min-w-11 p-2 rounded-lg {{ $config['icon_color'] }} hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 transition-colors"
                aria-label="Tutup notifikasi">
                <x-heroicon-o-x-mark class="w-5 h-5" aria-hidden="true" />
            </button>
        @endif
    </div>
</div>
