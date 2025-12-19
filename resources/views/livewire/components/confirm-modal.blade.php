<?php
/**
 * Confirm Modal Volt Component v3.6.0
 *
 * Reusable confirmation modal using Livewire Volt 1.10.
 * Implements focus trap, keyboard navigation, and ARIA attributes.
 *
 * Features:
 * - Focus trap for accessibility
 * - Keyboard navigation (Escape to close)
 * - Multiple variants (info, warning, danger)
 * - Customizable actions
 * - WCAG 2.2 AA compliant
 * - Bahasa Melayu exclusive interface
 *
 * @see D12 UI/UX Design Guide - Modal Components
 * @see D13 Frontend Framework - Livewire Volt Patterns
 * @see Requirements 6.2, 7.3, 7.4 - Modal accessibility
 */

use function Livewire\Volt\{state, on};

// Component state
state([
    'isOpen' => false,
    'title' => '',
    'message' => '',
    'variant' => 'warning', // info, warning, danger
    'confirmText' => 'Sahkan',
    'cancelText' => 'Batal',
    'confirmAction' => null,
    'confirmData' => null,
    'isProcessing' => false,
]);

// Open modal with configuration
$open = function (array $config): void {
    $this->title = $config['title'] ?? 'Pengesahan';
    $this->message = $config['message'] ?? 'Adakah anda pasti?';
    $this->variant = $config['variant'] ?? 'warning';
    $this->confirmText = $config['confirmText'] ?? 'Sahkan';
    $this->cancelText = $config['cancelText'] ?? 'Batal';
    $this->confirmAction = $config['action'] ?? null;
    $this->confirmData = $config['data'] ?? null;
    $this->isOpen = true;
    $this->isProcessing = false;
};

// Close modal
$close = function (): void {
    $this->isOpen = false;
    $this->isProcessing = false;
    $this->confirmAction = null;
    $this->confirmData = null;
};

// Confirm action
$confirm = function (): void {
    $this->isProcessing = true;

    if ($this->confirmAction) {
        $this->dispatch($this->confirmAction, $this->confirmData);
    }

    $this->dispatch('modal-confirmed', [
        'action' => $this->confirmAction,
        'data' => $this->confirmData,
    ]);

    $this->close();
};

// Listen for open-confirm-modal event
on([
    'open-confirm-modal' => function (array $config) {
        $this->open($config);
    },
]);

// Listen for close-confirm-modal event
on([
    'close-confirm-modal' => function () {
        $this->close();
    },
]);

?>

<div x-data="{
    focusableElements: null,
    firstFocusable: null,
    lastFocusable: null,
    init() {
        this.$watch('$wire.isOpen', (value) => {
            if (value) {
                this.$nextTick(() => this.setupFocusTrap());
            }
        });
    },
    setupFocusTrap() {
        this.focusableElements = this.$refs.modal?.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])' ); if (this.focusableElements?.length) {
    this.firstFocusable=this.focusableElements[0];
    this.lastFocusable=this.focusableElements[this.focusableElements.length - 1]; this.firstFocusable.focus(); } },
    handleTab(e) { if (!this.focusableElements?.length) return; if (e.shiftKey &&
    document.activeElement===this.firstFocusable) { e.preventDefault(); this.lastFocusable.focus(); } else if
    (!e.shiftKey && document.activeElement===this.lastFocusable) { e.preventDefault(); this.firstFocusable.focus(); } }
    }" x-on:keydown.escape.window="$wire.isOpen && $wire.close()" x-on:keydown.tab="handleTab($event)">
    {{-- Modal Backdrop --}}
    <div x-show="$wire.isOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-description">
        <div class="flex min-h-screen items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 transition-opacity" x-on:click="$wire.close()" aria-hidden="true">
            </div>

            {{-- Modal Panel --}}
            <div x-ref="modal" x-show="$wire.isOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full overflow-hidden">
                {{-- Modal Header with Icon --}}
                <div class="p-6 pb-4">
                    <div class="flex items-start gap-4">
                        {{-- Icon based on variant --}}
                        <div @class([
                            'shrink-0 flex items-center justify-center w-12 h-12 rounded-full',
                            'bg-blue-100 dark:bg-blue-900/30' => $variant === 'info',
                            'bg-warning-100 dark:bg-warning-900/30' => $variant === 'warning',
                            'bg-danger-100 dark:bg-danger-900/30' => $variant === 'danger',
                        ])>
                            @if ($variant === 'info')
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            @elseif($variant === 'warning')
                                <svg class="w-6 h-6 text-warning-600 dark:text-warning-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-danger-600 dark:text-danger-400"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                            @endif
                        </div>

                        {{-- Title and Message --}}
                        <div class="flex-1">
                            <h3 id="confirm-modal-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $title }}
                            </h3>
                            <p id="confirm-modal-description" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $message }}
                            </p>
                        </div>

                        {{-- Close Button --}}
                        <button type="button" wire:click="close"
                            class="shrink-0 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                            aria-label="Tutup">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Actions --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-3">
                    <button type="button" wire:click="close"
                        class="px-4 py-2 min-h-11 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors"
                        :disabled="$wire.isProcessing">
                        {{ $cancelText }}
                    </button>
                    <button type="button" wire:click="confirm" wire:loading.attr="disabled"
                        @class([
                            'px-4 py-2 min-h-11 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed',
                            'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' => $variant === 'info',
                            'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500' =>
                                $variant === 'warning',
                            'bg-danger-600 hover:bg-danger-700 focus:ring-danger-500' =>
                                $variant === 'danger',
                        ]) :disabled="$wire.isProcessing">
                        <span wire:loading.remove wire:target="confirm">
                            {{ $confirmText }}
                        </span>
                        <span wire:loading wire:target="confirm" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
}
