{{--
    name: portal/notification-center.blade.php
    description: Full notification center listing with filters & pagination
    trace: D03 SRS-FR-006; D04 §3.5; D11 §9
--}}
<div class="space-y-4">
    <div class="flex items-center gap-3 flex-wrap">
        <button type="button" wire:click="filterBy('all')" @class(['px-2 py-1 text-xs rounded', $filter==='all' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'])>{{ __('All') }}</button>
        <button type="button" wire:click="filterBy('unread')" @class(['px-2 py-1 text-xs rounded', $filter==='unread' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'])>{{ __('Unread') }}</button>
        <button type="button" wire:click="filterBy('read')" @class(['px-2 py-1 text-xs rounded', $filter==='read' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200'])>{{ __('Read') }}</button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded shadow divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($notifications as $n)
            <div class="p-3 flex items-start justify-between">
                <div class="space-y-1 flex-1">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $labelForType($n->type) }}</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300">{{ $n->data['message'] ?? '' }}</div>
                    @if($n->read_at)
                        <div class="text-xs text-green-600">{{ __('Read') }}</div>
                    @else
                        <div class="text-xs text-red-600">{{ __('Unread') }}</div>
                    @endif

                    {{-- Quick action links based on notification type --}}
                    @if(isset($n->data['ticket_id']))
                        <a href="#" class="text-xs text-blue-600 hover:text-blue-800">View Ticket</a>
                    @elseif(isset($n->data['loan_id']))
                        <a href="#" class="text-xs text-blue-600 hover:text-blue-800">View Loan</a>
                    @elseif(isset($n->data['asset_id']))
                        <a href="#" class="text-xs text-blue-600 hover:text-blue-800">View Asset</a>
                    @endif
                </div>
                <div class="flex flex-col gap-1 items-end">
                    <button type="button" wire:click="deleteNotification('{{ $n->id }}')" class="text-xs text-gray-500 hover:text-gray-700">{{ __('Delete') }}</button>
                    @if(!$n->read_at)
                        <button type="button" wire:click="markAsRead('{{ $n->id }}')" class="text-xs text-blue-600 hover:text-blue-800">{{ __('Mark read') }}</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-sm text-gray-500">{{ __('No notifications') }}</div>
        @endforelse
    </div>

    <div>{{ $notifications->links() }}</div>
</div>
