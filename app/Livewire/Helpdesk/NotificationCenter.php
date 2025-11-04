<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Notification Center Component
 *
 * Displays user notifications with filtering (all/unread/read),
 * mark-as-read functionality, and real-time updates via Laravel Echo.
 *
 * @trace Requirement 7.5
 *
 * @wcag WCAG 2.2 AA compliant with proper ARIA labels
 */
class NotificationCenter extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public bool $showDropdown = false;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);
    }

    #[On('echo:notifications.{userId},NotificationSent')]
    public function notificationReceived(): void
    {
        $this->dispatch('notification-received');
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('all-notifications-read');
    }

    public function deleteNotification(string $notificationId): void
    {
        Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->delete();

        $this->dispatch('notification-deleted');
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadNotifications()->count();
    }

    #[Computed]
    public function notifications(): Collection
    {
        $query = Auth::user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->latest()->limit(50)->get();
    }

    public function render()
    {
        return view('livewire.helpdesk.notification-center');
    }
}
