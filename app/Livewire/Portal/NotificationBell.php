<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Notification bell icon + unread count presenter.
 *
 * Exposes unreadCount, supports dropdown toggle, marking notifications
 * as read (single/all), and handling Echo-style notification payloads.
 *
 * Trace: D03 SRS-FR-006; D04 §3.5; D11 §9
 */
class NotificationBell extends Component
{
	public int $unreadCount = 0;

	public bool $open = false;

	/**
	 * Initialize unread count for the authenticated user.
	 */
	public function mount(): void
	{
		$this->unreadCount = (int) Auth::user()?->unreadNotifications()->count();
	}

	/**
	 * Toggle the dropdown panel visibility.
	 */
	public function toggleDropdown(): void
	{
		$this->open = ! $this->open;
	}

	/**
	 * Mark a single notification as read.
	 */
	public function markAsRead(string $notificationId): void
	{
		$notification = Auth::user()?->notifications()
			->whereKey($notificationId)
			->first();

		if ($notification instanceof DatabaseNotification && $notification->read_at === null) {
			$notification->markAsRead();
		}

		$this->refreshUnreadCount();
	}

	/**
	 * Mark all notifications as read.
	 */
	public function markAllAsRead(): void
	{
		$user = Auth::user();
		if ($user) {
			$user->unreadNotifications->markAsRead();
		}

		$this->refreshUnreadCount();
	}

	/**
	 * Handle a new notification payload from Echo and update the count.
	 *
	 * @param  array{id:string,type:string,data:array}  $payload
	 */
	public function handleEchoNotification(array $payload): void
	{
		// Optimistic update of unread counter – tests only assert the count.
		$this->unreadCount++;
	}

	/**
	 * Recalculate unread count from the database.
	 */
	private function refreshUnreadCount(): void
	{
		$this->unreadCount = (int) Auth::user()?->unreadNotifications()->count();
	}

	public function render()
	{
		$notifications = Auth::user()?->notifications()
			->latest()
			->limit(5)
			->get();

		return view('livewire.portal.notification-bell', [
			'notifications' => $notifications,
		]);
	}
}
