{{--
/**
 * Component: Form Radio Button
 * Description: WCAG 2.2 AA compliant radio button with proper labeling and ARIA attributes
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-003.4 (Form Accessibility)
 * @trace D03-FR-017.1 (Form Input Requirements)
 * @trace D12 §6.2 (Form Design Guidelines)
 * @trace D13 §3.7.1 (Real-time Validation)
 * @trace D14 §10.3 (Form Accessibility)
 * @wcag WCAG 2.2 Level AA (SC 1.3.1 Info and Relationships, SC 4.1.2 Name, Role, Value)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Requirements:
 * - 3.4: Form accessibility with proper ARIA attributes
 * - 17.1: Form input with aria-describedby, aria-invalid, aria-required
 * - 44×44px minimum touch target per WCAG 2.5.8
 *
 * Usage:
 * <x-form.radio
 *     name="priority"
 *     value="high"
 *     label="Keutamaan Tinggi"
 *     wire:model="priority"
 * />
 *
 * @example Radio Group
 * <fieldset>
 *     <legend class="text-sm font-medium text-gray-700">Pilih Keutamaan</legend>
 *     <div class="mt-2 space-y-2">
 *         <x-form.radio name="priority" value="low" label="Rendah" wire:model="priority" />
 *         <x-form.radio name="priority" value="medium" label="Sederhana" wire:model="priority" />
 *         <x-form.radio name="priority" value="high" label="Tinggi" wire:model="priority" />
 *     </div>
 * </fieldset>
 */
--}}

@props([
    'disabled' => false,
    'label' => null,
    'description' => null,
    'error' => null,
    'id' => null,
    'name' => null,
    'value' => null,
    'checked' => false,
])

@php
    // Generate unique ID if not provided
    $id = $id ?? 'radio-' . ($name ?? '') . '-' . ($value ?? '') . '-' . uniqid();

    // Get wire:model for Livewire error binding
    $wireModel =
        $attributes->get('wire:model') ?? ($attributes->get('wire:model.live') ?? $attributes->get('wire:model.defer'));

    // Determine if field has error
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

    // Build aria-describedby references
    $describedBy = [];
    if ($description) {
        $describedBy[] = $id . '-description';
    }
    if ($hasError && $errorMessage) {
        $describedBy[] = $id . '-error';
    }
    $ariaDescribedBy = !empty($describedBy) ? implode(' ', $describedBy) : null;

    // Radio input classes with error state styling
    $radioClasses =
        'h-5 w-5 border-gray-300 text-primary-600 ' .
        'focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 ' .
        'dark:border-gray-600 dark:bg-gray-800 dark:focus:ring-offset-gray-900 ' .
        'disabled:opacity-50 disabled:cursor-not-allowed ' .
        ($hasError ? 'border-danger-500 dark:border-danger-400' : '');
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'relative flex items-start']) }}>
    {{-- Radio input with 44×44px touch target wrapper --}}
    <div class="flex h-11 w-11 items-center justify-center shrink-0 -ml-3">
        <input type="radio" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
            @if ($disabled) disabled @endif @if ($checked) checked @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($ariaDescribedBy) aria-describedby="{{ $ariaDescribedBy }}" @endif
            {{ $attributes->except(['class', 'type', 'disabled', 'checked'])->merge([
                'class' => $radioClasses,
            ]) }}>
    </div>

    {{-- Label and description --}}
    <div class="ml-1 text-sm leading-6">
        @if ($label)
            <label for="{{ $id }}"
                class="font-medium text-gray-900 dark:text-gray-100 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                {{ $label }}
            </label>
        @endif

        @if ($description)
            <p id="{{ $id }}-description" class="text-gray-500 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif

        {{-- Error message --}}
        @if ($hasError && $errorMessage)
            <p id="{{ $id }}-error" class="mt-1 text-sm text-danger-600 dark:text-danger-400" role="alert"
                aria-live="polite">
                {{ $errorMessage }}
            </p>
        @endif
    </div>
</div>
