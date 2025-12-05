<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Toast Notification Component
 *
 * Manages toast notification queue with auto-dismiss for success/info
 * and persistent display for error/warning until acknowledged.
 *
 * Features:
 * - Queue management with Livewire dispatch events
 * - Auto-dismiss for success/info (4-5s) per D14 §9.3
 * - Persist for error/warning until acknowledged per D14 §9.3
 * - ARIA live region with aria-live="polite" per D14 §10.4
 * - 44×44px dismiss button touch target per D12 §4.1
 * - slideInUp/slideOutDown animations using --motion-easeoutback (400ms) per D12 §6.10
 *
 * @see D12 §6.4 Notification patterns
 * @see D14 §9.3 Toast notification guidelines
 * @see D14 §10.4 ARIA live regions
 *
 * @requirements 30.1-30.5 Toast notification system
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 */
class Toast extends Component
{
    /**
     * Active toast notifications queue
     *
     * @var array<int, array{id: string, message: string, type: string, duration: int, title: string|null}>
     */
    public array $toasts = [];

    /**
     * Maximum number of visible toasts
     */
    public int $maxToasts = 5;

    /**
     * Add a new toast notification to the queue
     *
     * @param  string  $message  The notification message
     * @param  string  $type  Type: success, error, warning, info
     * @param  int  $duration  Auto-dismiss duration in ms (0 = persistent)
     * @param  string|null  $title  Optional title for the toast
     */
    #[On('toast')]
    public function addToast(
        string $message,
        string $type = 'info',
        int $duration = 4000,
        ?string $title = null
    ): void {
        $id = uniqid('toast_', true);

        // Determine duration based on type per D14 §9.3
        // Success/Info: 4-5s auto-dismiss
        // Error/Warning: persistent until acknowledged
        if ($duration === 4000) {
            $duration = match ($type) {
                'success', 'info' => 4000,
                'error', 'warning' => 0, // Persistent
                default => 4000,
            };
        }

        $this->toasts[] = [
            'id' => $id,
            'message' => $message,
            'type' => $type,
            'duration' => $duration,
            'title' => $title,
        ];

        // Limit visible toasts
        if (\count($this->toasts) > $this->maxToasts) {
            array_shift($this->toasts);
        }

        // Note: Auto-dismiss is handled client-side via Alpine.js x-init
        // This provides smoother animations and doesn't require server round-trips
    }

    /**
     * Dismiss a specific toast by ID
     */
    #[On('dismiss-toast')]
    public function dismissToast(string $id): void
    {
        $this->toasts = array_values(
            array_filter($this->toasts, fn (array $toast): bool => $toast['id'] !== $id)
        );
    }

    /**
     * Dismiss all toasts
     */
    public function dismissAll(): void
    {
        $this->toasts = [];
    }

    /**
     * Get the icon name for a toast type
     */
    public function getIconForType(string $type): string
    {
        return match ($type) {
            'success' => 'heroicon-s-check-circle',
            'error' => 'heroicon-s-x-circle',
            'warning' => 'heroicon-s-exclamation-triangle',
            'info' => 'heroicon-s-information-circle',
            default => 'heroicon-s-information-circle',
        };
    }

    /**
     * Get CSS classes for toast type styling
     */
    public function getClassesForType(string $type): string
    {
        return match ($type) {
            'success' => 'bg-success-50 border-success-200 text-success-800',
            'error' => 'bg-danger-50 border-danger-200 text-danger-800',
            'warning' => 'bg-warning-50 border-warning-200 text-warning-800',
            'info' => 'bg-primary-50 border-primary-200 text-primary-800',
            default => 'bg-gray-50 border-gray-200 text-gray-800',
        };
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.components.toast');
    }
}
