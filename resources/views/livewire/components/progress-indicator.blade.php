<?php
/**
 * Progress Indicator Volt Component v3.6.0
 *
 * Progress indicator for long-running operations using Livewire Volt 1.10.
 * Implements ARIA live regions for accessibility announcements.
 *
 * Features:
 * - Determinate and indeterminate progress modes
 * - Step-based progress tracking
 * - ARIA live region announcements
 * - Customizable appearance
 * - WCAG 2.2 AA compliant
 * - Bahasa Melayu exclusive interface
 *
 * @see D12 UI/UX Design Guide - Progress Components
 * @see D13 Frontend Framework - Livewire Volt Patterns
 * @see Requirements 6.4, 6.5, 7.4 - Loading states and accessibility
 */

use function Livewire\Volt\{state, computed, on};

// Component state
state([
    'progress' => 0,
    'total' => 100,
    'currentStep' => 0,
    'totalSteps' => 0,
    'message' => '',
    'isIndeterminate' => false,
    'isVisible' => false,
    'variant' => 'primary', // primary, success, warning, danger
]);

// Computed percentage
$percentage = computed(function () {
    if ($this->isIndeterminate || $this->total === 0) {
        return 0;
    }
    return min(100, round(($this->progress / $this->total) * 100));
});

// Computed step text
$stepText = computed(function () {
    if ($this->totalSteps > 0) {
        return "Langkah {$this->currentStep} daripada {$this->totalSteps}";
    }
    return '';
});

// Start progress
$start = function (array $config = []): void {
    $this->progress = $config['progress'] ?? 0;
    $this->total = $config['total'] ?? 100;
    $this->currentStep = $config['currentStep'] ?? 0;
    $this->totalSteps = $config['totalSteps'] ?? 0;
    $this->message = $config['message'] ?? 'Memproses...';
    $this->isIndeterminate = $config['indeterminate'] ?? false;
    $this->variant = $config['variant'] ?? 'primary';
    $this->isVisible = true;
};

// Update progress
$update = function (int $progress, ?string $message = null): void {
    $this->progress = min($progress, $this->total);
    if ($message !== null) {
        $this->message = $message;
    }
};

// Update step
$updateStep = function (int $step, ?string $message = null): void {
    $this->currentStep = min($step, $this->totalSteps);
    if ($message !== null) {
        $this->message = $message;
    }
};

// Complete progress
$complete = function (?string $message = null): void {
    $this->progress = $this->total;
    $this->currentStep = $this->totalSteps;
    $this->message = $message ?? 'Selesai!';
    $this->isIndeterminate = false;
};

// Hide progress
$hide = function (): void {
    $this->isVisible = false;
    $this->reset();
};

// Reset state
$reset = function (): void {
    $this->progress = 0;
    $this->total = 100;
    $this->currentStep = 0;
    $this->totalSteps = 0;
    $this->message = '';
    $this->isIndeterminate = false;
};

// Listen for progress events
on([
    'progress-start' => function (array $config) {
        $this->start($config);
    },
]);

on([
    'progress-update' => function (int $progress, ?string $message = null) {
        $this->update($progress, $message);
    },
]);

on([
    'progress-step' => function (int $step, ?string $message = null) {
        $this->updateStep($step, $message);
    },
]);

on([
    'progress-complete' => function (?string $message = null) {
        $this->complete($message);
    },
]);

on([
    'progress-hide' => function () {
        $this->hide();
    },
]);

?>

<div x-data="{ show: $wire.entangle('isVisible') }" x-show="show" x-cloak x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform -translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform -translate-y-2" class="progress-indicator" role="progressbar"
    aria-valuemin="0" aria-valuemax="{{ $total }}" aria-valuenow="{{ $isIndeterminate ? null : $progress }}"
    aria-valuetext="{{ $message }}" aria-label="Kemajuan operasi">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-card border border-slate-200 dark:border-slate-700 p-4">
        {{-- Header with message and percentage --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex-1">
                {{-- Message with ARIA live region --}}
                <p class="text-sm font-medium text-slate-900 dark:text-slate-100" role="status" aria-live="polite"
                    aria-atomic="true">
                    {{ $message }}
                </p>

                {{-- Step indicator --}}
                @if ($this->stepText)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $this->stepText }}
                    </p>
                @endif
            </div>

            {{-- Percentage --}}
            @if (!$isIndeterminate)
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 ml-4">
                    {{ $this->percentage }}%
                </span>
            @endif
        </div>

        {{-- Progress Bar --}}
        <div class="relative h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
            @if ($isIndeterminate)
                {{-- Indeterminate animation --}}
                <div @class([
                    'absolute inset-y-0 w-1/3 rounded-full animate-indeterminate',
                    'bg-primary-500' => $variant === 'primary',
                    'bg-success-500' => $variant === 'success',
                    'bg-warning-500' => $variant === 'warning',
                    'bg-danger-500' => $variant === 'danger',
                ])></div>
            @else
                {{-- Determinate progress --}}
                <div @class([
                    'absolute inset-y-0 left-0 rounded-full transition-all duration-300 ease-out',
                    'bg-primary-500' => $variant === 'primary',
                    'bg-success-500' => $variant === 'success',
                    'bg-warning-500' => $variant === 'warning',
                    'bg-danger-500' => $variant === 'danger',
                ]) style="width: {{ $this->percentage }}%"></div>
            @endif
        </div>

        {{-- Steps indicator (if using steps) --}}
        @if ($totalSteps > 0)
            <div class="mt-4 flex items-center justify-between">
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex items-center">
                        {{-- Step circle --}}
                        <div @class([
                            'flex items-center justify-center w-8 h-8 rounded-full text-xs font-medium transition-colors',
                            'bg-primary-500 text-white' => $i <= $currentStep,
                            'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' =>
                                $i > $currentStep,
                        ])>
                            @if ($i < $currentStep)
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                {{ $i }}
                            @endif
                        </div>

                        {{-- Connector line --}}
                        @if ($i < $totalSteps)
                            <div @class([
                                'w-full h-0.5 mx-2',
                                'bg-primary-500' => $i < $currentStep,
                                'bg-slate-200 dark:bg-slate-700' => $i >= $currentStep,
                            ])></div>
                        @endif
                    </div>
                @endfor
            </div>
        @endif

        {{-- Screen reader announcement --}}
        <div class="sr-only" aria-live="assertive" aria-atomic="true">
            @if ($isIndeterminate)
                Operasi sedang dijalankan. {{ $message }}
            @else
                Kemajuan: {{ $this->percentage }} peratus. {{ $message }}
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes indeterminate {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(400%);
        }
    }

    .animate-indeterminate {
        animation: indeterminate 1.5s ease-in-out infinite;
    }
</style>
