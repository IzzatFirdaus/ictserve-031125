<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Notification Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <x-heroicon-o-bell class="w-8 h-8 text-primary-600" aria-hidden="true" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.notification_center.kpi.total') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['total'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <x-heroicon-o-envelope class="w-8 h-8 text-danger-600" aria-hidden="true" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.notification_center.kpi.unread') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['unread'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <x-heroicon-o-calendar class="w-8 h-8 text-success-600" aria-hidden="true" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.notification_center.kpi.today') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ $notificationStats['today'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="shrink-0">
                        <x-heroicon-o-chart-bar class="w-8 h-8 text-secondary-600" aria-hidden="true" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.notification_center.kpi.this_week') }}</p>
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
                    <button wire:click="setFilter('all')"
                        class="py-4 px-1 border-b-2 font-medium text-sm focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $activeFilter === 'all' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        aria-pressed="{{ $activeFilter === 'all' ? 'true' : 'false' }}">
                        {{ __('admin_pages.notification_center.tabs.all') }}
                        @if ($notificationStats['total'] > 0)
                            <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">
                                {{ $notificationStats['total'] }}
                            </span>
                        @endif
                    </button>

                    <button wire:click="setFilter('unread')"
                        class="py-4 px-1 border-b-2 font-medium text-sm focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $activeFilter === 'unread' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        aria-pressed="{{ $activeFilter === 'unread' ? 'true' : 'false' }}">
                        {{ __('admin_pages.notification_center.tabs.unread') }}
                        @if ($notificationStats['unread'] > 0)
                            <span class="ml-2 bg-danger-100 text-danger-900 py-0.5 px-2.5 rounded-full text-xs">
                                {{ $notificationStats['unread'] }}
                            </span>
                        @endif
                    </button>

                    <button wire:click="setFilter('read')"
                        class="py-4 px-1 border-b-2 font-medium text-sm focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 {{ $activeFilter === 'read' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                        aria-pressed="{{ $activeFilter === 'read' ? 'true' : 'false' }}">
                        {{ __('admin_pages.notification_center.tabs.read') }}
                    </button>
                </nav>
            </div>

            {{-- Notifications List --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700" aria-live="polite">
                @forelse($notifications as $notification)
                    <div class="p-6 {{ !$notification['is_read'] ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                        <div class="flex items-start space-x-4">
                            {{-- Notification Icon --}}
                            <div class="shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $notification['color'] === 'success'
                                        ? 'bg-success-100 text-success-600'
                                        : ($notification['color'] === 'danger'
                                            ? 'bg-danger-100 text-danger-600'
                                            : ($notification['color'] === 'warning'
                                                ? 'bg-warning-100 text-warning-600'
                                                : ($notification['color'] === 'info'
                                                    ? 'bg-primary-100 text-primary-600'
                                                    : 'bg-gray-100 text-gray-600'))) }}">
                                    <x-dynamic-component :component="$notification['icon']" class="w-5 h-5" aria-hidden="true" />
                                </div>
                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $notification['title'] }}
                                        </p>
                                        @if ($notification['priority'] === 'high')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                                {{ __('admin_pages.notification_center.badges.high_priority') }}
                                            </span>
                                        @elseif($notification['priority'] === 'urgent')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-danger-600 text-white">
                                                {{ __('admin_pages.notification_center.badges.urgent') }}
                                            </span>
                                        @endif
                                        @if (!$notification['is_read'])
                                            <span class="w-2 h-2 bg-primary-600 rounded-full"
                                                aria-label="{{ __('admin_pages.notification_center.tabs.unread') }}"></span>
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
                                    @if ($notification['action_url'])
                                        <button
                                            wire:click="handleNotificationAction('{{ $notification['id'] }}', '{{ $notification['action_url'] }}')"
                                            class="text-sm font-medium text-primary-600 hover:text-primary-500 min-h-11 flex items-center focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                            {{ $notification['action_label'] ?? __('admin_pages.notification_center.actions.view_details') }}
                                        </button>
                                    @endif

                                    @if (!$notification['is_read'])
                                        <button wire:click="markAsRead('{{ $notification['id'] }}')"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-500 min-h-11 flex items-center focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                                            {{ __('admin_pages.notification_center.actions.mark_read') }}
                                        </button>
                                    @else
                                        <button wire:click="markAsUnread('{{ $notification['id'] }}')"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-500 min-h-11 flex items-center focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                                            {{ __('admin_pages.notification_center.actions.mark_unread') }}
                                        </button>
                                    @endif

                                    <button wire:click="deleteNotification('{{ $notification['id'] }}')"
                                        wire:confirm="{{ __('admin_pages.notification_center.modals.delete_confirm') }}"
                                        class="text-sm font-medium text-danger-600 hover:text-danger-500 min-h-11 flex items-center focus-visible:ring-3 focus-visible:ring-danger-500 focus-visible:ring-offset-2">
                                        {{ __('admin_pages.notification_center.actions.delete') }}
                                    </button>
                                </div>

                                {{-- Read Status --}}
                                @if ($notification['is_read'] && $notification['read_at'])
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('admin_pages.notification_center.status.read_at', ['time' => $notification['read_at']->diffForHumans()]) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <x-heroicon-o-bell-slash class="mx-auto w-12 h-12 text-gray-400" aria-hidden="true" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            @if ($activeFilter === 'unread')
                                {{ __('admin_pages.notification_center.empty.unread_title') }}
                            @elseif($activeFilter === 'read')
                                {{ __('admin_pages.notification_center.empty.read_title') }}
                            @else
                                {{ __('admin_pages.notification_center.empty.title') }}
                            @endif
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('admin_pages.notification_center.empty.description') }}
                        </p>
                        <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">
                            {{ __('admin_pages.notification_center.empty.guidance') }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Load More Button --}}
            @if (count($notifications) >= $limit)
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <button wire:click="loadMoreNotifications"
                        class="w-full text-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus-visible:ring-3 focus-visible:ring-offset-2 focus-visible:ring-primary-500 min-h-11">
                        {{ __('admin_pages.notification_center.actions.load_more') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Auto-refresh every 30 seconds with visibility check --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let refreshInterval;

            function startRefresh() {
                refreshInterval = setInterval(function() {
                    if (!document.hidden) {
                        @this.call('refreshData');
                    }
                }, 30000);
            }

            function stopRefresh() {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                }
            }

            // Start refresh on page load
            startRefresh();

            // Pause/resume based on tab visibility
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopRefresh();
                } else {
                    startRefresh();
                }
            });
        });
    </script>
</x-filament-panels::page>
