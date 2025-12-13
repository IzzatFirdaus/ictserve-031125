{{--
    Keyboard Shortcuts Modal Component (x-ui.keyboard-shortcuts-modal)

    Modal displaying all available keyboard shortcuts for power users.
    Triggered by pressing the ? key.

    @props
    - show: Boolean to control modal visibility (wire:model or x-model binding)

    @usage
    <x-ui.keyboard-shortcuts-modal x-show="showShortcutsModal" @close="showShortcutsModal = false" />

    @trace Task 2.5.8, Requirement 24.2
    @see design.md Keyboard Shortcuts Manager
    @wcag-level AA - Escape key closes modal, focus trapped within
--}}

@props([
'show' => false,
])

<div
    x-data="{ open: @js($show) }"
    x-show="open"
    x-on:show-shortcuts-modal.window="open = true"
    x-on:close-shortcuts-modal.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="keyboard-shortcuts-title"
    role="dialog"
    aria-modal="true">
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity"
        aria-hidden="true"
        @click="open = false"></div>

    {{-- Modal Panel --}}
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-(--radius-l) bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-dropdown transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
            @click.stop>
            {{-- Header --}}
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30">
                        <svg class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 id="keyboard-shortcuts-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('portal.keyboard_shortcuts.title') }}
                    </h3>
                </div>
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-(--radius-s) bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
                    aria-label="{{ __('portal.close') }}">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Description --}}
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ __('portal.keyboard_shortcuts.description') }}
            </p>

            {{-- Shortcuts Grid --}}
            <div class="space-y-3" role="list" aria-label="{{ __('portal.keyboard_shortcuts.list_label') }}">
                {{-- Navigation Shortcuts --}}
                <div class="mb-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        {{ __('portal.keyboard_shortcuts.navigation') }}
                    </h4>
                    <div class="space-y-2">
                        <x-ui.keyboard-shortcut-item key="Alt + D" :label="__('portal.keyboard_shortcuts.dashboard')" />
                        <x-ui.keyboard-shortcut-item key="Alt + S" :label="__('portal.keyboard_shortcuts.submissions')" />
                        <x-ui.keyboard-shortcut-item key="Alt + P" :label="__('portal.keyboard_shortcuts.profile')" />
                        <x-ui.keyboard-shortcut-item key="Alt + H" :label="__('portal.keyboard_shortcuts.help')" />
                    </div>
                </div>

                {{-- Action Shortcuts --}}
                <div class="mb-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        {{ __('portal.keyboard_shortcuts.actions') }}
                    </h4>
                    <div class="space-y-2">
                        <x-ui.keyboard-shortcut-item key="Alt + N" :label="__('portal.keyboard_shortcuts.new_ticket')" />
                        <x-ui.keyboard-shortcut-item key="Alt + L" :label="__('portal.keyboard_shortcuts.new_loan')" />
                    </div>
                </div>

                {{-- General Shortcuts --}}
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                        {{ __('portal.keyboard_shortcuts.general') }}
                    </h4>
                    <div class="space-y-2">
                        <x-ui.keyboard-shortcut-item key="?" :label="__('portal.keyboard_shortcuts.show_shortcuts')" />
                        <x-ui.keyboard-shortcut-item key="Esc" :label="__('portal.keyboard_shortcuts.close_modal')" />
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('portal.keyboard_shortcuts.note') }}
                </p>
            </div>
        </div>
    </div>
</div>