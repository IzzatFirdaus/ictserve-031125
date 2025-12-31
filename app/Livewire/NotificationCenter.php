<?php

declare(strict_types=1);

// name: NotificationCenter
// description: Full notification history with pagination, filtering, bulk actions, search, and export
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-008, D04 §5.3, D12 §4 (Requirements 3.7, 3.8, 6.3, 6.4, 6.5, 7.1, 7.2)
// last-updated: 2025-12-30

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * NotificationCenter Component
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
 * @version 3.0.0
 *
 * @updated 2025-12-30
 */
class NotificationCenter extends Component
{
    use WithPagination;

    /**
     * Search query for filtering notifications.
     */
    #[Url(as: 'q')]
    public string $searchQuery = '';

    /**
     * Filter: show only unread notifications.
     */
    #[Url(as: 'unread')]
    public bool $unreadOnly = false;

    /**
     * Filter: notification type.
     */
    #[Url(as: 'type')]
    public ?string $typeFilter = null;

    /**
     * Filter: date range start.
     */
    #[Url(as: 'from')]
    public ?string $dateFrom = null;

    /**
     * Filter: date range end.
     */
    #[Url(as: 'to')]
    public ?string $dateTo = null;

    /**
     * Sort field.
     */
    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';

    /**
     * Sort direction.
     */
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    /**
     * Selected notification IDs for bulk actions.
     *
     * @var array<int, string>
     */
    public array $selectedIds = [];

    /**
     * Select all visible notifications flag.
     */
    public bool $selectAllVisible = false;

    /**
     * Items per page.
     */
    public int $perPage = 15;

    /**
     * Show export modal.
     */
    public bool $showExportModal = false;

    /**
     * Export format (csv, json).
     */
    public string $exportFormat = 'csv';

    /**
     * Available notification types.
     *
     * @var array<string, string>
     */
    public array $availableTypes = [
        'ticket_status' => 'Helpdesk Ticket Status',
        'ticket_assigned' => 'Ticket Assigned',
        'ticket_resolved' => 'Ticket Resolved',
        'loan_approval' => 'Loan Approval',
        'loan_status' => 'Loan Status Update',
        'loan_rejected' => 'Loan Rejected',
        'overdue_reminder' => 'Overdue Reminder',
        'system_announcement' => 'System Announcement',
        'sla_breach' => 'SLA Breach Warning',
    ];

    /**
     * Available sort options.
     *
     * @var array<string, string>
     */
    public array $sortOptions = [
        'created_at' => 'Date Created',
        'read_at' => 'Read Status',
        'type' => 'Type',
    ];

    /**
     * Reset pagination when search query changes.
     */
    public function updatedSearchQuery(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filters change.
     */
    public function updatedUnreadOnly(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when type filter changes.
     */
    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when date filters change.
     */
    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when date filters change.
     */
    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle unread filter.
     */
    public function toggleUnreadFilter(): void
    {
        $this->unreadOnly = ! $this->unreadOnly;
        $this->resetPage();
    }

    /**
     * Set type filter.
     */
    public function setTypeFilter(?string $type): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    /**
     * Set sort field and direction.
     */
    public function setSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->searchQuery = '';
        $this->unreadOnly = false;
        $this->typeFilter = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Search notifications.
     */
    public function search(): void
    {
        $this->resetPage();
    }

    /**
     * Apply filters.
     */
    public function applyFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->update(['read_at' => now()]);

        $this->dispatch('notification-read', notificationId: $notificationId);
        $this->dispatch('toast', message: __('notifications.marked_read'), type: 'success');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.marked_read'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at');

        if ($this->typeFilter) {
            $query->where('type', 'like', '%'.$this->typeFilter.'%');
        }

        $count = $query->count();
        $query->update(['read_at' => now()]);

        $this->dispatch('all-notifications-read');
        $this->dispatch('toast', message: __('notifications.all_marked_read'), type: 'success');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.all_marked_read_count', ['count' => $count]));
    }

    /**
     * Mark selected notifications as read (bulk action).
     */
    public function markSelectedAsRead(): void
    {
        $user = Auth::user();

        if (! $user || empty($this->selectedIds)) {
            return;
        }

        $count = count($this->selectedIds);

        DB::table('notifications')
            ->whereIn('id', $this->selectedIds)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->update(['read_at' => now()]);

        $this->selectedIds = [];
        $this->selectAllVisible = false;
        $this->dispatch('toast', message: __('notifications.selected_marked_read'), type: 'success');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.bulk_marked_read', ['count' => $count]));
    }

