<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use App\Services\NotificationService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Component: ApprovalInterface
 *
 * Provides loan application approval interface for Grade 41+ officers.
 * Supports both approval and rejection with comments and audit logging.
 *
 * @see D03-FR-023.1 (Approval interface for Grade 41+)
 * @see D03-FR-023.2 (Approval/rejection actions)
 * @see D04 §6.6 (Approval Interface Component)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-05
 *
 * WCAG 2.2 Level AA Compliance:
 * - Proper ARIA attributes for modals and forms
 * - Keyboard navigation support
 * - Screen reader announcements for approval actions
 * - 44×44px touch targets on all interactive elements
 */
class ApprovalInterface extends Component
{
    use AuthorizesRequests;
    use OptimizedLivewireComponent;
    use WithPagination;

    public string $statusFilter = 'pending';

    public string $applicantSearch = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?int $selectedApplicationId = null;

    public string $approvalAction = '';

    #[Validate('nullable|string|max:500')]
    public string $approvalRemarks = '';

    /**
     * Selected application IDs for bulk operations.
     *
     * @var array<int>
     */
    public array $selectedApplications = [];

    /**
     * Initialize component and verify authorization
     */
    public function mount(): void
    {
        // Verify user is Grade 41+ (Approver role)
        // Check role column attribute (same approach as middleware)
        $user = Auth::user();
        $allowedRoles = ['approver', 'admin', 'superuser'];

        if (! in_array(strtolower($user->role ?? ''), $allowedRoles)) {
            abort(403, __('staff.approvals.unauthorized'));
        }
    }

    /**
     * Get pending loan applications for approval
     */
    #[Computed]
    public function pendingApprovals()
    {
        $user = Auth::user();

        return LoanApplication::query()
            ->when($this->statusFilter === 'pending', fn ($q) => $q->where('status', LoanStatus::UNDER_REVIEW))
            ->when($this->statusFilter === 'approved', fn ($q) => $q->where('status', LoanStatus::APPROVED))
            ->when($this->statusFilter === 'rejected', fn ($q) => $q->where('status', LoanStatus::REJECTED))
            ->when($this->applicantSearch, function ($q) {
                $q->where(function ($query) {
                    $query->where('applicant_name', 'like', "%{$this->applicantSearch}%")
                        ->orWhere('applicant_email', 'like', "%{$this->applicantSearch}%")
                        ->orWhere('application_number', 'like', "%{$this->applicantSearch}%");
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->whereRaw('LOWER(approver_email) = ?', [strtolower($user->email)])
            ->with(['user'])
            ->latest()
            ->paginate(10);
    }

    /**
     * Open approval modal for an application
     */
    public function openApprovalModal(int $applicationId, string $action): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->approvalAction = $action;
        $this->approvalRemarks = '';
        $this->resetErrorBag();
    }

    /**
     * Close approval modal
     */
    public function closeApprovalModal(): void
    {
        $this->selectedApplicationId = null;
        $this->approvalAction = '';
        $this->approvalRemarks = '';
        $this->resetErrorBag();
    }

    /**
     * Toggle application selection for bulk actions.
     */
    public function selectApplication(int $applicationId): void
    {
        if (in_array($applicationId, $this->selectedApplications, true)) {
            $this->selectedApplications = array_values(array_filter(
                $this->selectedApplications,
                fn ($id) => $id !== $applicationId
            ));

            return;
        }

        $this->selectedApplications[] = $applicationId;
    }

    /**
     * Approve a loan application
     */
    public function approve(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        $this->validate();

        if (! $this->selectedApplicationId || $this->approvalAction !== 'approve') {
            return;
        }

        try {
            $application = LoanApplication::findOrFail($this->selectedApplicationId);

            // Authorize the action
            Gate::authorize('approve', $application);

            // Approve the application
            $loanService->approveApplication(
                $application,
                Auth::user(),
                $this->approvalRemarks,
                'portal'
            );

            // Send notification
            $notificationService->sendApprovalDecision($application, true, $this->approvalRemarks);

            session()->flash('success', __('staff.approvals.approved_success'));
            $this->dispatch('announce', message: __('staff.approvals.approved_success'));

            $this->closeApprovalModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            \Log::error('Failed to approve loan application', [
                'application_id' => $this->selectedApplicationId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.approval_failed'));
        }
    }

    /**
     * Reject a loan application
     */
    public function reject(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        $this->validate();

        if (! $this->selectedApplicationId || $this->approvalAction !== 'reject') {
            return;
        }

        try {
            $application = LoanApplication::findOrFail($this->selectedApplicationId);

            // Authorize the action
            Gate::authorize('approve', $application);

            // Reject the application
            $loanService->rejectApplication(
                $application,
                Auth::user(),
                $this->approvalRemarks,
                'portal'
            );

            // Send notification
            $notificationService->sendApprovalDecision($application, false, $this->approvalRemarks);

            session()->flash('success', __('staff.approvals.rejected_success'));
            $this->dispatch('announce', message: __('staff.approvals.rejected_success'));

            $this->closeApprovalModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            \Log::error('Failed to reject loan application', [
                'application_id' => $this->selectedApplicationId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.rejection_failed'));
        }
    }

    /**
     * Bulk approve selected applications.
     */
    public function bulkApprove(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        if (empty($this->selectedApplications)) {
            return;
        }

        try {
            $applications = LoanApplication::query()
                ->whereIn('id', $this->selectedApplications)
                ->get();

            foreach ($applications as $application) {
                Gate::authorize('approve', $application);

                $loanService->approveApplication(
                    $application,
                    Auth::user(),
                    $this->approvalRemarks,
                    'portal'
                );

                $notificationService->sendApprovalDecision($application, true, $this->approvalRemarks);
            }

            $this->selectedApplications = [];
            session()->flash('success', __('staff.approvals.approved_success'));
            $this->dispatch('announce', message: __('staff.approvals.approved_success'));
            $this->resetPage();
        } catch (\Throwable $e) {
            \Log::error('Failed bulk approval', [
                'application_ids' => $this->selectedApplications,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.approval_failed'));
        }
    }

    /**
     * Bulk reject selected applications.
     */
    public function bulkReject(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        if (empty($this->selectedApplications)) {
            return;
        }

        try {
            $applications = LoanApplication::query()
                ->whereIn('id', $this->selectedApplications)
                ->get();

            foreach ($applications as $application) {
                Gate::authorize('approve', $application);

                $loanService->rejectApplication(
                    $application,
                    Auth::user(),
                    $this->approvalRemarks,
                    'portal'
                );

                $notificationService->sendApprovalDecision($application, false, $this->approvalRemarks);
            }

            $this->selectedApplications = [];
            session()->flash('success', __('staff.approvals.rejected_success'));
            $this->dispatch('announce', message: __('staff.approvals.rejected_success'));
            $this->resetPage();
        } catch (\Throwable $e) {
            \Log::error('Failed bulk rejection', [
                'application_ids' => $this->selectedApplications,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.rejection_failed'));
        }
    }

    /**
     * Reset filters
     */
    public function resetFilters(): void
    {
        $this->statusFilter = 'pending';
        $this->applicantSearch = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.staff.approval-interface', [
            'applications' => $this->pendingApprovals,
        ])->layout('layouts.portal');
    }
}
