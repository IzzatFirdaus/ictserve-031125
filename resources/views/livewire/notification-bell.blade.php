{{--
/**
 * Notification Bell Component View
 *
 * Real-time notification bell with dropdown showing categorized notifications.
 *
 * Features:
 * - Unread count badge with --bg-danger token per D14 §4.1.1
 * - Heroicon bell icon (w-5 h-5) per D14 §8.1
 * - Categorized tabs (All, Tickets, Loans, System) per D12 §6.4
 * - shadow-dropdown styling per D14 §7.5
 * - ARIA live regions for screen reader announcements per D14 §10.4
 * - 44×44px minimum touch targets per D12 §4.1
 * - Keyboard navigation per D12 §6.11
 *
 * @see D03 SRS-FR-008
 * @see D12 §2 Real-time features
 * @see D12 §6.4 Notification categories
 * @see D14 §4.1.1 Danger token
 * @see D14 §7.5 Shadow tokens
 * @see D14 §8.1 Heroicons
 * @see D14 §10.4 ARIA live regions
 *
 * @requirements 15.1-15.5 Real-time notification UI
 *
 * @wcag-level AA
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */
--}}

<div x-data="{
    open: $wire.entangle('showDropdown'),
    activeCategory: $wire.entangle('activeCategory')
}" @click.away="open = false"
    @keydown.escape.window="if (open) { open = false; $refs.bellButton.focus(); }" class="relative"
    wire:poll.30s="refreshNotifications">

    {{-- ARIA Live Region for Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="notification-announcer">
        @if ($unreadCount > 0)
            {{ __('notifications.unread_count', ['count' => $unreadCount]) }}
        @endif
    </div>

    {{-- Bell Button - 44×44px minimum touch target per D12 §4.1 --}}
    <button x-ref="bellButton" @click="open = !open; if (open) $wire.loadNotifications()" type="button"
        class="relative inline-flex items-center justify-center min-w-11 min-h-11 p-2 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 rounded-lg transition-colors"
        :aria-expanded="open" aria-haspopup="true" aria-controls="notification-dropdown"
        aria-label="{{ __('notifications.bell_aria', ['count' => $unreadCount]) }}">

        {{-- Bell Icon (Heroicon w-5 h-5) per D14 §8.1 --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Count Badge with --bg-danger token per D14 §4.1.1 --}}
        @if ($unreadCount > 0)
            <span
                class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-5 h-5 px-1.5 text-xs font-bold text-white bg-danger-600 rounded-full ring-2 ring-white dark:ring-slate-800"
                aria-hidden="true">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel with shadow-dropdown per D14 §7.5 --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" x-cloak id="notification-dropdown"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden z-50"
        role="menu" aria-orientation="vertical" aria-labelledby="notifications-menu">

        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                {{ __('notifications.title') }}
            </h3>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" type="button"
                    class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 rounded px-2 py-2 min-h-11 transition-colors">
                    {{ __('notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        {{-- Category Tabs per D12 §6.4 --}}
        <div class="px-2 py-2 border-b border-slate-200 dark:border-slate-700 flex gap-1 overflow-x-auto scrollbar-thin" role="tablist" aria-label="{{ __('notifications.category_filter') }}">
            @foreach ($categories as $key => $label)
                <button wire:click="setCategory('{{ $key }}')" type="button" role="tab"
                    :aria-selected="activeCategory === '{{ $key }}'"
                    class="px-3 py-2.5 text-xs font-medium rounded-md whitespace-nowrap transition-all duration-150 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 min-h-11
                               {{ $activeCategory === $key
                                   ? 'bg-primary-600 dark:bg-primary-600 text-white shadow-sm'
                                   : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                    {{ __("notifications.category.{$key}") !== "notifications.category.{$key}" ? __("notifications.category.{$key}") : $label }}
                    @if ($key !== 'all')
                        @php
                            $count = count(array_filter($recentNotifications, fn($n) => $n['category'] === $key));
                        @endphp
                        @if ($count > 0)
                            <span class="ml-1 text-xs opacity-75">({{ $count }})</span>
                        @endif
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Notifications List --}}
        <div class="max-h-80 overflow-y-auto" role="tabpanel">
            @forelse($filteredNotifications as $notification)
                <div wire:key="notification-{{ $notification['id'] }}"
                    class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700 last:border-b-0 transition-all duration-150 cursor-pointer focus-within:bg-slate-50 dark:focus-within:bg-slate-700/50"
                    role="menuitem" tabindex="-1">
                    <div class="flex items-start gap-3">
                        {{-- Notification Icon --}}
                        <div
                            class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center {{ $notification['iconBg'] }} shadow-sm">
                            <x-dynamic-component :component="$notification['icon']" class="w-4 h-4 text-current" aria-hidden="true" />
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                                {{ $notification['title'] }}
                            </p>
                            @if ($notification['message'])
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                                    {{ $notification['message'] }}
                                </p>
                            @endif
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                {{ $notification['created_at'] }}
                            </p>

                            {{-- Action Links --}}
                            <div class="flex items-center gap-3 mt-2">
                                @if ($notification['url'])
                                    <a href="{{ $notification['url'] }}"
                                        class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded px-1 py-0.5 transition-colors">
                                        {{ __('notifications.view_details') }}
                                    </a>
                                @endif
                                <button wire:click="markAsRead('{{ $notification['id'] }}')" type="button"
                                    class="text-xs font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded px-1 py-0.5 transition-colors">
                                    {{ __('notifications.mark_read') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ __('notifications.no_new') }}
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            <a href="{{ route('staff.notifications') }}"
                class="block text-center text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded-md py-2 transition-colors"
                aria-label="{{ __('notifications.view_all') }}">
                {{ __('notifications.view_all') }}
            </a>
        </div>
    </div>
</div>

{{-- Echo/Reverb Integration Script --}}
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for notification-received events to update ARIA announcer
            Livewire.on('notification-received', (data) => {
                const announcer = document.getElementById('notification-announcer');
                if (announcer) {
                    announcer.textContent =
                        `You have ${data.count} unread notification${data.count !== 1 ? 's' : ''}`;
                }
            });
        });
    </script>
@endpush
