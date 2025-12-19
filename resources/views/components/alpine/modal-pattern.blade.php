{{--
    name: modal-pattern.blade.php
    description: Alpine.js modal pattern with focus trap documentation
    author: dev-team@motac.gov.my
    trace: D13 §5 (Alpine.js Patterns)
    requirements: 5.2
    last-updated: 2025-01-06
--}}

{{-- Modal with Focus Trap --}}
<div
    x-data="{ show: false }"
    @keydown.escape.window="show = false"
    @open-modal.window="show = true"
>
    <button @click="show = true" class="min-h-11 px-4 py-2 bg-primary-600 text-white rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
        Open Modal
    </button>

    <div
        x-show="show"
        x-trap="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
    >
        {{-- Backdrop --}}
        <div
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
            @click="show = false"
        ></div>

        {{-- Modal Content --}}
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6"
                @click.stop
            >
                <h2 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Modal Title
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Modal content goes here
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="show = false"
                        class="min-h-11 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    >
                        Cancel
                    </button>
                    <button
                        @click="show = false"
                        class="min-h-11 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{--
Usage Notes:
- x-trap="show" - Trap focus within modal when open
- @keydown.escape.window="show = false" - Close on Escape key
- @open-modal.window - Listen for custom event
- role="dialog" - Semantic dialog role
- aria-modal="true" - Indicate modal behavior
- aria-labelledby="modal-title" - Link to title
- @click.stop - Prevent backdrop click from closing
--}}
