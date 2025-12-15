<?php

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Volt\Component;

use function Livewire\Volt\computed;

new class extends Component
{
    public int $page = 1;

    public int $perPage = 10;

    #[Computed]
    public function notifications()
    {
        return auth()->user()
            ->notifications()
            ->latest()
            ->paginate($this->perPage, page: $this->page);
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications->count();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = DatabaseNotification::findOrFail($notificationId);

        if ($notification->notifiable_id === auth()->id()) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('all-notifications-read');
    }

    public function deleteNotification(string $notificationId): void
    {
        $notification = DatabaseNotification::findOrFail($notificationId);

        if ($notification->notifiable_id === auth()->id()) {
            $notification->delete();
            $this->dispatch('notification-deleted');
        }
    }

    public function clearAll(): void
    {
        auth()->user()->notifications()->delete();
        $this->dispatch('all-notifications-cleared');
    }

    public function loadMore(): void
    {
        $this->page++;
    }
};

?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6 px-4 sm:px-6 lg:px-8 theme-transition">
    <main id="main-content" class="max-w-4xl mx-auto" tabindex="-1">
        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-heading font-semibold text-gray-900 dark:text-white">
                    {{ __('notifications.title') }}
                </h1>
                @if($this->unreadCount > 0)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('notifications.unread_count', ['count' => $this->unreadCount]) }}
                    </p>
                @endif
            </div>

            @if($this->unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="btn-secondary min-h-11 px-4 py-2 rounded-md shadow-button
                           bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                           hover:bg-gray-300 dark:hover:bg-gray-600
                           focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                           transition-colors duration-200 text-sm font-medium">
                    {{ __('notifications.mark_all_read') }}
                </button>
            @endif
        </div>

        <!-- Notifications Container with Live Region -->
        <div role="log"
             aria-live="polite"
             aria-label="{{ __('notifications.list') }}"
             class="space-y-4">
            <!-- Hidden announcement for screen readers -->
            <div class="sr-only" aria-live="assertive" aria-atomic="true">
                @if($this->unreadCount > 0)
                    {{ __('notifications.new_count_announcement', ['count' => $this->unreadCount]) }}
                @endif
            </div>

            @if($this->notifications->isEmpty())
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-12 text-center">
                    <x-heroicon-o-bell-slash class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" aria-hidden="true" />
                    <h2 class="text-xl font-heading font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __('notifications.empty_title') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('notifications.empty_message') }}
                    </p>
                </div>
            @else
                <!-- Notification List -->
                @foreach($this->notifications as $notification)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-card p-6
                               {{ $notification->read_at ? 'border-l-4 border-gray-300 dark:border-gray-600' : 'border-l-4 border-primary-500 bg-primary-50 dark:bg-gray-800' }}
                               transition-colors duration-200"
                         wire:key="notification-{{ $notification->id }}">
                        <div class="flex gap-4">
                            <!-- Icon -->
                            <div class="shrink-0">
                                @if(Str::contains($notification->type, 'Ticket'))
                                    <x-heroicon-o-ticket class="w-6 h-6 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                                @elseif(Str::contains($notification->type, 'Approval'))
                                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" aria-hidden="true" />
                                @elseif(Str::contains($notification->type, 'Loan'))
                                    <x-heroicon-o-inbox-in class="w-6 h-6 text-purple-600 dark:text-purple-400" aria-hidden="true" />
                                @else
                                    <x-heroicon-o-bell class="w-6 h-6 text-gray-600 dark:text-gray-400" aria-hidden="true" />
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $notification->data['title'] ?? __('notifications.default_title') }}
                                            @if(!$notification->read_at)
                                                <span class="inline-block w-2 h-2 bg-primary-600 rounded-full ml-2"
                                                      aria-label="{{ __('notifications.unread') }}"></span>
                                            @endif
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $notification->data['message'] ?? __('notifications.default_message') }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 shrink-0">
                                        @if(!$notification->read_at)
                                            <button
                                                type="button"
                                                wire:click="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center justify-center min-h-11 min-w-11 rounded-md
                                                       text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-700
                                                       focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none
                                                       transition-colors duration-200"
                                                aria-label="{{ __('notifications.mark_read') }}"
                                                title="{{ __('notifications.mark_read') }}">
                                                <x-heroicon-o-check class="w-5 h-5" aria-hidden="true" />
                                            </button>
                                        @endif

                                        <button
                                            type="button"
                                            wire:click="deleteNotification('{{ $notification->id }}')"
                                            class="inline-flex items-center justify-center min-h-11 min-w-11 rounded-md
                                                   text-gray-400 dark:text-gray-500 hover:text-danger-600 dark:hover:text-danger-400 hover:bg-danger-50 dark:hover:bg-gray-700
                                                   focus-visible:ring-2 focus-visible:ring-danger-500 focus-visible:outline-none
                                                   transition-colors duration-200"
                                            aria-label="{{ __('notifications.delete') }}"
                                            title="{{ __('notifications.delete') }}">
                                            <x-heroicon-o-trash class="w-5 h-5" aria-hidden="true" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Notification Action Link (if available) -->
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}"
                                       class="mt-3 inline-flex items-center text-sm font-medium text-primary-600 dark:text-primary-400
                                              hover:text-primary-700 dark:hover:text-primary-300
                                              focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:outline-none rounded px-2 py-1"
                                       wire:navigate>
                                        {{ $notification->data['action_text'] ?? __('notifications.view') }}
                                        <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" aria-hidden="true" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($this->notifications->hasMorePages())
                    <div class="text-center mt-6">
                        <button
                            type="button"
                            wire:click="loadMore"
                            class="btn-secondary min-h-11 px-6 py-2 rounded-md shadow-button
                                   bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white
                                   hover:bg-gray-300 dark:hover:bg-gray-600
                                   focus-visible:ring-3 focus-visible:ring-gray-500 focus-visible:outline-none
                                   transition-colors duration-200 font-medium">
                            {{ __('notifications.load_more') }}
                        </button>
                    </div>
                @endif

                <!-- Clear All Button -->
                @if(!$this->notifications->isEmpty())
                    <div class="mt-8 text-center">
                        <button
                            type="button"
                            wire:click="clearAll"
                            wire:confirm="{{ __('notifications.confirm_clear_all') }}"
                            class="text-danger-600 dark:text-danger-400 hover:text-danger-700 dark:hover:text-danger-300
                                   focus-visible:ring-2 focus-visible:ring-danger-500 focus-visible:outline-none rounded px-3 py-2
                                   text-sm font-medium transition-colors duration-200">
                            {{ __('notifications.clear_all') }}
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </main>
</div>
