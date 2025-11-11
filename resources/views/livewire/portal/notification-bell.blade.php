{{--
    name: portal/notification-bell.blade.php
    description: Portal notification bell dropdown
    trace: D03 SRS-FR-006; D04 §3.5; D11 §9
--}}

<div class="relative">
    <button type="button" wire:click="toggleDropdown" class="inline-flex items-center gap-2">
        <span>Notifications</span>
        @if($unreadCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-white bg-red-600 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div class="mt-2 w-80 bg-white dark:bg-gray-800 rounded shadow border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-2">
            <strong class="text-sm">Recent notifications</strong>
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="text-xs text-blue-600">Mark all as read</button>
            @endif
        </div>
        <ul class="max-h-64 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $n)
                <li class="p-2 flex items-start justify-between">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ data_get($n->data, 'message', '') }}</div>
                    <button type="button" wire:click="markAsRead('{{ $n->id }}')" class="text-xs text-gray-500 hover:text-gray-700">Read</button>
                </li>
            @empty
                <li class="p-4 text-sm text-gray-500">No notifications</li>
            @endforelse
        </ul>
    </div>
</div>
