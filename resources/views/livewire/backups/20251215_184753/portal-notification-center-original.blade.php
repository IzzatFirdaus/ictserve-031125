{{--
/**
 * Component: Portal Notification Center
 * Description: WCAG 2.2 AA compliant full notification center with filters & pagination
 * @author Pasukan BPM MOTAC
 * @trace D03 SRS-FR-006; D04 §3.5; D11 §9
 * @trace Requirements 10.2, 10.4 - Notification center with filtering and ARIA accessibility
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages, SC 2.4.6 Headings and Labels)
 * @version 2.0.0
 * @updated 2025-12-14
 */
--}}

<div class="space-y-6">
    {{-- Screen Reader Announcements --}}
    <div class="sr-only" aria-live="assertive" aria-atomic="true">
        @if($filter === 'unread' && $notifications->count() > 0)
            {{ __('Anda mempunyai :count notifikasi baharu', ['count' => $notifications->count()]) }}
        @endif
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Pusat Pemberitahuan') }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Urus dan lihat semua pemberitahuan anda') }}
            </p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <nav class="flex items-center gap-2 flex-wrap" role="tablist" aria-label="{{ __('Tapis pemberitahuan') }}">
        <button type="button" wire:click="filterBy('all')" role="tab"
            aria-selected="{{ $filter === 'all' ? 'true' : 'false' }}" aria-controls="notification-list"
            @class([
                'min-h-11 min-w-11 px-4 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 dark:focus:ring-offset-gray-900',
                'bg-primary-600 text-white shadow-button' => $filter === 'all',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' =>
                    $filter !== 'all',
            ])>
            {{ __('Semua') }}
        </button>
        <button type="button" wire:click="filterBy('unread')" role="tab"
            aria-selected="{{ $filter === 'unread' ? 'true' : 'false' }}" aria-controls="notification-list"
            @class([
                'min-h-11 min-w-11 px-4 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 dark:focus:ring-offset-gray-900',
                'bg-primary-600 text-white shadow-button' => $filter === 'unread',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' =>
                    $filter !== 'unread',
            ])>
            {{ __('Belum Dibaca') }}
        </button>
        <button type="button" wire:click="filterBy('read')" role="tab"
            aria-selected="{{ $filter === 'read' ? 'true' : 'false' }}" aria-controls="notification-list"
            @class([
                'min-h-11 min-w-11 px-4 py-2.5 text-sm font-medium rounded-md transition-colors duration-200 focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 dark:focus:ring-offset-gray-900',
                'bg-primary-600 text-white shadow-button' => $filter === 'read',
                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' =>
                    $filter !== 'read',
            ])>
            {{ __('Sudah Dibaca') }}
        </button>
    </nav>

    {{-- Notification List --}}
    <div id="notification-list" role="log" aria-live="polite" aria-label="{{ __('Senarai pemberitahuan') }}"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-card theme-transition border border-gray-200 dark:border-gray-700 divide-y divide-gray-200 dark:divide-gray-700">

        @forelse($notifications as $n)
            <article class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                wire:key="notification-{{ $n->id }}" aria-labelledby="notification-title-{{ $n->id }}">
                <div class="flex items-start justify-between gap-4">
                    {{-- Notification Content --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        {{-- Type Badge --}}
                        <div class="flex items-center gap-2">
                            <span id="notification-title-{{ $n->id }}"
                                class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $labelForType($n->type) }}
                            </span>
                            @if ($n->read_at)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-success-100 dark:bg-success-900/50 text-success-800 dark:text-success-200">
                                    {{ __('Dibaca') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-warning-100 dark:bg-warning-900/50 text-warning-800 dark:text-warning-200">
                                    {{ __('Belum Dibaca') }}
                                </span>
                            @endif
                        </div>

                        {{-- Message --}}
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $n->data['message'] ?? __('Tiada mesej') }}
                        </p>

                        {{-- Timestamp --}}
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <time datetime="{{ $n->created_at?->toISOString() }}">
                                {{ $n->created_at?->diffForHumans() ?? '' }}
                            </time>
                        </p>

                        {{-- Quick Action Links --}}
                        <div class="flex items-center gap-3 pt-1">
                            @if (isset($n->data['ticket_id']))
                                <a href="{{ Route::has('helpdesk.show') ? route('helpdesk.show', $n->data['ticket_id']) : '#' }}"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('View Ticket') }}
                                </a>
                            @elseif(isset($n->data['loan_id']))
                                <a href="{{ Route::has('loans.show') ? route('loans.show', $n->data['loan_id']) : '#' }}"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('View Loan') }}
                                </a>
                            @elseif(isset($n->data['asset_id']))
                                <a href="{{ Route::has('assets.show') ? route('assets.show', $n->data['asset_id']) : '#' }}"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 focus:outline-none focus:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ __('Lihat Aset') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col gap-2 items-end shrink-0">
                        @if (!$n->read_at)
                            <button type="button" wire:click="markAsRead('{{ $n->id }}')"
                                class="min-h-11 min-w-11 px-3 py-2 text-xs font-medium text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-md focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:outline-none outline-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-150"
                                aria-label="{{ __('Tandakan sebagai dibaca') }}">
                                {{ __('Tandakan Dibaca') }}
                            </button>
                        @endif
                        <button type="button" wire:click="deleteNotification('{{ $n->id }}')"
                            wire:confirm="{{ __('Adakah anda pasti mahu memadam pemberitahuan ini?') }}"
                            class="min-h-11 min-w-11 px-3 py-1 text-xs font-medium text-danger-600 dark:text-danger-400 hover:text-danger-800 dark:hover:text-danger-300 hover:bg-danger-50 dark:hover:bg-danger-900/20 rounded-md focus:outline-none focus:ring-2 focus:ring-danger-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-150"
                            aria-label="{{ __('Padam pemberitahuan') }}">
                            {{ __('Padam') }}
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Tiada pemberitahuan') }}
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Anda tidak mempunyai sebarang pemberitahuan pada masa ini.') }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($notifications?->hasPages())
        <nav aria-label="{{ __('Navigasi halaman pemberitahuan') }}" class="mt-6">
            {{ $notifications->links() }}
        </nav>
    @endif

    {{-- ARIA Live Region for Screen Reader Announcements --}}
    <div class="sr-only" role="status" aria-live="polite" aria-atomic="true">
        {{ $notifications?->total() ?? 0 }} {{ __('pemberitahuan dijumpai') }}
    </div>
</div>
