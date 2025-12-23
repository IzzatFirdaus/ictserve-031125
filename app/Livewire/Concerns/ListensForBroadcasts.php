<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

/**
 * ListensForBroadcasts Trait
 *
 * Provides Laravel Echo broadcast listening capabilities for Livewire components.
 * Integrates with Laravel Reverb WebSocket server for real-time updates.
 *
 * Features:
 * - Automatic Echo listener registration for authenticated users
 * - Default handlers for notification and status update events
 * - Extensible via getAdditionalListeners() method
 * - Support for both user-specific and entity-specific channels
 *
 * @see D16 Broadcasting Setup
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md
 *
 * @requirements 1.3, 1.4
 *
 * @version 1.0.0
 */
trait ListensForBroadcasts
{
    /**
     * Get Echo listeners for real-time updates via Laravel Reverb.
     *
     * Automatically registers listeners for authenticated users on their private channel.
     * Components can extend this by implementing getAdditionalListeners().
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        $listeners = [];

        if (auth()->check()) {
            $userId = auth()->id();

            // Register default listeners for authenticated users
            $listeners["echo-private:user.{$userId},.notification.created"] = 'handleNotification';
            $listeners["echo-private:user.{$userId},.status.updated"] = 'handleStatusUpdate';
        }

        // Merge with component-specific listeners
        return array_merge($listeners, $this->getAdditionalListeners());
    }

    /**
     * Get additional component-specific listeners.
     *
     * Override this method in components to add custom Echo listeners.
     *
     * @return array<string, string>
     */
    protected function getAdditionalListeners(): array
    {
        return [];
    }

    /**
     * Handle notification.created event from Laravel Reverb.
     *
     * Default implementation dispatches a browser event for UI updates.
     * Override in component for custom behavior.
     *
     * @param  array<string, mixed>  $event
     */
    public function handleNotification(array $event): void
    {
        // Dispatch browser event for notification UI updates
        $this->dispatch('notification-received', $event);

        // Show toast notification if title is present
        if (isset($event['title'])) {
            $title = is_scalar($event['title']) ? (string) $event['title'] : '';
            $type = isset($event['type']) && is_scalar($event['type']) ? (string) $event['type'] : 'info';

            $this->dispatch('toast', message: $title, type: $type);
        }
    }

    /**
     * Handle status.updated event from Laravel Reverb.
     *
     * Default implementation dispatches a browser event for UI updates.
     * Override in component for custom behavior.
     *
     * @param  array<string, mixed>  $event
     */
    public function handleStatusUpdate(array $event): void
    {
        // Dispatch browser event for status UI updates
        $this->dispatch('status-updated', $event);

        // Show toast notification if message is present
        if (isset($event['message'])) {
            $message = is_scalar($event['message']) ? (string) $event['message'] : '';
            $this->dispatch('toast', message: $message, type: 'info');
        }
    }
}
