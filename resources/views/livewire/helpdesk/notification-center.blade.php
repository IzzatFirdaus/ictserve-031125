{{--
    Notification Center Component

    Displays user notifications with filtering, mark-as-read functionality,
    and real-time updates via Laravel Echo.

    @trace Requirement 7.5
    @wcag WCAG 2.2 AA compliant with proper ARIA labels
    @component Livewire component: App\Livewire\Helpdesk\NotificationCenter
--}}
<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    {{-- Notification Bell Button --}}
    <button @click="open = !open" type="button"
        class="relative rounded-full bg-white p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        aria-label="{{ __('Notifikasi') }}" aria-expanded="false" aria-haspopup="true">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Count Badge --}}
        @if ($this->unreadCount > 0)
            <span
                class="absolute right-0 top-0 inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-xs font-bold text-white">
                {{ $this->unreadCount > 9 ? '9+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu" aria-orientation="vertical" aria-labelledby="notification-menu" style="display: none;">
        {{-- Header --}}
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Notifikasi') }}</h3>
                @if ($this->unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-sm font-medium text-blue-600 hover:text-blue-700"
                        aria-label="{{ __('Tandakan semua sebagai dibaca') }}">
                        {{ __('Tandakan Semua') }}
                    </button>
                @endif
            </div>

            {{-- Filter Tabs --}}
            <div class="mt-3 flex gap-2" role="tablist">
                <button wire:click="setFilter('all')"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    role="tab" aria-selected="{{ $filter === 'all' ? 'true' : 'false' }}">
                    {{ __('Semua') }}
                </button>
                <button wire:click="setFilter('unread')"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'unread' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    role="tab" aria-selected="{{ $filter === 'unread' ? 'true' : 'false' }}">
                    {{ __('Belum Dibaca') }}
                    @if ($this->unreadCount > 0)
                        <span
                            class="ml-1 inline-flex items-center rounded-full bg-blue-600 px-2 py-0.5 text-xs font-medium text-white">
                            {{ $this->unreadCount }}
                        </span>
                    @endif
                </button>
                <button wire:click="setFilter('read')"
                    class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ $filter === 'read' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                    role="tab" aria-selected="{{ $filter === 'read' ? 'true' : 'false' }}">
                    {{ __('Dibaca') }}
                </button>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto" role="list">
            @forelse ($this->notifications as $notification)
                <div class="border-b border-gray-100 px-4 py-3 transition-colors hover:bg-gray-50 {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}"
                    role="listitem">
                    <div class="flex items-start gap-3">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full {{ is_null($notification->read_at) ? 'bg-blue-100' : 'bg-gray-100' }}">
                                <svg class="h-5 w-5 {{ is_null($notification->read_at) ? 'text-blue-600' : 'text-gray-600' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $notification->data['title'] ?? __('Notifikasi Baru') }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-1">
                            @if (is_null($notification->read_at))
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                    class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                    aria-label="{{ __('Tandakan sebagai dibaca') }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endif
                            <button wire:click="deleteNotification('{{ $notification->id }}')"
                                class="rounded p-1 text-gray-400 hover:bg-gray-100 hover:text-red-600"
                                aria-label="{{ __('Padam notifikasi') }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">
                        @if ($filter === 'unread')
                            {{ __('Tiada notifikasi belum dibaca.') }}
                        @elseif ($filter === 'read')
                            {{ __('Tiada notifikasi dibaca.') }}
                        @else
                            {{ __('Tiada notifikasi.') }}
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if ($this->notifications->isNotEmpty())
            <div class="border-t border-gray-200 px-4 py-3 text-center">
                <a href="{{ route('helpdesk.authenticated.notifications') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700">
                    {{ __('Lihat Semua Notifikasi') }} &rarr;
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Real-time Updates Script --}}
@script
    <script>
        // Listen for Laravel Echo notifications
        if (typeof Echo !== 'undefined') {
            Echo.private(`notifications.${@js(auth()->id())}`)
                .notification((notification) => {
                    // Dispatch Livewire event to refresh notifications
                    $wire.dispatch('notification-received');

                    // Show browser notification if permitted
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification(notification.title || 'New Notification', {
                            body: notification.message || '',
                            icon: '/favicon.ico'
                        });
                    }
                });
        }

        // Request notification permission on component mount
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    </script>
@endscript
