<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
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

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'notifications';

    public string $activeFilter = 'all';

    /** @var array<int, mixed> */
    public array $notifications = [];

    public int $unreadCount = 0;

    /** @var array<string, int> */
    public array $notificationStats = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['admin', 'superuser']) ?? false;
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
        return __('filament.navigation.system');
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin_pages.notification_center.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_all_read')
                ->label('Tandai Semua Dibaca')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('markAllAsRead')
                ->visible(fn () => $this->unreadCount > 0),

            Action::make('clear_all')
                ->label('Kosongkan Semua')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Kosongkan Semua Pemberitahuan')
                ->modalDescription('Adakah anda pasti mahu mengosongkan semua pemberitahuan? Tindakan ini tidak boleh dibuat asal.')
                ->action('clearAllNotifications'),

            Action::make('notification_preferences')
                ->label('Keutamaan')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url('/admin/notification-preferences')
                ->openUrlInNewTab(false),

            Action::make('refresh')
                ->label('Muat Semula')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action('loadNotifications')
                ->keyBindings(['ctrl+r', 'cmd+r']),
        ];
    }

    public function loadNotifications(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->notifications = [];
            $this->unreadCount = 0;

            return;
        }

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
            $data = is_array($data) ? $data : [];

            return [
                'id' => $notification->id,
                'type' => $this->getNotificationType($notification->type),
                'title' => $data['title'] ?? 'Pemberitahuan',
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
        $user = Auth::user();

        if (! $user) {
            $this->notificationStats = [
                'total' => 0,
                'unread' => 0,
                'today' => 0,
                'this_week' => 0,
            ];

            return;
        }

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

        Notification::make()->title('Pemberitahuan ditandai sebagai dibaca.')->success()->send();
    }

    public function markAsUnread(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->update(['read_at' => null]);

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Pemberitahuan ditandai sebagai belum dibaca.')->success()->send();
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Semua pemberitahuan ditandai sebagai dibaca.')->success()->send();
    }

    public function deleteNotification(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->delete();

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Pemberitahuan dipadam.')->success()->send();
    }

    public function clearAllNotifications(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->delete();

        $this->loadNotifications();
        $this->loadNotificationStats();

        Notification::make()->title('Semua pemberitahuan dikosongkan.')->success()->send();
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
            'App\\Notifications\\HelpdeskTicketAssigned' => 'Tiket Ditugaskan',
            'App\\Notifications\\HelpdeskTicketStatusChanged' => 'Status Tiket Berubah',
            'App\\Notifications\\LoanApplicationApproved' => 'Pinjaman Diluluskan',
            'App\\Notifications\\LoanApplicationRejected' => 'Pinjaman Ditolak',
            'App\\Notifications\\AssetOverdue' => 'Aset Tertunggak',
            'App\\Notifications\\SecurityIncident' => 'Amaran Keselamatan',
            'App\\Notifications\\SystemMaintenance' => 'Penyelenggaraan Sistem',
            'App\\Notifications\\SLABreach' => 'Pelanggaran SLA',
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
        $user = Auth::user();

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
