<?php

declare(strict_types=1);

namespace App\Livewire\Approver;

use App\Models\LoanApplication;
use App\Models\User;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Approval Queue Component
 *
 * Displays pending loan applications requiring approval from Grade 41+ users.
 * Supports bulk operations, SLA monitoring, and optimistic UI updates.
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @author Frontend Engineering Team
 */
class ApprovalQueue extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    /**
     * Filter by status
     */
    #[Url(as: 'status')]
    public string $statusFilter = 'pending';

    /**
     * Search query
     */
    #[Url(as: 'q')]
    public string $search = '';

    /**
     * Sort field
     */
    #[Url(as: 'sort')]
    public string $sortField = 'created_at';

    /**
     * Sort direction
     */
    #[Url(as: 'dir')]
    public string $sortDirection = 'asc';

    /**
     * Selected applications for bulk actions
     *
     * @var array<int, int>
     */
    public array $selected = [];

    /**
     * Select all flag
     */
    public bool $selectAll = false;

    /**
     * Bulk action result message
     */
    public string $bulkActionMessage = '';

    /**
     * Mount component
     */
    public function mount(): void
    {
        // Verify user has approver access
        $user = Auth::user();

        if (! $user instanceof User || ! $user->canApprove()) {
            abort(403, 'Unauthorized access. Grade 41+ required.');
        }
    }

    /**
     * Get pending loan applications
     *
     * @return LengthAwarePaginator<int, LoanApplication>
     */
    #[Computed]
    public function pendingApplications(): LengthAwarePaginator
    {
        /** @var Builder<LoanApplication> $query */
        $query = LoanApplication::query()
            ->with(['user', 'division', 'loanItems.asset'])
            ->where('approval_status', 'pending_approval');

        // Apply search filter
        if (! empty($this->search)) {
            $query->where(function (Builder $q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('applicant_name', 'like', "%{$this->search}%")
                    ->orWhereHas('user', function (Builder $userQuery) {
                        $userQuery->where('name', 'like', "%{$this->search}%");
                    });
            });
        }

        // Apply status filter if not 'all'
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    /**
     * Get SLA statistics
     *
     * @return array<string, int>
     */
    #[Computed]
    public function slaStats(): array
    {
        $pending = LoanApplication::where('approval_status', 'pending_approval')->get();

        $urgent = $pending->filter(function (LoanApplication $app) {
            return $app->created_at->diffInHours(now()) > 48; // 48 hours SLA
        })->count();

        $warning = $pending->filter(function (LoanApplication $app) {
            return $app->created_at->diffInHours(now()) > 24 && $app->created_at->diffInHours(now()) <= 48;
        })->count();

        $normal = $pending->count() - $urgent - $warning;

        return [
            'total' => $pending->count(),
            'urgent' => $urgent,
            'warning' => $warning,
            'normal' => $normal,
        ];
    }

    /**
     * Toggle select all
     */
    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selected = $this->pendingApplications->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    /**
     * Approve a single application
     */
    public function approve(int $applicationId): void
    {
        $this->processSingleAction($applicationId, 'approved');
    }

    /**
     * Reject a single application
     */
    public function reject(int $applicationId, ?string $reason = null): void
    {
        $this->processSingleAction($applicationId, 'rejected', $reason);
    }

    /**
     * Process single approval/rejection
     */
    protected function processSingleAction(int $applicationId, string $action, ?string $reason = null): void
    {
        /** @var User $user */
        $user = Auth::user();

        $application = LoanApplication::findOrFail($applicationId);

        $application->update([
            'approval_status' => $action,
            'approver_id' => $user->id,
            'approved_at' => $action === 'approved' ? now() : null,
            'rejection_reason' => $reason,
        ]);

        $this->dispatch('application-processed', [
            'id' => $applicationId,
            'action' => $action,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __("Application {$application->application_number} {$action} successfully."),
        ]);
    }

    /**
     * Bulk approve selected applications
     */
    public function bulkApprove(): void
    {
        $this->processBulkAction('approved');
    }

    /**
     * Bulk reject selected applications
     */
    public function bulkReject(?string $reason = null): void
    {
        $this->processBulkAction('rejected', $reason);
    }

    /**
     * Process bulk approval/rejection
     */
    protected function processBulkAction(string $action, ?string $reason = null): void
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => __('Please select at least one application.'),
            ]);

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        $count = LoanApplication::whereIn('id', $this->selected)
            ->update([
                'approval_status' => $action,
                'approver_id' => $user->id,
                'approved_at' => $action === 'approved' ? now() : null,
                'rejection_reason' => $reason,
            ]);

        $this->selected = [];
        $this->selectAll = false;

        $this->bulkActionMessage = __("{$count} applications {$action} successfully.");

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->bulkActionMessage,
        ]);
    }

    /**
     * Change sort
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.approver.approval-queue')
            ->layout('layouts.portal');
    }
}
