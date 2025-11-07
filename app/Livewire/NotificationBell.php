<?php

declare(strict_types=1);

// name: NotificationBell
// description: Notification bell icon with real-time unread count and dropdown
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-008, D04 §5.3, D12 §4 (Requirements 6.2, 6.3)
// last-updated: 2025-11-06

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class NotificationBell extends Component
{
    /**
     * Unread notification count.
     */
    public int $unreadCount = 0;

    /**
     * Recent notifications (limited to 5).
     */
    public array $recentNotifications = [];

    /**
     * Indicates whether dropdown is open.
     */
    public bool $showDropdown = false;

    /**
     * Mount component and load notifications.
     */
    public function mount(): void
    {
        $this->loadNotifications();
    }

    /**
     * Load unread notifications and recent items.
     */
    public function loadNotifications(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $notifiableId = $user->id;
        $notifiableType = $user->getMorphClass();

        // Get unread count
        $this->unreadCount = DB::table('notifications')
            ->where('notifiable_id', $notifiableId)
            ->where('notifiable_type', $notifiableType)
            ->whereNull('read_at')
            ->count();

        // Get 5 most recent unread notifications
        $this->recentNotifications = DB::table('notifications')
            ->where('notifiable_id', $notifiableId)
            ->where('notifiable_type', $notifiableType)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);

                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'general',
                    'title' => $data['title'] ?? __('notifications.untitled'),
                    'message' => $data['message'] ?? '',
                    'created_at' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                    'url' => $data['url'] ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Toggle dropdown visibility.
     */
    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;

        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->dispatch('notification-read', notificationId: $notificationId);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', $user->getMorphClass())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->dispatch('all-notifications-read');
    }

    /**
     * Refresh notifications (called by real-time events).
     */
    public function refreshNotifications(): void
    {
        $this->loadNotifications();
    }

    /**
     * Handle new notification from Echo broadcast.
     */
    public function handleEchoNotification(array $event): void
    {
        $this->loadNotifications();
        $this->showDropdown = true;
    }

    /**
     * Get event listeners for Echo integration.
     *
     * @return array<string, string>
     */
    protected function getListeners(): array
    {
        return [
            'echo:notification-created' => 'handleEchoNotification',
        ];
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.notification-bell');
    }
}
