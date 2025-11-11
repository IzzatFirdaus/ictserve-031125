<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Notification center page component.
 *
 * Supports filtering (all/read/unread and by types), pagination,
 * deletion, and reacts to new notifications.
 *
 * Trace: D03 SRS-FR-006; D04 §3.5; D11 §9
 */
class NotificationCenter extends Component
{
	use WithPagination;

	public string $filter = 'all';

	/** @var array<int, string> */
	public array $notificationTypes = [];

	public int $perPage = 10;

	public function updatingFilter(): void
	{
		$this->resetPage();
	}

	public function filterBy(string $filter): void
	{
		$this->filter = $filter;
		$this->resetPage();
	}

	public function deleteNotification(string $notificationId): void
	{
		Auth::user()?->notifications()
			->whereKey($notificationId)
			->delete();
	}

	public function markAsRead(string $notificationId): void
	{
		$notification = Auth::user()?->notifications()
			->whereKey($notificationId)
			->first();

		if ($notification instanceof DatabaseNotification && $notification->read_at === null) {
			$notification->markAsRead();
		}
	}

	#[On('echo:notifications,NotificationCreated')]
	public function handleEchoNotification(array $payload = []): void
	{
		// No-op for now; reactivity occurs via render() pulling latest.
		// Method exists to satisfy potential listeners.
	}

	/**
	 * @phpstan-ignore-next-line
	 */
	private function labelForType(string $notificationType): string
	{
		return match ($notificationType) {
			'App\\Notifications\\TicketAssigned' => 'Ticket Assigned',
			'App\\Notifications\\TicketResolved' => 'Ticket Resolved',
			'App\\Notifications\\LoanApproved' => 'Loan Approved',
			'App\\Notifications\\LoanRejected' => 'Loan Rejected',
			'App\\Notifications\\AssetOverdue' => 'Asset Overdue',
			default => class_basename($notificationType),
		};
	}

	public function render()
	{
		$query = Auth::user()?->notifications()->latest();

		if ($this->filter === 'unread') {
			$query?->whereNull('read_at');
		} elseif ($this->filter === 'read') {
			$query?->whereNotNull('read_at');
		}

		if (! empty($this->notificationTypes)) {
			$query?->whereIn('type', $this->notificationTypes);
		}

		$notifications = $query?->paginate($this->perPage);

		return view('livewire.portal.notification-center', [
			'notifications' => $notifications,
			'labelForType' => fn (string $t): string => $this->labelForType($t),
		]);
	}
}
