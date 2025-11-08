<div class="space-y-4">
    <div class="space-y-3">
        @forelse($comments as $comment)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                    <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-gray-700 dark:text-gray-300">{{ $comment->comment }}</p>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">{{ __('common.no_comments') }}</p>
        @endforelse
    </div>

    <form wire:submit="addComment" class="mt-4">
        <textarea 
            wire:model="comment" 
            rows="3" 
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
            placeholder="{{ __('common.add_comment') }}"
        ></textarea>
        @error('comment') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            {{ __('common.submit') }}
        </button>
    </form>
</div>
