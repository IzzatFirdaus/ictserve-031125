<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Notification Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-bell class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Notifications</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['total'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-envelope class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unread</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['unread'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-calendar class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['today'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-chart-bar class="h-8 w-8 text-purple-600" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">This Week</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['this_week'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button
                        wire:click="setFilter('all')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeFilter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        All Notifications
                        @if($notificationStats['total'] > 0)
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                                {{ $notificationStats['total'] }}
                            </span>
                        @endif
                    </button>
                    
                    <button
                        wire:click="setFilter('unread')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeFilter === 'unread' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Unread
                        @if($notificationStats['unread'] > 0)
                            <span class="ml-2 bg-red-100 text-red-900 py-0.5 px-2.5 rounded-full text-xs">
                                {{ $notificationStats['unread'] }}
                            </span>
                        @endif
                    </button>
                    
                    <button
                        wire:click="setFilter('read')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeFilter === 'read' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Read
                    </button>
                </nav>
            </div>

            {{-- Notifications List --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($notifications as $notification)
                    <div class="p-6 {{ !$notification['is_read'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                        <div class="flex items-start space-x-4">
                            {{-- Notification Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $notification['color'] === 'success' ? 'bg-green-100 text-green-600' : 
                                       ($notification['color'] === 'danger' ? 'bg-red-100 text-red-600' : 
                                        ($notification['color'] === 'warning' ? 'bg-yellow-100 text-yellow-600' : 
                                         ($notification['color'] === 'info' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'))) }}">
                                    @php
                                        $iconComponent = str_replace('heroicon-o-', 'heroicon-o-', $notification['icon']);
                                    @endphp
                                    <x-dynamic-component :component="$iconComponent" class="h-5 w-5" />
                                </div>
                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $notification['title'] }}
                                        </p>
                                        @if($notification['priority'] === 'high')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                High Priority
                                            </span>
                                        @elseif($notification['priority'] === 'urgent')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white">
                                                Urgent
                                            </span>
                                        @endif
                                        @if(!$notification['is_read'])
                                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $notification['created_at']->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $notification['message'] }}
                                </p>

                                {{-- Notification Actions --}}
                                <div class="mt-3 flex items-center space-x-4">
                                    @if($notification['action_url'])
                                        <button
                                            wire:click="handleNotificationAction('{{ $notification['id'] }}', '{{ $notification['action_url'] }}')"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-500"
                                        >
                                            {{ $notification['action_label'] ?? 'View Details' }}
                                        </button>
                                    @endif

                                    @if(!$notification['is_read'])
                                        <button
                                            wire:click="markAsRead('{{ $notification['id'] }}')"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-500"
                                        >
                                            Mark as Read
                                        </button>
                                    @else
                                        <button
                                            wire:click="markAsUnread('{{ $notification['id'] }}')"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-500"
                                        >
                                            Mark as Unread
                                        </button>
                                    @endif

                                    <button
                                        wire:click="deleteNotification('{{ $notification['id'] }}')"
                                        wire:confirm="Are you sure you want to delete this notification?"
                                        class="text-sm font-medium text-red-600 hover:text-red-500"
                                    >
                                        Delete
                                    </button>
                                </div>

                                {{-- Read Status --}}
                                @if($notification['is_read'] && $notification['read_at'])
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Read {{ $notification['read_at']->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <x-heroicon-o-bell-slash class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($activeFilter === 'unread')
                                You have no unread notifications.
                            @elseif($activeFilter === 'read')
                                You have no read notifications.
                            @else
                                You don't have any notifications yet.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Load More Button --}}
            @if(count($notifications) >= 50)
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button
                        wire:click="loadMoreNotifications"
                        class="w-full text-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Load More Notifications
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Auto-refresh every 30 seconds --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setInterval(function() {
                @this.call('loadNotifications');
            }, 30000);
        });
    </script>
</x-filament-panels::page>