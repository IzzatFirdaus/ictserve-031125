{{--
/**
 * Confirmation Dialog Component
 *
 * Specialized modal for confirming destructive or important actions.
 *
 * Features:
lear warning message with --txt-danger token per D14 §4.1.1
 * - Destructive action styling with danger button per D14 §6.5
 * - Focus trap and keyboard navigation per D12 §6.11
 * - 44×44px minimum touch targets per D12 §4.1
 * - Follows D13 §3.7.2 confirmation dialog pattern
 *
 * @props name: string - Unique dialog identifier
 * @props title: string - Dialog title
 * @props message: string - Confirmation message
 * @props confirmText: string - Confirm button text
 * @props cancelText: string - Cancel button text
 * @props variant: danger|warning|info - Visual variant
 * @props icon: bool - Show warning icon
 *
 * @see D13 §3.7.2 Confirmation dialog pattern
 * @see D14 §4.1.1 Danger token
 * @see D14 §6.5 Button styling
 *
 * @requirements 28.2 Confirmation dialog
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-12-05
 */
--}}

@props([
'name' => 'confirm',
'title' => null,
'message' => null,
'confirmText' => null,
'cancelText' => null,
'variant' => 'danger',
'icon' => true,
])

@php
$title = $title ?? __('Confirm Action');
$message = $message ?? __('Are you sure you want to proceed? This action cannot be undone.');
$confirmText = $confirmText ?? __('Confirm');
$cancelText = $cancelText ?? __('Cancel');

$variantConfig = match ($variant) {
'danger' => [
'iconBg' => 'bg-danger-100 dark:bg-danger-900/50',
'iconColor' => 'text-danger-600 dark:text-danger-400',
'buttonClass' => 'bg-danger-600 hover:bg-danger-700',
],
'warning' => [
'iconBg' => 'bg-warning-100 dark:bg-warning-900/50',
'iconColor' => 'text-warning-600 dark:text-warning-400',
'buttonClass' => 'bg-warning-600 hover:bg-warning-700',
],
'info' => [
'iconBg' => 'bg-primary-100 dark:bg-primary-900/50',
'iconColor' => 'text-primary-600 dark:text-primary-400',
'buttonClass' => 'bg-primary-600 hover:bg-primary-700',
],
default => [
'iconBg' => 'bg-danger-100 dark:bg-danger-900/50',
'iconColor' => 'text-danger-600 dark:text-danger-400',
'buttonClass' => 'bg-danger-600 hover:bg-danger-700',
],
};
@endphp

<div x-data="{
    show: false,
    name: '{{ $name }}',
    triggerElement: null,
    onConfirm: null,
    onCancel: null,
    init() {
        this.$watch('show', (value) => {
            if (value) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
                if (this.triggerElement) {
                    this.$nextTick(() => this.triggerElement.focus());
                }
            }
        });
    },
    open(options = {}) {
        this.triggerElement = options.trigger || document.activeElement;
        this.onConfirm = options.onConfirm || null;
        this.onCancel = options.onCancel || null;
        this.show = true;
    },
    close() {
        this.show = false;
    },
    confirm() {
        if (this.onConfirm) this.onConfirm();
        this.close();
    },
    cancel() {
        if (this.onCancel) this.onCancel();
        this.close();
    }
}" x-show="show"
    x-on:open-confirm.window="if ($event.detail === name || $event.detail?.name === name) open($event.detail)"
    x-on:close-confirm.window="if ($event.detail === name || $event.detail?.name === name) close()"
    x-on:keydown.escape.window="if (show) cancel()" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="alertdialog"
    aria-modal="true" aria-labelledby="confirm-title-{{ $name }}"
    aria-describedby="confirm-message-{{ $name }}" {{ $attributes }}>

    {{-- Backdrop --}}
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity" aria-hidden="true"></div>

    {{-- Dialog Panel Container --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            {{-- Dialog Panel --}}
            <div x-show="show" x-trap.noscroll.inert="show" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-(--radius-l) bg-white dark:bg-gray-800 text-left shadow-dropdown transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icon --}}
                        @if ($icon)
                        <div
                            class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full {{ $variantConfig['iconBg'] }} sm:mx-0 sm:h-10 sm:w-10">
                            @if ($variant === 'danger' || $variant === 'warning')
                            <svg class="h-6 w-6 {{ $variantConfig['iconColor'] }}" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            @else
                            <svg class="h-6 w-6 {{ $variantConfig['iconColor'] }}" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            @endif
                        </div>
                        @endif

                        {{-- Content --}}
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 id="confirm-title-{{ $name }}"
                                class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                {{ $title }}
                            </h3>
                            <div class="mt-2">
                                <p id="confirm-message-{{ $name }}"
                                    class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $message }}
                                </p>
                            </div>

                            {{-- Custom content slot --}}
                            @if ($slot->isNotEmpty())
                            <div class="mt-4">
                                {{ $slot }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Actions - 44×44px minimum touch targets per D12 §4.1 --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <button type="button" @click="confirm()"
                        class="inline-flex w-full justify-center items-center min-h-11 rounded-m px-4 py-2 text-sm font-semibold text-white shadow-sm {{ $variantConfig['buttonClass'] }} focus:outline-none sm:w-auto transition-colors">
                        {{ $confirmText }}
                    </button>
                    <button type="button" @click="cancel()"
                        class="mt-3 inline-flex w-full justify-center items-center min-h-11 rounded-m bg-white dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none sm:mt-0 sm:w-auto transition-colors">
                        {{ $cancelText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reduced motion support --}}
<style>
    @media (prefers-reduced-motion: reduce) {

        [x-transition\:enter],
        [x-transition\:leave] {
            transition-duration: 0.01ms !important;
        }
    }
</style>