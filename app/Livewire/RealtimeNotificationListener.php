<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Real-time Notification Listener Component
 *
 * Global component that listens for WebSocket events via Laravel Reverb
 * and dispatches browser events for UI updates. Include this component
 * in layouts to enable real-time notifications across the application.
 *
 * Features:
 * - Listens to user-specific private channels
 * - Dispatches browser events for toast notifications
 * - Updates ARIA live regions for screen reader announcements
 * - Handles connection state changes
 *
 * @see .kiro/specs/frontend-comprehensive-v3.6/requirements.md - Requirements 10.1, 10.3, 10.4
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 *
 * @trace D03 SRS-FR-008; D04 §5.3
 *
 * @wcag WCAG 2.2 Level AA (SC 4.1.3 Status Messages)
 */
class RealtimeNotificationListener extends Component
{
    /**
     * Connection status for UI feedback.
     */
    public bool $connected = false;

    /**
     * Last received notification timestamp.
     */
    public ?string $lastNotificationAt = null;

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
            "echo-private:user.{$user->id},.ticket.status.changed" => 'handleTicketStatusChanged',
            "echo-private:user.{$user->id},.loan.status.changed" => 'handleLoanStatusChanged',
            "echo-private:user.{$user->id},.notification.created" => 'handleNotificationCreated',
            "echo-private:user.{$user->id},.status.updated" => 'handleStatusUpdated',
            "echo-private:user.{$user->id},.comment.posted" => 'handleCommentPosted',
        ];
    }

    /**
     * Handle ticket status change event.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:ticket.status.changed')]
    public function handleTicketStatusChanged(array $event): void
    {
        $this->lastNotificationAt = now()->toISOString();

        $message = $event['message'] ?? __('notifications.ticket_updated');
        $ticketNumber = $event['ticket_number'] ?? '';

        // Dispatch toast notification
        $this->dispatch('toast', message: $message, type: 'info');

        // Dispatch for ARIA live region announcement
        $this->dispatch('announce', message: $message, priority: 'polite');

        // Refresh notification bell
        $this->dispatch('refresh-notifications');

        // Dispatch custom event for other components
        $this->dispatch('ticket-status-changed', [
            'ticketNumber' => $ticketNumber,
            'newStatus' => $event['new_status'] ?? '',
            'oldStatus' => $event['old_status'] ?? '',
        ]);
    }

    /**
     * Handle loan status change event.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:loan.status.changed')]
    public function handleLoanStatusChanged(array $event): void
    {
        $this->lastNotificationAt = now()->toISOString();

        $message = $event['message'] ?? __('notifications.loan_updated');
        $applicationNumber = $event['application_number'] ?? '';

        // Dispatch toast notification
        $this->dispatch('toast', message: $message, type: 'info');

        // Dispatch for ARIA live region announcement
        $this->dispatch('announce', message: $message, priority: 'polite');

        // Refresh notification bell
        $this->dispatch('refresh-notifications');

        // Dispatch custom event for other components
        $this->dispatch('loan-status-changed', [
            'applicationNumber' => $applicationNumber,
            'newStatus' => $event['new_status'] ?? '',
            'oldStatus' => $event['old_status'] ?? '',
        ]);
    }

    /**
     * Handle new notification created event.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:notification.created')]
    public function handleNotificationCreated(array $event): void
    {
        $this->lastNotificationAt = now()->toISOString();

        $data = $event['data'] ?? [];
        $title = $data['title'] ?? __('notifications.new_notification');
        $type = $data['type'] ?? 'info';

        // Dispatch toast notification
        $this->dispatch('toast', message: $title, type: $type);

        // Dispatch for ARIA live region announcement
        $this->dispatch('announce', message: $title, priority: 'polite');

        // Refresh notification bell
        $this->dispatch('refresh-notifications');
    }

    /**
     * Handle generic status update event.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:status.updated')]
    public function handleStatusUpdated(array $event): void
    {
        $this->lastNotificationAt = now()->toISOString();

        $modelType = $event['model_type'] ?? 'Item';
        $newStatus = $event['new_status'] ?? 'updated';
        $message = __('notifications.status_updated', ['type' => $modelType, 'status' => $newStatus]);

        // Dispatch toast notification
        $this->dispatch('toast', message: $message, type: 'info');

        // Dispatch for ARIA live region announcement
        $this->dispatch('announce', message: $message, priority: 'polite');

        // Refresh notification bell
        $this->dispatch('refresh-notifications');
    }

    /**
     * Handle comment posted event.
     *
     * @param  array<string, mixed>  $event
     */
    #[On('echo-private:comment.posted')]
    public function handleCommentPosted(array $event): void
    {
        $this->lastNotificationAt = now()->toISOString();

        $authorName = $event['author_name'] ?? __('common.someone');
        $message = __('notifications.comment_posted', ['author' => $authorName]);

        // Dispatch toast notification
        $this->dispatch('toast', message: $message, type: 'info');

        // Dispatch for ARIA live region announcement
        $this->dispatch('announce', message: $message, priority: 'polite');

        // Refresh notification bell
        $this->dispatch('refresh-notifications');
    }

    /**
     * Handle WebSocket connection established.
     */
    #[On('echo:connected')]
    public function handleConnected(): void
    {
        $this->connected = true;
    }

    /**
     * Handle WebSocket disconnection.
     */
    #[On('echo:disconnected')]
    public function handleDisconnected(): void
    {
        $this->connected = false;
    }

    /**
     * Render the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.realtime-notification-listener');
    }
}
