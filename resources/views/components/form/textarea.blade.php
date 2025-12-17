{{--
/**
 * MyDS Design System - Textarea Component
 * @trace D13 §2.2-2.7, D14 §10.3
 * @requirements 3.4, 17.1 (ARIA enhancements)
 * WCAG 2.2 AA: 3px focus ring, aria-describedby, aria-invalid
 */
--}}

@props([
'disabled' => false,
'label' => null,
'error' => null,
'helper' => null,
'id' => null,
'rows' => 3,
'required' => false,
'maxlength' => null,
'showCount' => false,
])

@php
$id = $id ?? ($attributes->get('wire:model') ?? ($attributes->get('name') ?? 'textarea-' . uniqid()));
$errorBag = $errors ?? null;
$hasError = $error || ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'has') && $errorBag->has($attributes->get('wire:model')));
$errorMessage = $error ?? ($attributes->has('wire:model') && $errorBag && is_object($errorBag) && method_exists($errorBag, 'first') ? $errorBag->first($attributes->get('wire:model')) : null);
$isRequired = $required || $attributes->has('required');
$helperId = $helper ? $id . '-helper' : null;
$errorId = $hasError ? $id . '-error' : null;
$countId = $showCount && $maxlength ? $id . '-count' : null;
$describedBy =
collect([$helperId, $errorId, $countId])
->filter()
->implode(' ') ?:
null;
@endphp

<div class="{{ $attributes->get('class') }}" @if ($showCount && $maxlength) x-data="{ charCount: 0 }" @endif>
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
        <textarea id="{{ $id }}" rows="{{ $rows }}" {{ $disabled ? 'disabled' : '' }}
            @if ($isRequired) aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($maxlength) maxlength="{{ $maxlength }}" @endif
            @if ($showCount && $maxlength) x-on:input="charCount = $el.value.length" @endif
            {{ $attributes->except(['class'])->merge([
                'class' =>
                    'block w-full rounded-md border-gray-300 shadow-sm ' .
                    'focus:border-primary-500 focus:outline-none ' .
                    'transition-colors duration-200 ' .
                    'dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 ' .
                    ($hasError
                        ? 'border-danger-500 text-danger-900 placeholder-danger-300 focus:border-danger-500'
                        : '') .
                    ($disabled ? ' opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-700' : ''),
            ]) }}>{{ $slot }}</textarea>
    </div>

    <div class="flex justify-between items-center mt-1">
        <div>
            @if ($errorMessage)
            <p id="{{ $errorId }}" class="text-sm text-danger-600 dark:text-danger-400" role="alert">
                {{ $errorMessage }}
            </p>
            @elseif($helper)
            <p id="{{ $helperId }}" class="text-sm text-gray-500 dark:text-gray-400">
                {{ $helper }}
            </p>
            @endif
        </div>

        @if ($showCount && $maxlength)
        <p id="{{ $countId }}" class="text-sm text-gray-500 dark:text-gray-400" aria-live="polite">
            <span x-text="charCount">0</span>/{{ $maxlength }}
        </p>
        @endif
    </div>
</div>
