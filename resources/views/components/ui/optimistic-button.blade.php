{{--
    Optimistic Action Button Component (x-ui.optimistic-button)

    Button that provides immediate UI feedback while server processes request.
    Includes automatic rollback on server failure for graceful error handling.

    @props
    - label: Button text when in default state
    - processingLabel: Button text while processing (default: 'Processing...')
    - doneLabel: Button text after successful completion
    - errorLabel: Button text when error occurs
    - type: Button type (primary, secondary, danger, success)
    - action: Livewire action to call (e.g., "$wire.approve()")
    - icon: Optional icon slot
    - doneIcon: Optional icon for completed state
    - disabled: Whether button is disabled

    @usage
    <x-ui.optimistic-button
        label="Approve"
        processingLabel="Approving..."
        doneLabel="Approved"
        action="$wire.approve()"
        type="success"
    />

    @trace Task 3.1.4, Task 4.4.3, Requirement 12
    @see design.md Optimistic UI Pattern
    @wcag-level AA - Uses aria-live for status updates
--}}

@props([
'label' => 'Submit',
'processingLabel' => null,
'doneLabel' => null,
'errorLabel' => null,
'type' => 'primary',
'action' => null,
'disabled' => false,
])

@php
$processingLabel = $processingLabel ?? __('common.processing');
$doneLabel = $doneLabel ?? __('common.done');
$errorLabel = $errorLabel ?? __('common.retry');

// Type-based styling
$typeClasses = [
'primary' => 'bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500',
'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 focus:ring-gray-500',
'danger' => 'bg-danger-600 hover:bg-danger-700 text-white focus:ring-danger-500',
'success' => 'bg-success-600 hover:bg-success-700 text-white focus:ring-success-500',
'warning' => 'bg-warning-600 hover:bg-warning-700 text-white focus:ring-warning-500',
];

$baseClasses = 'inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium rounded-m transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed';
$typeClass = $typeClasses[$type] ?? $typeClasses['primary'];
@endphp

<div
    x-data="optimisticButton(false)"
    {{ $attributes->merge(['class' => 'inline-block']) }}>
    {{-- Button --}}
    <button
        type="button"
        @click="submit(() => {{ $action }})"
        :disabled="processing || done || {{ $disabled ? 'true' : 'false' }}"
        :class="{
            'opacity-75': processing,
            '{{ $typeClasses['success'] ?? '' }}': done && !error,
            '{{ $typeClasses['danger'] ?? '' }}': error
        }"
        class="{{ $baseClasses }} {{ $typeClass }}"
        :aria-busy="processing"
        :aria-disabled="done || processing">
        {{-- Default State --}}
        <template x-if="!processing && !done && !error">
            <span class="flex items-center gap-2">
                @if(isset($icon))
                {{ $icon }}
                @endif
                <span>{{ $label }}</span>
            </span>
        </template>

        {{-- Processing State --}}
        <template x-if="processing">
            <span class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ $processingLabel }}</span>
            </span>
        </template>

        {{-- Done State --}}
        <template x-if="done && !error && !processing">
            <span class="flex items-center gap-2">
                @if(isset($doneIcon))
                {{ $doneIcon }}
                @else
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                @endif
                <span>{{ $doneLabel }}</span>
            </span>
        </template>

        {{-- Error State (allows retry) --}}
        <template x-if="error && !processing">
            <span class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>{{ $errorLabel }}</span>
            </span>
        </template>
    </button>

    {{-- Error Message --}}
    <div
        x-show="error"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-1"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-cloak
        class="mt-2 text-sm text-danger-600 dark:text-danger-400"
        role="alert"
        aria-live="polite">
        <span x-text="error"></span>
    </div>

    {{-- Screen Reader Status --}}
    <div class="sr-only" aria-live="polite" aria-atomic="true">
        <span x-show="processing">{{ $processingLabel }}</span>
        <span x-show="done && !error">{{ $doneLabel }}</span>
        <span x-show="error" x-text="error"></span>
    </div>
</div>