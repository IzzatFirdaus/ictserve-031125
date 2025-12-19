{{--
/**
 * MyDS Design System - Toggle Component
 * @trace D13 §2.2-2.7, D14 §10.3
 * @requirements 3.4, 17.1 (ARIA enhancements)
 * WCAG 2.2 AA: 44px touch target, 3px focus ring, role="switch"
 */
--}}

@props([
'id' => null,
'label' => null,
'description' => null,
'disabled' => false,
])

@php
$id = $id ?? 'toggle-' . uniqid();
$descriptionId = $description ? $id . '-description' : null;
@endphp

<div class="flex items-center justify-between gap-4">
    <div class="flex flex-col">
        @if ($label)
        <label for="{{ $id }}"
            class="text-sm font-medium text-gray-900 dark:text-gray-100 {{ $disabled ? 'opacity-50' : '' }}">
            {{ $label }}
        </label>
        @endif
        @if ($description)
        <span id="{{ $descriptionId }}" class="text-xs text-gray-500 dark:text-gray-400">
            {{ $description }}
        </span>
        @endif
    </div>

    {{-- Toggle button with 44px touch target --}}
    <button type="button" role="switch" id="{{ $id }}" aria-checked="false"
        @if ($descriptionId) aria-describedby="{{ $descriptionId }}" @endif
        @if ($disabled) disabled aria-disabled="true" @endif
        x-data="{
            on: @entangle($attributes->wire('model')).live
        }"
        :aria-checked="on.toString()"
        @click="!{{ $disabled ? 'true' : 'false' }} && (on = !on)"
        :class="on ? 'bg-primary-500' : 'bg-gray-200 dark:bg-gray-600'"
        class="relative inline-flex h-7 w-12 min-h-11 min-w-11 items-center shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
        {{ $attributes->except(['wire:model', 'wire:model.live', 'class']) }}>
        <span class="sr-only">{{ $label ?? __('Toggle') }}</span>
        <span aria-hidden="true" :class="on ? 'translate-x-5' : 'translate-x-0'"
            class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-button ring-0 transition duration-200 ease-in-out"></span>
    </button>
</div>