    /**
     * Delete a single notification.
     */
    public function deleteNotification(string $notificationId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->delete();

        $this->dispatch('toast', message: __('notifications.deleted'), type: 'success');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.deleted'));
    }

    /**
     * Delete selected notifications (bulk action).
     */
    public function deleteSelected(): void
    {
        $user = Auth::user();

        if (! $user || empty($this->selectedIds)) {
            return;
        }

        $count = count($this->selectedIds);

        DB::table('notifications')
            ->whereIn('id', $this->selectedIds)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->delete();

        $this->selectedIds = [];
        $this->selectAllVisible = false;
        $this->dispatch('toast', message: __('notifications.selected_deleted'), type: 'success');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.bulk_deleted', ['count' => $count]));
    }

    /**
     * Toggle notification selection.
     */
    public function toggleSelection(string $notificationId): void
    {
        if (in_array($notificationId, $this->selectedIds)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$notificationId]));
        } else {
            $this->selectedIds[] = $notificationId;
        }

        // Update selectAllVisible state
        $this->updateSelectAllState();
    }

    /**
     * Update select all visible state based on current selection.
     */
    protected function updateSelectAllState(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $visibleIds = $this->getVisibleNotificationIds();
        $this->selectAllVisible = ! empty($visibleIds) && empty(array_diff($visibleIds, $this->selectedIds));
    }

    /**
     * Get IDs of currently visible notifications.
     *
     * @return array<int, string>
     */
    protected function getVisibleNotificationIds(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $query = $this->buildNotificationQuery($user);

        return $query->pluck('id')->toArray();
    }

    /**
     * Select all visible notifications.
     */
    public function selectAll(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $this->selectedIds = $this->getVisibleNotificationIds();
        $this->selectAllVisible = true;

        // Announce to screen readers
        $count = count($this->selectedIds);
        $this->dispatch('aria-announce', message: __('notifications.selected_count', ['count' => $count]));
    }

    /**
     * Deselect all notifications.
     */
    public function deselectAll(): void
    {
        $this->selectedIds = [];
        $this->selectAllVisible = false;

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.deselected_all'));
    }

    /**
     * Toggle select all visible notifications.
     */
    public function toggleSelectAll(): void
    {
        if ($this->selectAllVisible) {
            $this->deselectAll();
        } else {
            $this->selectAll();
        }
    }

    /**
     * Open export modal.
     */
    public function openExportModal(): void
    {
        $this->showExportModal = true;
    }

    /**
     * Close export modal.
     */
    public function closeExportModal(): void
    {
        $this->showExportModal = false;
    }

    /**
     * Export notifications to file.
     */
    public function exportNotifications(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $query = $this->buildNotificationQuery($user);
        $notifications = $query->get();

        $filename = 'notifications_'.now()->format('Y-m-d_His');

        if ($this->exportFormat === 'json') {
            return $this->exportAsJson($notifications, $filename);
        }

        return $this->exportAsCsv($notifications, $filename);
    }

    /**
     * Export notifications as CSV.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $notifications
     */
    protected function exportAsCsv($notifications, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return Response::streamDownload(function () use ($notifications): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            // Write CSV header
            fputcsv($handle, ['ID', 'Type', 'Title', 'Message', 'Created At', 'Read At', 'URL']);

            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);

                fputcsv($handle, [
                    $notification->id,
                    $data['type'] ?? 'general',
                    $data['title'] ?? '',
                    $data['message'] ?? '',
                    $notification->created_at,
                    $notification->read_at ?? '',
                    $data['url'] ?? '',
                ]);
            }

            fclose($handle);
        }, "{$filename}.csv", $headers);
    }

    /**
     * Export notifications as JSON.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $notifications
     */
    protected function exportAsJson($notifications, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
        ];

        $exportData = $notifications->map(function ($notification) {
            $data = json_decode($notification->data, true);

            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'general',
                'title' => $data['title'] ?? '',
                'message' => $data['message'] ?? '',
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
                'url' => $data['url'] ?? null,
            ];
        })->toArray();

        return Response::streamDownload(function () use ($exportData): void {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, "{$filename}.json", $headers);
    }

    /**
     * Build the notification query with all filters applied.
     */
    protected function buildNotificationQuery(mixed $user): \Illuminate\Database\Query\Builder
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user));

        // Apply search filter
        if (! empty($this->searchQuery)) {
            $searchTerm = '%'.$this->searchQuery.'%';
            $query->where(function ($q) use ($searchTerm): void {
                $q->where('data', 'like', $searchTerm);
            });
        }

        // Apply unread filter
        if ($this->unreadOnly) {
            $query->whereNull('read_at');
        }

        // Apply type filter
        if ($this->typeFilter) {
            $query->where('type', 'like', '%'.$this->typeFilter.'%');
        }

        // Apply date range filters
        if ($this->dateFrom) {
            $query->where('created_at', '>=', Carbon::parse($this->dateFrom)->startOfDay());
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', Carbon::parse($this->dateTo)->endOfDay());
        }

        return $query;
    }

    /**
     * Get unread count for the current user.
     */
    #[Computed]
    public function unreadCount(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get total count for the current user.
     */
    #[Computed]
    public function totalCount(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->count();
    }

    /**
     * Check if any filters are active.
     */
    #[Computed]
    public function hasActiveFilters(): bool
    {
        return ! empty($this->searchQuery)
            || $this->unreadOnly
            || $this->typeFilter !== null
            || $this->dateFrom !== null
            || $this->dateTo !== null;
    }

    /**
     * Handle real-time notification updates.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('notification-created')]
    public function handleNewNotification(array $event): void
    {
        // Refresh the view when new notification arrives
        $this->dispatch('$refresh');

        // Announce to screen readers
        $this->dispatch('aria-announce', message: __('notifications.new_notification_received'));
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        $query = $this->buildNotificationQuery($user);

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $notifications = $query->paginate($this->perPage);

        // Transform notifications
        $notifications->getCollection()->transform(function ($notification) {
            $data = json_decode($notification->data, true);
            $type = $data['type'] ?? 'general';

            return (object) [
                'id' => $notification->id,
                'type' => $type,
                'category' => $this->mapNotificationType($type),
                'title' => $data['title'] ?? __('notifications.untitled'),
                'message' => $data['message'] ?? '',
                'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
                'created_at_full' => Carbon::parse($notification->created_at)->format('Y-m-d H:i'),
                'created_at_raw' => $notification->created_at,
                'url' => $data['url'] ?? null,
                'read_at' => $notification->read_at,
                'is_read' => $notification->read_at !== null,
                'icon' => $this->getIconForType($type),
                'iconBg' => $this->getIconBgForType($type),
            ];
        });

        return view('livewire.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => $this->unreadCount,
            'totalCount' => $this->totalCount,
            'hasActiveFilters' => $this->hasActiveFilters,
        ]);
    }

    /**
     * Map notification type to category.
     */
    protected function mapNotificationType(string $type): string
    {
        return match (true) {
            str_contains($type, 'ticket') => 'tickets',
            str_contains($type, 'loan'), str_contains($type, 'approval') => 'loans',
            str_contains($type, 'sla'), str_contains($type, 'overdue') => 'alerts',
            default => 'system',
        };
    }

    /**
     * Get Heroicon name for notification type.
     */
    protected function getIconForType(string $type): string
    {
        return match (true) {
            str_contains($type, 'ticket') => 'heroicon-o-ticket',
            str_contains($type, 'loan') => 'heroicon-o-clipboard-document-check',
            str_contains($type, 'approval') => 'heroicon-o-check-badge',
            str_contains($type, 'warning'), str_contains($type, 'sla') => 'heroicon-o-exclamation-triangle',
            str_contains($type, 'error'), str_contains($type, 'rejected') => 'heroicon-o-x-circle',
            str_contains($type, 'overdue') => 'heroicon-o-clock',
            str_contains($type, 'system') => 'heroicon-o-cog-6-tooth',
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
            str_contains($type, 'warning'), str_contains($type, 'sla'), str_contains($type, 'overdue') => 'bg-warning-100 dark:bg-warning-900/50 text-warning-600 dark:text-warning-400',
            str_contains($type, 'error'), str_contains($type, 'rejected') => 'bg-danger-100 dark:bg-danger-900/50 text-danger-600 dark:text-danger-400',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
        };
    }
}
