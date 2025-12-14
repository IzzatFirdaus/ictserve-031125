{{--
/**
 * Component: Portal Notification Bell
 * Description: WCAG 2.2 AA compliant notification bell with dropdown
 * @author Pasukan BPM MOTAC
 * @trace D03 SRS-FR-006; D04 §3.5; D11 §9
 * @trace Requirements 10.2, 10.4 - Notification center with ARIA accessibility
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages, SC 2.4.11 Focus Not Obscured)
 * @version 2.0.0
 * @updated 2025-12-14
 */
--}}

<div class="relative" x-data="{ open: @entangle('open') }" @keydown.escape.window="open = false" @click.away="open = false">

    {{-- Notification Bell Button --}}
    <button type="button" wire:click="toggleDropdown"
        class="relative inline-flex items-center justify-center min-h-11 min-w-11 p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-200"
        aria-label="{{ __('Pemberitahuan') }} {{ $unreadCount > 0 ? '(' . $unreadCount . ' ' . __('belum dibaca') . ')' : '' }}"
        aria-expanded="{{ $open ? 'true' : 'false' }}" aria-haspopup="menu" aria-controls="notification-dropdown">

        {{-- Bell Icon --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Badge --}}
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold text-white bg-danger-600 rounded-full"
                aria-hidden="true">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" x-cloak id="notification-dropdown"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-dropdown border border-gray-200 dark:border-gray-700 z-50"
        role="menu" aria-orientation="vertical" aria-labelledby="notification-button" tabindex="-1">

        {{-- Header --}}
        <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Pemberitahuan Terkini') }}
            </h3>
            @if ($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead"
                    class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:underline min-h-8 px-2"
                    aria-label="{{ __('Tandakan semua sebagai dibaca') }}">
                    {{ __('Tandakan semua dibaca') }}
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <ul class="max-h-64 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700" role="list"
            aria-label="{{ __('Senarai pemberitahuan') }}">
            @forelse($notifications as $n)
                <li class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                    role="menuitem" wire:key="notification-{{ $n->id }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm text-gray-900 dark:text-gray-100 {{ $n->read_at === null ? 'font-medium' : '' }}">
                                {{ data_get($n->data, 'message', __('Pemberitahuan baharu')) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $n->created_at?->diffForHumans() ?? '' }}
                            </p>
                        </div>
                        @if ($n->read_at === null)
                            <button type="button" wire:click="markAsRead('{{ $n->id }}')"
                                class="shrink-0 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:underline min-h-8 px-2"
                                aria-label="{{ __('Tandakan sebagai dibaca') }}">
                                {{ __('Dibaca') }}
                            </button>
                        @else
                            <span class="shrink-0 w-2 h-2 bg-gray-300 dark:bg-gray-600 rounded-full"
                                aria-hidden="true"></span>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-6 text-center" role="menuitem">
                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Tiada pemberitahuan') }}
                    </p>
                </li>
            @endforelse
        </ul>

        {{-- Footer --}}
        @if ($notifications && $notifications->count() > 0)
            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ Route::has('notifications') ? route('notifications') : '#' }}"
                    class="block w-full text-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-150">
                    {{ __('Lihat semua pemberitahuan') }}
                </a>
            </div>
        @endif
    </div>

    {{-- ARIA Live Region for Screen Reader Announcements --}}
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        @if ($unreadCount > 0)
            {{ $unreadCount }} {{ __('pemberitahuan belum dibaca') }}
        @endif
    </div>
</div>
