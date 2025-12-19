{{--
/**
 * MyDS Design System - Checkbox Component
 * @trace D13 §2.2-2.7, D14 §10.3
 * @requirements 3.4, 17.1 (ARIA enhancements)
 * WCAG 2.2 AA: 44px touch target area, 3px focus ring
 */
--}}

@props([
'disabled' => false,
'label' => null,
'error' => null,
'id' => null,
'description' => null,
'required' => false,
])

@php
$id = $id ?? ($attributes->get('wire:model') ?? ($attributes->get('name') ?? 'checkbox-' . uniqid()));
$errorBag = $errors ?? null;
$hasError = $error || ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'has') && $errorBag->has($attributes->get('wire:model')));
$errorMessage = $error ?? ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'first') ? $errorBag->first($attributes->get('wire:model')) : null);
$isRequired = $required || $attributes->has('required');
$descriptionId = $description ? $id . '-description' : null;
$errorId = $hasError ? $id . '-error' : null;
$describedBy =
collect([$descriptionId, $errorId])
->filter()
->implode(' ') ?:
null;
@endphp

<div class="relative flex items-start {{ $attributes->get('class') }}">
    {{-- Touch target wrapper for 44px minimum --}}
    <div class="flex h-6 items-center min-h-11 min-w-11 justify-center -ml-2">
        <input id="{{ $id }}" type="checkbox" {{ $disabled ? 'disabled' : '' }}
            @if ($isRequired) aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except(['class'])->merge([
                'class' =>
                    'h-5 w-5 rounded-sm border-gray-300 text-primary-500 ' .
                    'focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 ' .
                    'transition-colors duration-200 ' .
                    'dark:border-gray-600 dark:bg-gray-800 dark:ring-offset-gray-900 ' .
                    ($hasError ? 'border-danger-500 text-danger-500' : '') .
                    ($disabled ? ' opacity-50 cursor-not-allowed' : ' cursor-pointer'),
            ]) }}>
    </div>

    @if ($label || $description || $errorMessage)
    <div class="ml-2 text-sm leading-6">
        @if ($label)
        <label for="{{ $id }}"
            class="font-medium text-gray-900 dark:text-gray-100 {{ $disabled ? 'opacity-50' : 'cursor-pointer' }}">
            {{ $label }}
            @if ($isRequired)
            <span class="text-danger-500" aria-hidden="true">*</span>
            <span class="sr-only">{{ __('common.required') }}</span>
            @endif
        </label>
        @endif

        @if ($description)
        <p id="{{ $descriptionId }}" class="text-gray-500 dark:text-gray-400">
            {{ $description }}
        </p>
        @endif

        @if ($errorMessage)
        <p id="{{ $errorId }}" class="text-danger-600 dark:text-danger-400" role="alert">
            {{ $errorMessage }}
        </p>
        @endif
    </div>
    @endif
</div>
