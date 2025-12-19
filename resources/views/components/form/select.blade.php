{{--
/**
 * MyDS Design System - Select Component
 * @trace D13 §2.2-2.7, D14 §10.3
 * @requirements 3.4, 17.1 (ARIA enhancements)
 * WCAG 2.2 AA: 44px touch target, 3px focus ring, aria-describedby
 */
--}}

@props([
'disabled' => false,
'label' => null,
'error' => null,
'helper' => null,
'id' => null,
'options' => [],
'placeholder' => null,
'required' => false,
])

@php
$id = $id ?? ($attributes->get('wire:model') ?? ($attributes->get('name') ?? 'select-' . uniqid()));
$errorBag = $errors ?? null;
$hasError = $error || ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'has') && $errorBag->has($attributes->get('wire:model')));
$errorMessage = $error ?? ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'first') ? $errorBag->first($attributes->get('wire:model')) : null);
$isRequired = $required || $attributes->has('required');
$helperId = $helper ? $id . '-helper' : null;
$errorId = $hasError ? $id . '-error' : null;
$describedBy =
collect([$helperId, $errorId])
->filter()
->implode(' ') ?:
null;
@endphp

<div class="{{ $attributes->get('class') }}">
    @if ($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ $label }}
        @if ($isRequired)
        <span class="text-danger-500" aria-hidden="true">*</span>
        <span class="sr-only">{{ __('common.required') }}</span>
        @endif
    </label>
    @endif

    <div class="relative">
        <select id="{{ $id }}" {{ $disabled ? 'disabled' : '' }}
            @if ($isRequired) aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except(['class'])->merge([
                'class' =>
                    'block w-full min-h-11 rounded-lg border-gray-300 shadow-sm pr-10 ' .
                    'focus:ring-3 focus:ring-primary-500 focus:outline-none ' .
                    'transition-colors duration-200 ' .
                    'dark:bg-gray-800 dark:border-gray-600 dark:text-white ' .
                    ($hasError ? 'border-danger-500 text-danger-900 focus:ring-danger-500' : '') .
                    ($disabled ? ' opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-700' : ''),
            ]) }}>
            @if ($placeholder)
            <option value="" disabled>{{ $placeholder }}</option>
            @endif

            @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
            @endforeach

            {{ $slot }}
        </select>

        {{-- Dropdown indicator --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    @if ($errorMessage)
    <p id="{{ $errorId }}" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert">
        {{ $errorMessage }}
    </p>
    @elseif($helper)
    <p id="{{ $helperId }}" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ $helper }}
    </p>
    @endif
</div>
