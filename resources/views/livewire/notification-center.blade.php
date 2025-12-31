{{--
/**
 * Notification Center Component View
 *
 * Full notification management interface with search, filtering, bulk actions, and export.
 *
 * Features:
 * - Search functionality across notification titles and messages
 * - Advanced filtering by type, status, and date range
 * - Bulk actions (mark as read, delete selected)
 * - Export functionality (CSV, JSON)
 * - Improved ARIA support for accessibility
 * - Keyboard navigation support
 *
 * @see D03 SRS-FR-008
 * @see D04 §5.3
 * @see D12 §4 Requirements 3.7, 3.8, 6.3, 6.4, 6.5
 * @see D14 §10.4 ARIA live regions
 *
 * @requirements 3.7, 3.8, 7.1, 7.2
 *
 * @wcag-level AA
 *
 * @version 3.0.0
 *
 * @updated 2025-12-30
 */
--}}

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition" x-data="{
    showFilters: false,
    showExportModal: $wire.entangle('showExportModal')
}">

    {{-- ARIA Live Region for Screen Reader Announcements --}}
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="notification-center-announcer"
        x-on:aria-announce.window="$el.textContent = $event.detail.message">
    </div>

    <main id="main-content" class="max-w-6xl mx-auto" tabindex="-1">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                        {{ __('notifications.title') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('notifications.total_count', ['total' => $totalCount, 'unread' => $unreadCount]) }}
                    </p>
                </div>

                {{-- Header Actions --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Export Button --}}
                    <button type="button" wire:click="openExportModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg
                                   bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                                   border border-gray-300 dark:border-gray-600
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                   transition-colors min-h-11"
                        aria-label="{{ __('notifications.export') }}">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" aria-hidden="true" />
                        <span class="hidden sm:inline">{{ __('notifications.export') }}</span>
                    </button>

                    {{-- Toggle Filters Button --}}
                    <button type="button" @click="showFilters = !showFilters"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg
                                   bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                                   border border-gray-300 dark:border-gray-600
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                   transition-colors min-h-11"
                        :aria-expanded="showFilters" aria-controls="filter-panel">
                        <x-heroicon-o-funnel class="w-4 h-4" aria-hidden="true" />
                        <span class="hidden sm:inline">{{ __('notifications.filters') }}</span>
                        @if ($hasActiveFilters)
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-primary-600 rounded-full">
                                !
                            </span>
                        @endif
                    </button>

                    {{-- Mark All Read Button --}}
                    @if ($unreadCount > 0)
                        <button type="button" wire:click="markAllAsRead"
                            wire:confirm="{{ __('notifications.confirm_mark_all_read') }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg
                                       bg-primary-600 text-white
                                       hover:bg-primary-700
                                       focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2
                                       transition-colors min-h-11">
                            <x-heroicon-o-check-circle class="w-4 h-4" aria-hidden="true" />
                            <span class="hidden sm:inline">{{ __('notifications.mark_all_read') }}</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Search and Filter Panel --}}
        <div class="mb-6 space-y-4">
            {{-- Search Bar --}}
            <div class="relative">
                <label for="search-notifications" class="sr-only">{{ __('notifications.search_placeholder') }}</label>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" aria-hidden="true" />
                </div>
                <input type="search" id="search-notifications" wire:model.live.debounce.300ms="searchQuery"
                    placeholder="{{ __('notifications.search_placeholder') }}"
                    class="block w-full pl-10 pr-4 py-3 text-sm
                              bg-white dark:bg-gray-800
                              border border-gray-300 dark:border-gray-600
                              rounded-lg shadow-sm
                              text-gray-900 dark:text-white
                              placeholder-gray-500 dark:placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                              transition-colors"
                    aria-describedby="search-help">
                <p id="search-help" class="sr-only">{{ __('notifications.search_help') }}</p>
            </div>

            {{-- Advanced Filters Panel --}}
            <div x-show="showFilters" x-collapse x-cloak id="filter-panel"
                class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Status Filter --}}
                    <div>
                        <label for="filter-status"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('notifications.filter_status') }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="filter-status" wire:model.live="unreadOnly"
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span
                                class="text-sm text-gray-600 dark:text-gray-400">{{ __('notifications.unread_only') }}</span>
                        </div>
                    </div>

                    {{-- Type Filter --}}
                    <div>
                        <label for="filter-type"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('notifications.filter_type') }}
                        </label>
                        <select id="filter-type" wire:model.live="typeFilter"
                            class="block w-full px-3 py-2 text-sm
                                       bg-white dark:bg-gray-700
                                       border border-gray-300 dark:border-gray-600
                                       rounded-lg
                                       text-gray-900 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">{{ __('notifications.all_types') }}</option>
                            @foreach ($availableTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div>
                        <label for="filter-date-from"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('notifications.filter_date_from') }}
                        </label>
                        <input type="date" id="filter-date-from" wire:model.live="dateFrom"
                            class="block w-full px-3 py-2 text-sm
                                      bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-lg
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    {{-- Date To --}}
                    <div>
                        <label for="filter-date-to"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('notifications.filter_date_to') }}
                        </label>
                        <input type="date" id="filter-date-to" wire:model.live="dateTo"
                            class="block w-full px-3 py-2 text-sm
                                      bg-white dark:bg-gray-700
                                      border border-gray-300 dark:border-gray-600
                                      rounded-lg
                                      text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                {{-- Clear Filters --}}
                @if ($hasActiveFilters)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="clearFilters"
                            class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300
                                       focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded px-2 py-1">
                            {{ __('notifications.clear_filters') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Bulk Actions Bar --}}
        @if (count($selectedIds) > 0)
            <div class="mb-4 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800
                        flex flex-wrap items-center justify-between gap-3"
                role="toolbar" aria-label="{{ __('notifications.bulk_actions') }}">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-primary-700 dark:text-primary-300">
                        {{ __('notifications.selected_count', ['count' => count($selectedIds)]) }}
                    </span>
                    <button type="button" wire:click="deselectAll"
                        class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded px-2 py-1">
                        {{ __('notifications.deselect_all') }}
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="markSelectedAsRead"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg
                                   bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                                   border border-gray-300 dark:border-gray-600
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500
                                   transition-colors">
                        <x-heroicon-o-check class="w-4 h-4" aria-hidden="true" />
                        {{ __('notifications.mark_read') }}
                    </button>
                    <button type="button" wire:click="deleteSelected"
                        wire:confirm="{{ __('notifications.confirm_delete_selected') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg
                                   bg-danger-600 text-white
                                   hover:bg-danger-700
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-danger-500
                                   transition-colors">
                        <x-heroicon-o-trash class="w-4 h-4" aria-hidden="true" />
                        {{ __('notifications.delete') }}
                    </button>
                </div>
            </div>
        @endif

        {{-- Notifications Container with Live Region --}}
        <div role="log" aria-live="polite" aria-label="{{ __('notifications.list') }}" class="space-y-3">
            @if ($notifications->isEmpty())
                {{-- Empty State --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <x-heroicon-o-bell-slash class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4"
                        aria-hidden="true" />
                    <h2 class="text-xl font-heading font-semibold text-gray-900 dark:text-white mb-2">
                        {{ $hasActiveFilters ? __('notifications.no_results') : __('notifications.empty_title') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ $hasActiveFilters ? __('notifications.try_different_filters') : __('notifications.empty_message') }}
                    </p>
                    @if ($hasActiveFilters)
                        <button type="button" wire:click="clearFilters"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                       bg-primary-600 text-white hover:bg-primary-700
                                       focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500
                                       transition-colors">
                            {{ __('notifications.clear_filters') }}
                        </button>
                    @endif
                </div>
            @else
                {{-- Select All Header --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 px-4 py-3
                            flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="select-all" wire:click="toggleSelectAll"
                            @checked($selectAllVisible)
                            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            aria-label="{{ __('notifications.select_all') }}">
                        <label for="select-all" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('notifications.select_all_visible') }}
                        </label>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('notifications.sort_by') }}:
                        </span>
                        <select wire:model.live="sortBy"
                            class="text-sm bg-transparent border-0 text-gray-700 dark:text-gray-300 focus:ring-0 cursor-pointer"
                            aria-label="{{ __('notifications.sort_by') }}">
                            @foreach ($sortOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" wire:click="setSort('{{ $sortBy }}')"
                            class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded"
                            aria-label="{{ $sortDirection === 'asc' ? __('notifications.sort_desc') : __('notifications.sort_asc') }}">
                            @if ($sortDirection === 'asc')
                                <x-heroicon-o-arrow-up class="w-4 h-4" aria-hidden="true" />
                            @else
                                <x-heroicon-o-arrow-down class="w-4 h-4" aria-hidden="true" />
                            @endif
                        </button>
                    </div>
                </div>

                {{-- Notification List --}}
                @foreach ($notifications as $notification)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700
                               {{ !$notification->is_read ? 'border-l-4 border-l-primary-500' : '' }}
                               transition-all duration-200 hover:shadow-md"
                        wire:key="notification-{{ $notification->id }}">
                        <div class="p-4 flex gap-4">
                            {{-- Checkbox --}}
                            <div class="shrink-0 flex items-start pt-1">
                                <input type="checkbox" wire:click="toggleSelection('{{ $notification->id }}')"
                                    @checked(in_array($notification->id, $selectedIds))
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    aria-label="{{ __('notifications.select_notification', ['title' => $notification->title]) }}">
                            </div>

                            {{-- Icon --}}
                            <div class="shrink-0">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->iconBg }} shadow-sm">
                                    <x-dynamic-component :component="$notification->icon" class="w-5 h-5" aria-hidden="true" />
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h3
                                            class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                            {{ $notification->title }}
                                            @if (!$notification->is_read)
                                                <span class="inline-block w-2 h-2 bg-primary-600 rounded-full"
                                                    aria-label="{{ __('notifications.unread') }}"></span>
                                            @endif
                                        </h3>
                                        @if ($notification->message)
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ $notification->message }}
                                            </p>
                                        @endif
                                        <div
                                            class="mt-2 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-500">
                                            <span title="{{ $notification->created_at_full }}">
                                                {{ $notification->created_at }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                        bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                {{ $availableTypes[$notification->type] ?? ucfirst($notification->category) }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 shrink-0">
                                        @if ($notification->url)
                                            <a href="{{ $notification->url }}"
                                                class="inline-flex items-center justify-center min-h-10 min-w-10 rounded-lg
                                                      text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-700
                                                      focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500
                                                      transition-colors"
                                                aria-label="{{ __('notifications.view') }}"
                                                title="{{ __('notifications.view') }}" wire:navigate>
                                                <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5"
                                                    aria-hidden="true" />
                                            </a>
                                        @endif

                                        @if (!$notification->is_read)
                                            <button type="button" wire:click="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center justify-center min-h-10 min-w-10 rounded-lg
                                                           text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-700
                                                           focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500
                                                           transition-colors"
                                                aria-label="{{ __('notifications.mark_read') }}"
                                                title="{{ __('notifications.mark_read') }}">
                                                <x-heroicon-o-check class="w-5 h-5" aria-hidden="true" />
                                            </button>
                                        @endif

                                        <button type="button"
                                            wire:click="deleteNotification('{{ $notification->id }}')"
                                            wire:confirm="{{ __('notifications.confirm_delete') }}"
                                            class="inline-flex items-center justify-center min-h-10 min-w-10 rounded-lg
                                                       text-gray-400 dark:text-gray-500 hover:text-danger-600 dark:hover:text-danger-400 hover:bg-danger-50 dark:hover:bg-gray-700
                                                       focus:outline-none focus-visible:ring-2 focus-visible:ring-danger-500
                                                       transition-colors"
                                            aria-label="{{ __('notifications.delete') }}"
                                            title="{{ __('notifications.delete') }}">
                                            <x-heroicon-o-trash class="w-5 h-5" aria-hidden="true" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </main>

    {{-- Export Modal --}}
    <div x-show="showExportModal" x-cloak x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="export-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
                @click="showExportModal = false" aria-hidden="true"></div>

            {{-- Modal panel --}}
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                @keydown.escape.window="showExportModal = false">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 dark:bg-primary-900/50 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-arrow-down-tray class="h-6 w-6 text-primary-600 dark:text-primary-400"
                                aria-hidden="true" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                id="export-modal-title">
                                {{ __('notifications.export_title') }}
                            </h3>
                            <div class="mt-4">
                                <label for="export-format"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('notifications.export_format') }}
                                </label>
                                <select id="export-format" wire:model="exportFormat"
                                    class="block w-full px-3 py-2 text-sm
                                               bg-white dark:bg-gray-700
                                               border border-gray-300 dark:border-gray-600
                                               rounded-lg
                                               text-gray-900 dark:text-white
                                               focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="csv">CSV ({{ __('notifications.export_csv_desc') }})</option>
                                    <option value="json">JSON ({{ __('notifications.export_json_desc') }})</option>
                                </select>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('notifications.export_description', ['count' => $totalCount]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" wire:click="exportNotifications"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        {{ __('notifications.export_download') }}
                    </button>
                    <button type="button" wire:click="closeExportModal"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        {{ __('notifications.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Keyboard Navigation Script --}}
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Handle ARIA announcements
            Livewire.on('aria-announce', (data) => {
                const announcer = document.getElementById('notification-center-announcer');
                if (announcer) {
                    announcer.textContent = data.message;
                }
            });

            // Keyboard navigation for notification list
            document.addEventListener('keydown', (e) => {
                const notificationList = document.querySelector('[role="log"]');
                if (!notificationList) return;

                const notifications = notificationList.querySelectorAll('[wire\\:key^="notification-"]');
                const currentFocus = document.activeElement;
                const currentIndex = Array.from(notifications).findIndex(n => n.contains(currentFocus));

                if (e.key === 'ArrowDown' && currentIndex < notifications.length - 1) {
                    e.preventDefault();
                    const nextNotification = notifications[currentIndex + 1];
                    const focusable = nextNotification.querySelector('input, button, a');
                    if (focusable) focusable.focus();
                } else if (e.key === 'ArrowUp' && currentIndex > 0) {
                    e.preventDefault();
                    const prevNotification = notifications[currentIndex - 1];
                    const focusable = prevNotification.querySelector('input, button, a');
                    if (focusable) focusable.focus();
                }
            });
        });
    </script>
@endpush
