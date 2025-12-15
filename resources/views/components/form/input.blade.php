{{--
/**
 * Component: Form Input
 * Description: WCAG 2.2 AA compliant form input with comprehensive ARIA enhancements
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-003.4 (Form Accessibility)
 * @trace D03-FR-017.1 (Form Input Requirements)
 * @trace D12 §6.2 (Form Design Guidelines)
 * @trace D13 §3.7.1 (Real-time Validation)
 * @trace D14 §10.3 (Form Accessibility)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1 Info and Relationships, SC 3.3.2 Labels or Instructions)
 * @version 2.0.0
 * @updated 2025-12-05
 *
 * Requirements:
 * - 3.4: Form accessibility with proper ARIA attributes
 * - 17.1: Form input with aria-describedby, aria-invalid, aria-required
 *
 * ARIA Enhancements (D13 §3.7.1, D14 §10.3):
 * - aria-describedby: Links input to hint/error messages
 * - aria-invalid: Indicates error state for screen readers
 * - aria-required: Indicates required fields
 * - aria-errormessage: Points to error message element (WCAG 2.2)
 *
 * Usage:
 * <x-form.input
 *     label="Email Address"
 *     type="email"
 *     name="email"
 *     wire:model.live.debounce.300ms="email"
 *     required
 *     helper="We'll never share your email"
 * />
 */
--}}

@props([
'disabled' => false,
'label' => null,
'error' => null,
'helper' => null,
'hint' => null,
'id' => null,
'type' => 'text',
'required' => false,
'autocomplete' => null,
])

@php
// Generate unique ID if not provided
$id =
$id ??
($attributes->get('wire:model') ??
($attributes->get('name') ?? 'input-' . md5(serialize($attributes->getAttributes()))));

// Get wire:model for Livewire error binding
$wireModel =
$attributes->get('wire:model') ??
($attributes->get('wire:model.live') ?? $attributes->get('wire:model.live.debounce.300ms'));

// Determine if field has error (from prop or Livewire errors)
$hasError =
$error ||
($wireModel &&
isset($errors) &&
is_object($errors) &&
method_exists($errors, 'has') &&
$errors->has($wireModel));
$errorMessage =
$error ??
($wireModel && isset($errors) && is_object($errors) && method_exists($errors, 'first')
? $errors->first($wireModel)
: null);

// Determine if field is required (from prop or attribute)
$isRequired = $required || $attributes->has('required');

// Build aria-describedby references
$describedBy = [];
if ($helper || $hint) {
$describedBy[] = $id . '-helper';
}
if ($hasError && $errorMessage) {
$describedBy[] = $id . '-error';
}
$ariaDescribedBy = !empty($describedBy) ? implode(' ', $describedBy) : null;

// Input classes with error state styling per D14 §4.1.1
$inputClasses =
'block w-full rounded-md shadow-sm sm:text-sm min-h-11 px-3 py-2 ' .
'border transition-colors duration-200 ' .
'focus:outline-none ' .
'dark:bg-gray-800 dark:text-white ' .
($hasError
? 'border-danger-500 text-danger-900 placeholder-danger-400 focus:border-danger-500 dark:border-danger-400 dark:text-danger-100'
: 'border-gray-300 focus:border-primary-500 dark:border-gray-600');
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    {{-- Label with required indicator per D12 §6.2 --}}
    @if ($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
        {{ $label }}
        @if ($isRequired)
        <abbr title="{{ __('Required field') }}" class="text-danger-500 no-underline ml-0.5"
            aria-label="{{ __('required') }}">*</abbr>
        @endif
    </label>
    @endif

    {{-- Helper/Hint text above input per D14 §10.3 --}}
    @if ($helper || $hint)
    <p id="{{ $id }}-helper" class="text-sm text-gray-500 dark:text-gray-400 mb-1.5">
        {{ $helper ?? $hint }}
    </p>
    @endif

    <div class="relative">
        <input type="{{ $type }}" id="{{ $id }}" @if ($disabled) disabled @endif
            @if ($isRequired) required
            aria-required="true" @endif
            @if ($hasError) aria-invalid="true"
            aria-errormessage="{{ $id }}-error" @endif
            @if ($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {{ $attributes->except(['class', 'type', 'required', 'disabled'])->merge([
                'class' => $inputClasses,
            ]) }}>

        {{-- Error icon indicator --}}
        @if ($hasError)
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3" aria-hidden="true">
            <svg class="h-5 w-5 text-danger-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
        </div>
        @endif
    </div>

    {{-- Error message with ARIA live region per D14 §10.4 --}}
    @if ($hasError && $errorMessage)
    <p id="{{ $id }}-error"
        class="mt-1.5 text-sm text-danger-600 dark:text-danger-400 flex items-center gap-1" role="alert"
        aria-live="polite">
        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
            aria-hidden="true">
            <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd" />
        </svg>
        <span>{{ $errorMessage }}</span>
    </p>
    @endif
</div>
