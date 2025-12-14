<?php

declare(strict_types=1);

/**
 * NotificationBell Component
 *
 * Notification bell icon with real-time unread count and dropdown.
 * Integrates with Laravel Reverb WebSocket for real-time updates.
 *
 * Features:
 * - Unread count badge with --bg-danger token per D14 §4.1.1
 * - Laravel Reverb WebSocket integration per D12 §2
 * - Heroicon bell icon (w-5 h-5) per D14 §8.1
 * - Categorized notifications (Tickets, Loans, System) per D12 §6.4
 * - Mark-as-read functionality
 * - ARIA live regions for screenreader announcements per D14 §10.4
 *
 * @see D03 SRS-FR-008
 * @see D04 §5.3
 * @see D12 §2 Real-time features
 * @see D12 §4 Requirements 6.2, 6.3
 * @see D14 §4.1.1 Danger token
 * @see D14 §8.1 Heroicons
 *
 * @requirements 15.1-15.5 Real-time notification UI
 *
 * @version 2.0.0
 *
 * @updated 2025-12-05
 */

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    /**
     * Unread notification count.
     */
    public int $unreadCount = 0;

    /**
     * Recent notifications (limited to 10).
     *
     * @var array<int, array<string, mixed>>
     */
    public array $recentNotifications = [];

    /**
     * Notifications grouped by category.
     *
     * @var array<string, array<int, array<string, mixed>>>
     */
    public array $categorizedNotifications = [];

    /**
     * Indicates whether dropdown is open.
     */
    public bool $showDropdown = false;

    /**
     * Active category filter.
     */
    public string $activeCategory = 'all';

    /**
     * Available notification categories per D12 §6.4.
     *
     * @var array<string, string>
     */
    public array $categories = [
        'all' => 'All',
        'tickets' => 'Tickets',
        'loans' => 'Loans',
        'system' => 'System',
    ];

    /**
     * Mount component and load notifications.
     */
    public function mount(): void
    {
        $this->loadNotifications();
    }

    /**
     * Get Echo listeners for real-time updates via Laravel Reverb.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return [];
        }

        return [
            "echo-private:App.Models.User.{$user->id},notification" => 'handleNewNotification',
            "echo-private:user.{$user->id},.ticket.status.changed" => 'handleTicketStatusChanged',
            "echo-private:user.{$user->id},.loan.status.changed" => 'handleLoanStatusChanged',
            "echo-private:user.{$user->id},.notification.created" => 'handleNewNotification',
            "echo-private:user.{$user->id},.status.updated" => 'handleStatusUpdated',
            'refresh-notifications' => 'loadNotifications',
        ];
    }

    /**
     * Handle ticket status change from Laravel Reverb broadcast.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:ticket.status.changed')]
    public function handleTicketStatusChanged(array $event): void
    {
        $this->loadNotifications();

        $message = $event['message'] ?? __('notifications.ticket_updated');
        $this->dispatch('toast', message: $message, type: 'info');
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle loan status change from Laravel Reverb broadcast.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:loan.status.changed')]
    public function handleLoanStatusChanged(array $event): void
    {
        $this->loadNotifications();

        $message = $event['message'] ?? __('notifications.loan_updated');
        $this->dispatch('toast', message: $message, type: 'info');
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle generic status update from Laravel Reverb broadcast.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:status.updated')]
    public function handleStatusUpdated(array $event): void
    {
        $this->loadNotifications();

        $modelType = $event['model_type'] ?? 'Item';
        $newStatus = $event['new_status'] ?? 'updated';
        $message = __('notifications.status_updated', ['type' => $modelType, 'status' => $newStatus]);

        $this->dispatch('toast', message: $message, type: 'info');
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle new notification from Laravel Reverb broadcast.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:notification')]
    public function handleNewNotification(array $event): void
    {
        // Increment count optimistically
        $this->unreadCount++;

        // Reload notifications to get the new one
        $this->loadNotifications();

        // Dispatch toast notification for user feedback
        $data = $event['notification'] ?? $event;

        if (! is_array($data)) {
            $data = (array) $data;
        }

        /** @var array<string, mixed> $data */
        $rawTitle = $data['title'] ?? null;
        $title = is_scalar($rawTitle) ? (string) $rawTitle : (string) __('notifications.new_notification');

        $rawType = $data['type'] ?? 'general';
        $typeStr = is_scalar($rawType) ? (string) $rawType : 'general';
        $type = $this->mapNotificationType($typeStr);

        $this->dispatch('toast', message: $title, type: $type === 'system' ? 'info' : 'success');

        // Announce to screen readers via ARIA live region
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle email verification event from WP-08.
     *
     * Triggered when user completes email verification.
     * Updates bell to reflect account status change.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo:email-verified')]
    public function handleEmailVerified(array $event): void
    {
        $this->dispatch('toast', message: (string) __('notifications.email_verified'), type: 'success');
        $this->loadNotifications();
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle account linking event from WP-08.
     *
     * Triggered when guest submissions are linked to authenticated account.
     * Updates bell to show linking completion.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo:account-linked')]
    public function handleAccountLinked(array $event): void
    {
        $rawCount = $event['linked_submissions'] ?? 0;
        $linkedCount = is_numeric($rawCount) ? (int) $rawCount : 0;

        $message = sprintf(
            (string) __('notifications.submissions_linked'),
            $linkedCount
        );

        $this->dispatch('toast', message: $message, type: 'success');
        $this->loadNotifications();
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle API token creation event from WP-08.
     *
     * Triggered when new API token is created via Filament.
     * Updates bell to show token creation.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo:api-token-created')]
    public function handleApiTokenCreated(array $event): void
    {
        $rawName = $event['token_name'] ?? 'Unknown';
        $tokenName = is_scalar($rawName) ? (string) $rawName : 'Unknown';
        $message = sprintf((string) __('notifications.api_token_created'), $tokenName);

        $this->dispatch('toast', message: $message, type: 'success');
        $this->loadNotifications();
        $this->dispatch('notification-received', count: $this->unreadCount);
    }

    /**
     * Handle Google SSO linking event from WP-08.
     *
     * Triggered when user links Google account for SSO.
     * Updates bell to show SSO linking.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo:google-sso-linked')]
    public function handleGoogleSsoLinked(array $event): void
    {
        $rawEmail = $event['google_email'] ?? (string) __('notifications.google_account');
        $googleEmail = is_scalar($rawEmail) ? (string) $rawEmail : '';

        $message = sprintf((string) __('notifications.google_sso_linked'), $googleEmail);

        $this->dispatch('toast', message: $message, type: 'success');
        $this->loadNotifications();
        $this->dispatch('notification-received', count: $this->unreadCount);
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

        // Get 10 most recent unread notifications
        $notifications = DB::table('notifications')
            ->where('notifiable_id', $notifiableId)
            ->where('notifiable_type', $notifiableType)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification): array {
                $data = (array) json_decode($notification->data, true);
                $type = isset($data['type']) && is_scalar($data['type']) ? (string) $data['type'] : 'general';
                $category = $this->mapNotificationType($type);

                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'category' => $category,
                    'title' => isset($data['title']) && is_scalar($data['title']) ? (string) $data['title'] : (string) __('notifications.untitled'),
                    'message' => isset($data['message']) ? $data['message'] : '',
                    'created_at' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                    'created_at_raw' => $notification->created_at,
                    'url' => isset($data['url']) ? $data['url'] : null,
                    'icon' => (string) $this->getIconForType($type),
                    'iconBg' => (string) $this->getIconBgForType($type),
                ];
            })
            ->toArray();

        /** @var array<int, array<string, mixed>> $notifications */
        $this->recentNotifications = $notifications;

        // Group by category
        // Cast to array to ensure type safety.
        // PHPStan complains because groupBy returns collection of collections, which toArray converts recursively.
        $categorized = collect($notifications)
            ->groupBy('category')
            ->map(fn ($group) => $group->toArray())
            ->toArray();

        /** @var array<string, array<int, array<string, mixed>>> $categorized */
        $this->categorizedNotifications = $categorized;
    }

    /**
     * Map notification type to category.
     */
    protected function mapNotificationType(string $type): string
    {
        return match (true) {
            str_contains($type, 'ticket') => 'tickets',
            str_contains($type, 'loan'), str_contains($type, 'approval') => 'loans',
            default => 'system',
        };
    }

    /**
     * Get Heroicon name for notification type per D14 §8.1.
     */
    protected function getIconForType(string $type): string
    {
        return match (true) {
            str_contains($type, 'ticket') => 'heroicon-o-ticket',
            str_contains($type, 'loan') => 'heroicon-o-clipboard-document-check',
            str_contains($type, 'approval') => 'heroicon-o-check-badge',
            str_contains($type, 'warning'), str_contains($type, 'sla') => 'heroicon-o-exclamation-triangle',
            str_contains($type, 'error') => 'heroicon-o-x-circle',
            default => 'heroicon-o-bell',
        };
    }

    /**
     * Get icon background color class for notification type.
     */
    protected function getIconBgForType(string $type): string
    {
        return match (true) {
            str_contains($type, 'ticket') => 'bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400',
            str_contains($type, 'loan'), str_contains($type, 'approval') => 'bg-success-100 dark:bg-success-900/50 text-success-600 dark:text-success-400',
            str_contains($type, 'warning'), str_contains($type, 'sla') => 'bg-warning-100 dark:bg-warning-900/50 text-warning-600 dark:text-warning-400',
            str_contains($type, 'error') => 'bg-danger-100 dark:bg-danger-900/50 text-danger-600 dark:text-danger-400',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
        };
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
     * Set active category filter.
     */
    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
    }

    /**
     * Get filtered notifications based on active category.
     *
     * @return array<int, mixed>
     */
    public function getFilteredNotifications(): array
    {
        if ($this->activeCategory === 'all') {
            return $this->recentNotifications;
        }

        return array_filter(
            $this->recentNotifications,
            function ($n) {
                return isset($n['category']) && $n['category'] === $this->activeCategory;
            }
        );
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
        $this->dispatch('toast', message: __('notifications.all_marked_read'), type: 'success');
    }

    /**
     * Refresh notifications (called by real-time events or polling).
     */
    #[On('refresh-notifications')]
    public function refreshNotifications(): void
    {
        $this->loadNotifications();
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.notification-bell', [
            'filteredNotifications' => $this->getFilteredNotifications(),
        ]);
    }
}
