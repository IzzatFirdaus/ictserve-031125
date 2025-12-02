<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                {{ __('Notifications') }}
                @if($unreadCount > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $unreadCount }}
                    </span>
                @endif
            </h2>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-2">
            @if(count($selectedIds) > 0)
                <button wire:click="markSelectedAsRead" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Mark Read') }}
                </button>
                <button wire:click="deleteSelected" type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Delete') }}
                </button>
            @else
                <button wire:click="markAllAsRead" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    {{ __('Mark All Read') }}
                </button>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        {{-- Filters --}}
        <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input wire:click="toggleUnreadFilter" id="unread_only" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ $unreadOnly ? 'checked' : '' }}>
                    <label for="unread_only" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ __('Unread Only') }}
                    </label>
                </div>
                
                <select wire:change="setTypeFilter($event.target.value)" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($availableTypes as $key => $label)
                        <option value="{{ $key }}" {{ $typeFilter === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <button wire:click="selectAll" class="text-sm text-primary-600 hover:text-primary-500">{{ __('Select All') }}</button>
                <span class="text-gray-300">|</span>
                <button wire:click="deselectAll" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Deselect All') }}</button>
            </div>
        </div>

        {{-- Notification List --}}
        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                <li class="relative hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150 ease-in-out {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:click="toggleSelection('{{ $notification->id }}')" type="checkbox" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded" {{ in_array($notification->id, $selectedIds) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-primary-600 truncate">
                                        {{ $notification->title }}
                                    </p>
                                    <div class="ml-2 shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ !$notification->is_read ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ !$notification->is_read ? __('New') : __('Read') }}
                                        </p>
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
                                            <svg class="shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $notification->created_at }}
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-xs sm:mt-0 space-x-4">
                                        @if($notification->url)
                                            <a href="{{ $notification->url }}" wire:click="markAsRead('{{ $notification->id }}')" class="font-medium text-primary-600 hover:text-primary-500">
                                                {{ __('View Details') }}
                                            </a>
                                        @endif
                                        @if(!$notification->is_read)
                                            <button wire:click="markAsRead('{{ $notification->id }}')" class="font-medium text-gray-500 hover:text-gray-700">
                                                {{ __('Mark as Read') }}
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
