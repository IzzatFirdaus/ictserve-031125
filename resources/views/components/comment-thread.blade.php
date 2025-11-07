@props(['comment', 'editingCommentId' => null, 'editingContent' => '', 'depth' => 0])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700 {{ $depth > 0 ? 'ml-8' : '' }}"
     x-data="{ showActions: false }"
     @mouseenter="showActions = true"
     @mouseleave="showActions = false">

    {{-- Comment Header --}}
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
            {{-- User Avatar --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
            </div>

            {{-- User Info --}}
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $comment->user->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $comment->created_at->diffForHumans() }}
                    @if ($comment->created_at != $comment->updated_at)
                        <span class="text-gray-400 dark:text-gray-500">({{ __('internal_comments.edited') }})</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        @if ($editingCommentId !== $comment->id)
            <div x-show="showActions" x-transition class="flex items-center gap-2">
                {{-- Reply Button (max depth 3) --}}
                @if ($depth < 2)
                    <button
                        wire:click="startReply({{ $comment->id }})"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        title="{{ __('internal_comments.reply') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        <span class="sr-only">{{ __('internal_comments.reply') }}</span>
                    </button>
                @endif

                {{-- Edit Button (owner or Admin/Superuser) --}}
                @can('update', $comment)
                    <button
                        wire:click="startEdit({{ $comment->id }})"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        title="{{ __('internal_comments.edit') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span class="sr-only">{{ __('internal_comments.edit') }}</span>
                    </button>
                @endcan

                {{-- Delete Button (owner or Admin/Superuser) --}}
                @can('delete', $comment)
                    <button
                        wire:click="deleteComment({{ $comment->id }})"
                        wire:confirm="{{ __('internal_comments.delete_confirm') }}"
                        class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                        title="{{ __('internal_comments.delete') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span class="sr-only">{{ __('internal_comments.delete') }}</span>
                    </button>
                @endcan
            </div>
        @endif
    </div>

    {{-- Comment Content --}}
    @if ($editingCommentId === $comment->id)
        {{-- Edit Form --}}
        <form wire:submit.prevent="saveEdit" class="mb-3">
            <textarea
                wire:model="editingContent"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
            ></textarea>
            @error('editingContent')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
            @enderror

            <div class="flex items-center gap-2 mt-3">
                <button
                    type="submit"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    {{ __('internal_comments.save') }}
                </button>
                <button
                    type="button"
                    wire:click="cancelEdit"
                    class="px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm rounded-lg transition-colors"
                >
                    {{ __('internal_comments.cancel') }}
                </button>
            </div>
        </form>
    @else
        {{-- Display Content --}}
        <div class="prose prose-sm dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->content }}</p>
        </div>
    @endif

    {{-- Nested Replies (max depth 3) --}}
    @if ($comment->replies->count() > 0)
        <div class="mt-4 space-y-3">
            @foreach ($comment->replies as $reply)
                <x-comment-thread
                    :comment="$reply"
                    :editing-comment-id="$editingCommentId"
                    :editing-content="$editingContent"
                    :depth="$depth + 1"
                />
            @endforeach
        </div>
    @endif
</div>
