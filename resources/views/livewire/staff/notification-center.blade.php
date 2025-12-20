<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Volt\Component;

new class extends Component {
    #[Reactive]
    public bool $showAll = false;
    
    #[Reactive]
    public string $filter = 'all';
    
    /**
     * Get user notifications with caching.
     */
    #[Computed(persist: true, cache: true)]
    public function notifications()
    {
        $query = auth()->user()->notifications();
        
        if (!$this->showAll) {
            $query->limit(5);
        }
        
        if ($this->filter !== 'all') {
            $query->where('type', $this->filter);
        }
        
        return $query->latest()->get();
    }
    
    /**
     * Mark notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        auth()->user()->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
            
        $this->dispatch('notification-read');
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('all-notifications-read');
    }
    
    /**
     * Toggle show all notifications.
     */
    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;
    }
}; ?>

<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
            {{ __('staff.notifications.title') }}
        </h2>
        <div class="flex items-center gap-2">
            <select wire:model.live="filter" class="text-sm border-slate-300 dark:border-slate-600 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 min-h-11">
                <option value="all">{{ __('staff.notifications.filter.all') }}</option>
                <option value="ticket">{{ __('staff.notifications.filter.tickets') }}</option>
                <option value="loan">{{ __('staff.notifications.filter.loans') }}</option>
            </select>
            @if(count($this->notifications) > 0)
                <button wire:click="markAllAsRead" 
                        class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 px-2 py-1 min-h-11">
                    {{ __('staff.notifications.mark_all_read') }}
                </button>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="space-y-3">
        @forelse($this->notifications as $notification)
            <div wire:key="notification-{{ $notification->id }}" 
                 class="flex items-start p-3 rounded-lg {{ $notification->read_at ? 'bg-slate-50 dark:bg-slate-700' : 'bg-primary-50 dark:bg-primary-900/20' }} border border-slate-200 dark:border-slate-600">
                <div class="flex-1">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                        {{ $notification->data['title'] ?? 'Notification' }}
                    </p>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                        {{ $notification->data['message'] ?? '' }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-500 mt-2">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                @if(!$notification->read_at)
                    <button wire:click="markAsRead('{{ $notification->id }}')"
                            class="ml-2 text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 p-1 min-h-11 min-w-11 flex items-center justify-center"
                            aria-label="{{ __('staff.notifications.mark_as_read') }}">
                        <x-heroicon-o-check class="h-4 w-4" />
                    </button>
                @endif
            </div>
        @empty
            <div class="text-center py-8">
                <x-heroicon-o-bell-slash class="h-12 w-12 text-slate-400 mx-auto mb-4" />
                <p class="text-slate-500 dark:text-slate-400">
                    {{ __('staff.notifications.empty') }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Show More Button --}}
    @if(!$this->showAll && count($this->notifications) >= 5)
        <div class="mt-4 text-center">
            <button wire:click="toggleShowAll" 
                    class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400 rounded-lg focus:outline-none focus-visible:ring-3 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-800 px-3 py-2 min-h-11">
                {{ __('staff.notifications.show_all') }}
            </button>
        </div>
    @endif
</div>
