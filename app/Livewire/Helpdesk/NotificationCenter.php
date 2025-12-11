<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
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
    use OptimizedLivewireComponent;
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
        $user = Auth::user();
        assert($user !== null);

        $notification = $user
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->invalidateComponentCache();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        assert($user !== null);

        $user->unreadNotifications->markAsRead();
        $this->invalidateComponentCache();
        $this->dispatch('all-notifications-read');
    }

    public function deleteNotification(string $notificationId): void
    {
        $user = Auth::user();
        assert($user !== null);

        $user->notifications()
            ->where('id', $notificationId)
            ->delete();

        $this->invalidateComponentCache();
        $this->dispatch('notification-deleted');
    }

    #[Computed]
    public function unreadCount(): int
    {
        $user = Auth::user();
        assert($user !== null);

        $count = $this->getCachedComponentData('unread_count', function () use ($user) {
            return $user->unreadNotifications()->count();
        }, 30); // Cache for 30 seconds

        if (! is_int($count)) {
            throw new \UnexpectedValueException('Unread notification count must be an integer.');
        }

        return $count;
    }

    /**
     * @return Collection<int, DatabaseNotification>
     */
    #[Computed]
    public function notifications(): Collection
    {
        $cacheKey = 'notifications_'.$this->filter;

        /** @var Collection<int, DatabaseNotification> $notifications */
        $notifications = $this->getCachedComponentData($cacheKey, function () {
            $user = Auth::user();
            assert($user !== null);
            /** @var Builder<DatabaseNotification> $query */
            $query = $user->notifications();

            if ($this->filter === 'unread') {
                $query->whereNull('read_at');
            } elseif ($this->filter === 'read') {
                $query->whereNotNull('read_at');
            }

            return $query->latest()->limit(50)->get();
        }, 30); // Cache for 30 seconds

        return $notifications;
    }

    public function render(): \Illuminate\View\View: \Illuminate\View\View
    {
        return view('livewire.helpdesk.notification-center')->layout('layouts.portal');
    }
}
