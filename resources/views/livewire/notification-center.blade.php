<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                {{ __('notifications.title') }}
                @if($unreadCount > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-danger-100 dark:bg-danger-900/50 text-danger-800 dark:text-danger-200" aria-label="{{ __('notifications.unread_count', ['count' => $unreadCount]) }}">
                        {{ $unreadCount }}
                    </span>
                @endif
            </h1>
        </div>
        <div class="mt-4 flex flex-wrap md:mt-0 md:ml-4 gap-2">
            @if(count($selectedIds) > 0)
                <button wire:click="markSelectedAsRead" type="button" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 transition-colors"
                    aria-label="{{ __('notifications.mark_selected_read') }}">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('notifications.mark_read') }}
                </button>
                <button wire:click="deleteSelected" type="button" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-danger-600 hover:bg-danger-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-danger-500 transition-colors"
                    aria-label="{{ __('common.delete_selected') }}">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('common.delete') }}
                </button>
            @else
                <button wire:click="markAllAsRead" type="button" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 transition-colors"
                    aria-label="{{ __('notifications.mark_all_as_read') }}">
                    {{ __('notifications.mark_all_as_read') }}
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        {{-- Filters --}}
        <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center">
                    <input wire:click="toggleUnreadFilter" id="unread_only" type="checkbox" 
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 focus-visible:ring-2 focus-visible:ring-offset-2 border-gray-300 dark:border-gray-600 rounded transition-colors" 
                        {{ $unreadOnly ? 'checked' : '' }}
                        aria-describedby="unread-help">
                    <label for="unread_only" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">
                        {{ __('notifications.filter_unread') }}
                    </label>
                </div>
                
                <select wire:change="setTypeFilter($event.target.value)" 
                    class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white transition-colors"
                    aria-label="{{ __('notifications.filter_by_type') }}">
                    <option value="">{{ __('notifications.filter_all') }}</option>
                    @foreach($availableTypes as $key => $label)
                        <option value="{{ $key }}" {{ $typeFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2">
                <button wire:click="selectAll" type="button"
                    class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded px-2 py-1 transition-colors">
                    {{ __('common.select_all') }}
                </button>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <button wire:click="deselectAll" type="button"
                    class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded px-2 py-1 transition-colors">
                    {{ __('common.deselect_all') }}
                </button>
            </div>
        </div>

        {{-- Notification List --}}
        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <li class="relative hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-150 ease-in-out {{ !$notification->is_read ? 'bg-primary-50/50 dark:bg-primary-900/10 border-l-4 border-primary-500' : '' }}">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:click="toggleSelection('{{ $notification->id }}')" type="checkbox" 
                                    class="focus:ring-primary-500 focus-visible:ring-2 focus-visible:ring-offset-2 h-4 w-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded transition-colors" 
                                    {{ in_array($notification->id, $selectedIds) ? 'checked' : '' }}
                                    aria-label="{{ __('common.select_item') }}">
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $notification->title }}
                                    </p>
                                    <div class="ml-2 shrink-0 flex">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ !$notification->is_read ? 'bg-success-100 dark:bg-success-900/50 text-success-800 dark:text-success-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                                            {{ !$notification->is_read ? __('common.new') : __('common.read') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $notification->message }}
                                    </p>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                            <svg class="shrink-0 mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <time datetime="{{ $notification->created_at }}">{{ $notification->created_at }}</time>
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-xs sm:mt-0 gap-4">
                                        @if($notification->url)
                                            <a href="{{ $notification->url }}" wire:click="markAsRead('{{ $notification->id }}')" 
                                                class="font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-1 rounded px-1 py-0.5 transition-colors">
                                                {{ __('notifications.view_details') }}
                                            </a>
                                        @endif
                                        @if(!$notification->is_read)
                                            <button wire:click="markAsRead('{{ $notification->id }}')" type="button"
                                                class="font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-1 rounded px-1 py-0.5 transition-colors">
                                                {{ __('notifications.mark_as_read') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-2 text-sm font-medium">{{ __('No notifications found') }}</p>
                </li>
            @endforelse
        </ul>
        
        {{-- Pagination --}}
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
