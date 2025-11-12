<div class="space-y-4">
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                    <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }} ago</span>
                </div>
                <p class="text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>

                @if($comment->canHaveReplies())
                    <button wire:click="replyToComment({{ $comment->id }})" class="text-sm text-blue-600 hover:text-blue-700 mt-2">
                        Reply
                    </button>
                @endif

                {{-- Nested replies --}}
                @if($comment->replies->isNotEmpty())
                    <div class="ml-6 mt-3 space-y-2">
                        @foreach($comment->replies as $reply)
                            <div class="bg-white dark:bg-gray-700 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-medium text-sm text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }} ago</span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->comment }}</p>

                                @if($reply->canHaveReplies())
                                    <button wire:click="replyToComment({{ $reply->id }})" class="text-xs text-blue-600 hover:text-blue-700 mt-1">
                                        Reply
                                    </button>
                                @endif

                                {{-- Level 3 nested replies --}}
                                @if($reply->replies->isNotEmpty())
                                    <div class="ml-4 mt-2 space-y-2">
                                        @foreach($reply->replies as $nestedReply)
                                            <div class="bg-gray-50 dark:bg-gray-600 rounded-lg p-2">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="font-medium text-xs text-gray-900 dark:text-white">{{ $nestedReply->user->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $nestedReply->created_at->diffForHumans() }} ago</span>
                                                </div>
                                                <p class="text-xs text-gray-700 dark:text-gray-300">{{ $nestedReply->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">No comments yet</p>
        @endforelse
    </div>

    <form wire:submit="addComment" class="mt-4">
        @if($replyingTo)
            <div class="mb-2 p-2 bg-blue-50 dark:bg-blue-900 rounded flex justify-between items-center">
                <span class="text-sm text-blue-700 dark:text-blue-300">Replying to comment...</span>
                <button type="button" wire:click="cancelReply" class="text-blue-600 hover:text-blue-700">✕</button>
            </div>
        @endif

        <textarea
            wire:model="newComment"
            rows="3"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
            placeholder="Add a comment... Use @username to mention someone"
            maxlength="1000"
        ></textarea>

        <div class="flex justify-between items-center mt-2">
            <span class="text-sm text-gray-500">{{ 1000 - strlen($newComment) }} characters remaining</span>
            @error('newComment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Hantar
        </button>
    </form>
</div>
