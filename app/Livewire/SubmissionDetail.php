<?php

declare(strict_types=1);

// name: SubmissionDetail
// description: Display comprehensive submission information for authenticated staff
// author: dev-team@motac.gov.my
// trace: SRS-FR-014; D04 §4.3; D11 §7; Requirements 2.4, 2.5, 7.1, 10.1, 10.2, 10.3
// last-updated: 2025-11-06

namespace App\Livewire;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\SubmissionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmissionDetail extends Component
{
    public int $id = 0; // Route parameter: the submission ID

    public ?string $type = null; // 'helpdesk' or 'loans', from query param

    public bool $showClaimModal = false;

    public bool $showCancelModal = false;

    public string $cancelReason = '';

    protected SubmissionService $submissionService;

    /**
     * Boot component with dependency injection
     */
    public function boot(SubmissionService $submissionService): void
    {
        $this->submissionService = $submissionService;
    }

    /**
     * Mount component with route and query parameters
     */
    public function mount(int $id, ?string $type = null): void
    {
        $this->id = $id;
        $this->type = $type ?? 'ticket'; // Default to ticket

        // Verify access to submission
        $submission = $this->submission;
        if (! $submission) {
            abort(404, __('validation.submission_not_found'));
        }
    }

    /**
     * Get submission model with eager loading
     */
    #[Computed]
    public function submission(): HelpdeskTicket|LoanApplication|null
    {
        if ($this->type === 'helpdesk' || $this->type === 'ticket') {
            return HelpdeskTicket::with([
                'user', 'division', 'category', 'internalComments.user', 'attachments',
            ])->find($this->id);
        }

        if ($this->type === 'loans') {
            return LoanApplication::with([
                'user', 'items.asset', 'approvalHistory', 'internalComments.user',
            ])->find($this->id);
        }

        return null;
    }

    /**
     * Check if submission is claimable (guest submission)
     */
    #[Computed]
    public function isClaimable(): bool
    {
        $submission = $this->submission;

        if ($this->type === 'helpdesk') {
            return $submission instanceof HelpdeskTicket
                && $submission->user_id === null
                && $submission->guest_email === Auth::user()?->email;
        }

        return false;
    }

    /**
     * Check if submission can be cancelled
     */
    #[Computed]
    public function isCancellable(): bool
    {
        $submission = $this->submission;

        if ($this->type === 'helpdesk') {
            return $submission instanceof HelpdeskTicket
                && in_array($submission->status, ['open', 'in_progress', 'pending']);
        }

        if ($this->type === 'loans') {
            return $submission instanceof LoanApplication
                && in_array($submission->status, ['pending', 'approved']);
        }

        return false;
    }

    /**
     * Get formatted timeline activities
     */
    #[Computed]
    public function timelineActivities(): array
    {
        $submission = $this->submission;

        if (! $submission || ! $submission->relationLoaded('activities')) {
            return [];
        }

        return $submission->activities
            ->sortByDesc('created_at')
            ->take(20)
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->activity_type,
                    'description' => $activity->description,
                    'user_name' => $activity->user?->name ?? __('portal.system'),
                    'created_at' => $activity->created_at,
                    'icon' => $this->getActivityIcon($activity->activity_type),
                    'color' => $this->getActivityColor($activity->activity_type),
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Get icon for activity type
     */
    private function getActivityIcon(string $type): string
    {
        return match ($type) {
            'created' => 'heroicon-o-plus-circle',
            'status_changed' => 'heroicon-o-arrow-path',
            'assigned' => 'heroicon-o-user',
            'comment_added' => 'heroicon-o-chat-bubble-left',
            'approved' => 'heroicon-o-check-circle',
            'rejected' => 'heroicon-o-x-circle',
            'returned' => 'heroicon-o-arrow-uturn-left',
            default => 'heroicon-o-information-circle',
        };
    }

    /**
     * Get color for activity type
     */
    private function getActivityColor(string $type): string
    {
        return match ($type) {
            'created' => 'blue',
            'status_changed' => 'amber',
            'assigned' => 'purple',
            'comment_added' => 'gray',
            'approved' => 'green',
            'rejected' => 'red',
            'returned' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Open claim modal
     */
    public function openClaimModal(): void
    {
        if ($this->isClaimable) {
            $this->showClaimModal = true;
        }
    }

    /**
     * Close claim modal
     */
    public function closeClaimModal(): void
    {
        $this->showClaimModal = false;
    }

    /**
     * Claim guest submission
     */
    public function claimSubmission(): void
    {
        if (! $this->isClaimable) {
            session()->flash('error', __('portal.cannot_claim_submission'));

            return;
        }

        $submission = $this->submission;

        if ($submission instanceof HelpdeskTicket) {
            $previousEmail = $submission->guest_email;

            $submission->update([
                'user_id' => Auth::id(),
                'guest_email' => null,
                'guest_name' => null,
                'guest_phone' => null,
            ]);

            // Log activity
            $submission->activities()->create([
                'activity_type' => 'claimed',
                'description' => __('portal.submission_claimed_by_user', ['user' => Auth::user()->name]),
                'user_id' => Auth::id(),
                'metadata' => [
                    'previous_email' => $previousEmail,
                ],
            ]);

            session()->flash('success', __('portal.submission_claimed_successfully'));
            $this->showClaimModal = false;
            $this->dispatch('submission-claimed');
        }
    }

    /**
     * Open cancel modal
     */
    public function openCancelModal(): void
    {
        if ($this->isCancellable) {
            $this->showCancelModal = true;
            $this->cancelReason = '';
        }
    }

    /**
     * Close cancel modal
     */
    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancelReason = '';
    }

    /**
     * Cancel submission
     */
    public function cancelSubmission(): void
    {
        $this->validate([
            'cancelReason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if (! $this->isCancellable) {
            session()->flash('error', __('portal.cannot_cancel_submission'));

            return;
        }

        $submission = $this->submission;

        $submission->update([
            'status' => 'cancelled',
            'cancellation_reason' => $this->cancelReason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);

        // Log activity
        $submission->activities()->create([
            'activity_type' => 'cancelled',
            'description' => __('portal.submission_cancelled_by_user', ['user' => Auth::user()->name]),
            'user_id' => Auth::id(),
            'metadata' => [
                'reason' => $this->cancelReason,
            ],
        ]);

        session()->flash('success', __('portal.submission_cancelled_successfully'));
        $this->showCancelModal = false;
        $this->dispatch('submission-cancelled');
    }

    /**
     * Refresh submission data
     */
    public function refreshSubmission(): void
    {
        unset($this->submission);
        $this->dispatch('submission-refreshed');
    }

    /**
     * Handle status update from Echo broadcast.
     */
    public function handleEchoStatusUpdate(array $event): void
    {
        // Check if this update is for current submission
        if ($event['submission_type'] === $this->type && $event['submission_id'] === $this->id) {
            $this->refreshSubmission();
            session()->flash('info', __('portal.submission_status_updated', [
                'status' => $event['new_status'],
            ]));
        }
    }

    /**
     * Get event listeners for Echo integration.
     *
     * @return array<string, string>
     */
    protected function getListeners(): array
    {
        return [
            'echo:status-updated' => 'handleEchoStatusUpdate',
            'submission-claimed' => '$refresh',
            'submission-cancelled' => '$refresh',
            'submission-refreshed' => '$refresh',
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.submission-detail', [
            'submission' => $this->submission,
            'isClaimable' => $this->isClaimable,
            'isCancellable' => $this->isCancellable,
            'timelineActivities' => $this->timelineActivities,
        ]);
    }
}
