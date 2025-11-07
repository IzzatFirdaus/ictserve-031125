<div class="space-y-6" x-data="{ scrollToForm() { document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth' }); } }" @scroll-to-form.window="scrollToForm()">
    {{-- Flash Messages --}}
    @if (session()->has('comment-success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/30 p-4" role="alert">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('comment-success') }}</p>
        </div>
    @endif

    @if (session()->has('comment-error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/30 p-4" role="alert">
            <p class="text-sm text-red-800 dark:text-red-200">{{ session('comment-error') }}</p>
        </div>
    @endif

    {{-- Comment Form --}}
    <div id="comment-form" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            @if ($replyingToId)
                {{ __('internal_comments.replying_to') }}
            @else
                {{ __('internal_comments.add_comment') }}
            @endif
        </h3>

        <form wire:submit.prevent="addComment">
            <div class="mb-4">
                <label for="new-comment" class="sr-only">{{ __('internal_comments.comment_label') }}</label>
                <textarea
                    wire:model="newCommentContent"
                    id="new-comment"
                    rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="{{ __('internal_comments.comment_placeholder') }}"
                    required
                ></textarea>
                @error('newCommentContent')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors focus:ring-4 focus:ring-blue-300"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('internal_comments.submit') }}</span>
                    <span wire:loading>{{ __('internal_comments.submitting') }}</span>
                </button>

                @if ($replyingToId)
                    <button
                        type="button"
                        wire:click="cancelReply"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        {{ __('internal_comments.cancel_reply') }}
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse ($comments as $comment)
            <x-comment-thread :comment="$comment" :editing-comment-id="$editingCommentId" :editing-content="$editingContent" />
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('internal_comments.no_comments') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $comments->links() }}
    </div>
</div>
