<?php

declare(strict_types=1);

// name: NotificationCenter
// description: Full notification history with pagination, filtering, and bulk actions
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-008, D04 §5.3, D12 §4 (Requirements 6.3, 6.4, 6.5)
// last-updated: 2025-11-06

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    /**
     * Filter: show only unread notifications.
     */
    public bool $unreadOnly = false;

    /**
     * Filter: notification type.
     */
    public ?string $typeFilter = null;

    /**
     * Selected notification IDs for bulk actions.
     */
    public array $selectedIds = [];

    /**
     * Available notification types.
     */
    public array $availableTypes = [
        'ticket_status' => 'Helpdesk Ticket Status',
        'loan_approval' => 'Loan Approval',
        'loan_status' => 'Loan Status Update',
        'overdue_reminder' => 'Overdue Reminder',
        'system_announcement' => 'System Announcement',
    ];

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
            $query->where('type', $this->typeFilter);
        }

        $query->update(['read_at' => now()]);

        $this->dispatch('all-notifications-read');
        $this->dispatch('success', message: __('notifications.all_marked_read'));
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

        DB::table('notifications')
            ->whereIn('id', $this->selectedIds)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->update(['read_at' => now()]);

        $this->selectedIds = [];
        $this->dispatch('success', message: __('notifications.selected_marked_read'));
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

        DB::table('notifications')
            ->whereIn('id', $this->selectedIds)
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->delete();

        $this->selectedIds = [];
        $this->dispatch('success', message: __('notifications.selected_deleted'));
    }

    /**
     * Toggle notification selection.
     */
    public function toggleSelection(string $notificationId): void
    {
        if (in_array($notificationId, $this->selectedIds)) {
            $this->selectedIds = array_diff($this->selectedIds, [$notificationId]);
        } else {
            $this->selectedIds[] = $notificationId;
        }
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

        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user));

        if ($this->unreadOnly) {
            $query->whereNull('read_at');
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $this->selectedIds = $query->pluck('id')->toArray();
    }

    /**
     * Deselect all notifications.
     */
    public function deselectAll(): void
    {
        $this->selectedIds = [];
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

        $query = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user));

        if ($this->unreadOnly) {
            $query->whereNull('read_at');
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        $notifications = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Transform notifications
        $notifications->getCollection()->transform(function ($notification) {
            $data = json_decode($notification->data, true);

            return (object) [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'general',
                'title' => $data['title'] ?? __('notifications.untitled'),
                'message' => $data['message'] ?? '',
                'created_at' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                'created_at_full' => \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d H:i'),
                'url' => $data['url'] ?? null,
                'read_at' => $notification->read_at,
                'is_read' => $notification->read_at !== null,
            ];
        });

        return view('livewire.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', get_class($user))
                ->whereNull('read_at')
                ->count(),
        ]);
    }
}
