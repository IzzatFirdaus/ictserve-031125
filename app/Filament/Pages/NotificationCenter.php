<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use UnitEnum;

/**
 * Notification Center
 *
 * Centralized notification management for admin users with real-time updates,
 * filtering, and notification preferences management.
 *
 * Requirements: 10.1, 10.3
 *
 * @see D03-FR-008.1 Notification management
 * @see D04 §8.1 Notification system
 */
class NotificationCenter extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    protected string $view = 'filament.pages.notification-center';

    protected static ?string $navigationLabel = null;

    protected static UnitEnum|string|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'notifications';

    public string $activeFilter = 'all';

    public array $notifications = [];

    public int $unreadCount = 0;

    public array $notificationStats = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public function mount(): void
    {
        $this->loadNotifications();
        $this->loadNotificationStats();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin_pages.notification_center.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin_pages.notification_center.group');
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_pages.notification_center.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_all_read')
                ->label('Mark All as Read')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('markAllAsRead')
                ->visible(fn () => $this->unreadCount > 0),

            Action::make('clear_all')
                ->label('Clear All')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear All Notifications')
                ->modalDescription('Are you sure you want to clear all notifications? This action cannot be undone.')
                ->action('clearAllNotifications'),

            Action::make('notification_preferences')
                ->label('Preferences')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url('/admin/notification-preferences')
                ->openUrlInNewTab(false),

            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action('loadNotifications')
                ->keyBindings(['ctrl+r', 'cmd+r']),
        ];
    }

    public function loadNotifications(): void
    {
        $user = auth()->user();

        // Get notifications from database_notifications table
        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderBy('created_at', 'desc');

        // Apply filter
        if ($this->activeFilter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->activeFilter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->limit(50)->get();

        $this->notifications = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);

            return [
                'id' => $notification->id,
                'type' => $this->getNotificationType($notification->type),
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'icon' => $this->getNotificationIcon($notification->type),
                'color' => $this->getNotificationColor($notification->type),
                'created_at' => \Carbon\Carbon::parse($notification->created_at),
                'read_at' => $notification->read_at ? \Carbon\Carbon::parse($notification->read_at) : null,
                'is_read' => ! is_null($notification->read_at),
                'action_url' => $data['action_url'] ?? null,
                'action_label' => $data['action_label'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
                'category' => $data['category'] ?? 'general',
                'metadata' => $data['metadata'] ?? [],
            ];
        })->toArray();

        $this->unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->count();
    }

    public function loadNotificationStats(): void
    {
        $user = auth()->user();

        $this->notificationStats = [
            'total' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->count(),
            'unread' => $this->unreadCount,
            'today' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereDate('created_at', today())
                ->count(),
            'this_week' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        $this->loadNotifications();
    }

    public function markAsRead(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Notification marked as read.')->success()->send();
    }

    public function markAsUnread(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['read_at' => null]);

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Notification marked as unread.')->success()->send();
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('All notifications marked as read.')->success()->send();
    }

    public function deleteNotification(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->delete();

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Notification deleted.')->success()->send();
    }

    public function clearAllNotifications(): void
    {
        $user = auth()->user();

        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->delete();

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('All notifications cleared.')->success()->send();
    }

    public function handleNotificationAction(string $notificationId, ?string $actionUrl = null): void
    {
        // Mark as read when action is taken
        $this->markAsRead($notificationId);

        if ($actionUrl) {
            $this->redirect($actionUrl);
        }
    }

    private function getNotificationType(string $type): string
    {
        $typeMap = [
            'App\\Notifications\\HelpdeskTicketAssigned' => 'Ticket Assigned',
            'App\\Notifications\\HelpdeskTicketStatusChanged' => 'Ticket Status Changed',
            'App\\Notifications\\LoanApplicationApproved' => 'Loan Approved',
            'App\\Notifications\\LoanApplicationRejected' => 'Loan Rejected',
            'App\\Notifications\\AssetOverdue' => 'Asset Overdue',
            'App\\Notifications\\SecurityIncident' => 'Security Alert',
            'App\\Notifications\\SystemMaintenance' => 'System Maintenance',
            'App\\Notifications\\SLABreach' => 'SLA Breach',
        ];

        return $typeMap[$type] ?? class_basename($type);
    }

    private function getNotificationIcon(string $type): string
    {
        $iconMap = [
            'App\\Notifications\\HelpdeskTicketAssigned' => 'heroicon-o-ticket',
            'App\\Notifications\\HelpdeskTicketStatusChanged' => 'heroicon-o-arrow-path',
            'App\\Notifications\\LoanApplicationApproved' => 'heroicon-o-check-circle',
            'App\\Notifications\\LoanApplicationRejected' => 'heroicon-o-x-circle',
            'App\\Notifications\\AssetOverdue' => 'heroicon-o-clock',
            'App\\Notifications\\SecurityIncident' => 'heroicon-o-shield-exclamation',
            'App\\Notifications\\SystemMaintenance' => 'heroicon-o-wrench',
            'App\\Notifications\\SLABreach' => 'heroicon-o-exclamation-triangle',
        ];

        return $iconMap[$type] ?? 'heroicon-o-bell';
    }

    private function getNotificationColor(string $type): string
    {
        $colorMap = [
            'App\\Notifications\\HelpdeskTicketAssigned' => 'info',
            'App\\Notifications\\HelpdeskTicketStatusChanged' => 'warning',
            'App\\Notifications\\LoanApplicationApproved' => 'success',
            'App\\Notifications\\LoanApplicationRejected' => 'danger',
            'App\\Notifications\\AssetOverdue' => 'warning',
            'App\\Notifications\\SecurityIncident' => 'danger',
            'App\\Notifications\\SystemMaintenance' => 'info',
            'App\\Notifications\\SLABreach' => 'danger',
        ];

        return $colorMap[$type] ?? 'gray';
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        if (! $user || ! $user->hasAnyRole(['admin', 'superuser'])) {
            return null;
        }

        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->count();

        return $unreadCount > 0 ? (string) $unreadCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unreadCount = (int) static::getNavigationBadge();

        return match (true) {
            $unreadCount > 10 => 'danger',
            $unreadCount > 5 => 'warning',
            $unreadCount > 0 => 'info',
            default => null,
        };
    }

    protected function getViewData(): array
    {
        return [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
            'notificationStats' => $this->notificationStats,
            'activeFilter' => $this->activeFilter,
        ];
    }
}
