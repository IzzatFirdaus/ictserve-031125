<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\DelegationService;
use App\Services\LoanApplicationService;
use App\Services\NotificationService;
use App\Services\SlaMonitoringService;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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

    #[Validate('nullable|string|max:1000')]
    public string $approvalRemarks = '';

    /**
     * Array of selected application IDs for bulk operations
     *
     * @var array<int>
     */
    public array $selectedApplications = [];

    /**
     * Track optimistic UI state for applications being processed
     * Maps application ID to processing state ('approving', 'rejecting', null)
     *
     * @var array<int, string|null>
     */
    public array $processingApplications = [];

    /**
     * Track applications that have been optimistically updated
     * Used for rollback on failure
     *
     * @var array<int, string>
     */
    public array $optimisticUpdates = [];

    /**
     * SLA monitoring service instance
     */
    protected ?SlaMonitoringService $slaService = null;

    /**
     * Initialize component and verify authorization
     */
    public function mount(): void
    {
        // Verify user is Grade 41+ (Approver role)
        // Check role column attribute (same approach as middleware)
        $user = Auth::user();
        $allowedRoles = ['approver', 'admin', 'superuser'];

        if (! \in_array(strtolower($user->role ?? ''), $allowedRoles)) {
            abort(403, __('staff.approvals.unauthorized'));
        }

        // Initialize SLA monitoring service
        $this->slaService = app(SlaMonitoringService::class);
    }

    /**
     * Get SLA monitoring service instance
     */
    protected function getSlaService(): SlaMonitoringService
    {
        if ($this->slaService === null) {
            $this->slaService = app(SlaMonitoringService::class);
        }

        return $this->slaService;
    }

    /**
     * Get SLA status for a specific application
     *
     * @return array{status: string, hours_elapsed: float, hours_remaining: float|null, percentage: float}
     */
    public function getSlaStatus(LoanApplication $application): array
    {
        return $this->getSlaService()->getSlaStatus($application);
    }

    /**
     * Get SLA color class for styling
     */
    public function getSlaColorClass(string $status): string
    {
        return $this->getSlaService()->getSlaColorClass($status);
    }

    /**
     * Get SLA summary statistics for dashboard
     *
     * @return array{total_pending: int, ok: int, warning: int, critical: int, breached: int, compliance_rate: float}
     */
    #[Computed]
    public function slaSummary(): array
    {
        return $this->getSlaService()->getSlaSummary();
    }

    /**
     * Get pending loan applications for approval
     * Includes applications delegated to the current user
     */
    #[Computed]
    public function pendingApprovals()
    {
        $user = Auth::user();
        $userEmail = strtolower($user->email);

        // Get emails of users who have delegated to current user
        $delegatedEmails = $this->delegationsToMe->pluck('originalApprover.email')
            ->map(fn ($email) => strtolower($email))
            ->toArray();

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
            ->where(function ($q) use ($userEmail, $delegatedEmails) {
                // Include applications assigned to current user
                $q->whereRaw('LOWER(approver_email) = ?', [$userEmail]);

                // Include applications delegated to current user
                if (! empty($delegatedEmails)) {
                    $q->orWhereIn(DB::raw('LOWER(approver_email)'), $delegatedEmails);
                }
            })
            ->with(['user'])
            ->latest()
            ->paginate(10);
    }

    /**
     * Get active delegations where current user is the delegated approver
     */
    #[Computed]
    public function delegationsToMe()
    {
        $delegationService = app(DelegationService::class);

        return $delegationService->getDelegationsToUser(Auth::id());
    }

    /**
     * Check if an application is delegated (not directly assigned to current user)
     */
    public function isDelegatedApplication(LoanApplication $application): bool
    {
        $userEmail = strtolower(Auth::user()->email);
        $approverEmail = strtolower($application->approver_email ?? '');

        return $approverEmail !== $userEmail;
    }

    /**
     * Get the original approver name for a delegated application
     */
    public function getOriginalApproverName(LoanApplication $application): ?string
    {
        if (! $this->isDelegatedApplication($application)) {
            return null;
        }

        $delegation = $this->delegationsToMe->first(function ($d) use ($application) {
            return strtolower($d->originalApprover->email) === strtolower($application->approver_email ?? '');
        });

        return $delegation?->originalApprover?->name;
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
     * Approve a loan application with optimistic UI
     *
     * Provides immediate visual feedback while server processes the approval.
     * Rolls back UI state on failure.
     */
    public function approve(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        $this->validate();

        if (! $this->selectedApplicationId || $this->approvalAction !== 'approve') {
            return;
        }

        $applicationId = $this->selectedApplicationId;

        // Optimistic UI: Mark as processing immediately
        $this->processingApplications[$applicationId] = 'approving';
        $this->optimisticUpdates[$applicationId] = 'approved';

        // Dispatch optimistic update event for immediate UI feedback
        $this->dispatch('optimistic-update', applicationId: $applicationId, status: 'approved');

        try {
            $application = LoanApplication::findOrFail($applicationId);

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

            // Clear processing state on success
            unset($this->processingApplications[$applicationId]);
            unset($this->optimisticUpdates[$applicationId]);

            session()->flash('success', __('staff.approvals.approved_success'));
            $this->dispatch('announce', message: __('staff.approvals.approved_success'));
            $this->dispatch('approval-success', applicationId: $applicationId, action: 'approved');

            $this->closeApprovalModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            // Rollback optimistic update on failure
            unset($this->processingApplications[$applicationId]);
            unset($this->optimisticUpdates[$applicationId]);

            // Dispatch rollback event
            $this->dispatch('optimistic-rollback', applicationId: $applicationId);

            Log::error('Failed to approve loan application', [
                'application_id' => $applicationId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.approval_failed'));
        }
    }

    /**
     * Reject a loan application with optimistic UI
     *
     * Provides immediate visual feedback while server processes the rejection.
     * Rolls back UI state on failure.
     */
    public function reject(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        $this->validate();

        if (! $this->selectedApplicationId || $this->approvalAction !== 'reject') {
            return;
        }

        $applicationId = $this->selectedApplicationId;

        // Optimistic UI: Mark as processing immediately
        $this->processingApplications[$applicationId] = 'rejecting';
        $this->optimisticUpdates[$applicationId] = 'rejected';

        // Dispatch optimistic update event for immediate UI feedback
        $this->dispatch('optimistic-update', applicationId: $applicationId, status: 'rejected');

        try {
            $application = LoanApplication::findOrFail($applicationId);

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

            // Clear processing state on success
            unset($this->processingApplications[$applicationId]);
            unset($this->optimisticUpdates[$applicationId]);

            session()->flash('success', __('staff.approvals.rejected_success'));
            $this->dispatch('announce', message: __('staff.approvals.rejected_success'));
            $this->dispatch('approval-success', applicationId: $applicationId, action: 'rejected');

            $this->closeApprovalModal();
            $this->resetPage();
        } catch (\Throwable $e) {
            // Rollback optimistic update on failure
            unset($this->processingApplications[$applicationId]);
            unset($this->optimisticUpdates[$applicationId]);

            // Dispatch rollback event
            $this->dispatch('optimistic-rollback', applicationId: $applicationId);

            Log::error('Failed to reject loan application', [
                'application_id' => $applicationId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('approval', __('staff.approvals.rejection_failed'));
        }
    }

    /**
     * Toggle selection of an application for bulk operations
     *
     * @param  int  $applicationId  The ID of the application to toggle
     */
    public function selectApplication(int $applicationId): void
    {
        if (in_array($applicationId, $this->selectedApplications)) {
            // Remove if already selected
            $this->selectedApplications = array_values(
                array_filter($this->selectedApplications, fn ($id) => $id !== $applicationId)
            );
        } else {
            // Add if not selected
            $this->selectedApplications[] = $applicationId;
        }
    }

    /**
     * Bulk approve selected applications
     */
    public function bulkApprove(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        if (empty($this->selectedApplications)) {
            $this->addError('bulk', __('staff.approvals.no_selection'));

            return;
        }

        try {
            $successCount = 0;
            $failedCount = 0;

            foreach ($this->selectedApplications as $applicationId) {
                try {
                    $application = LoanApplication::findOrFail($applicationId);

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

                    $successCount++;
                } catch (\Throwable $e) {
                    Log::error('Failed to bulk approve loan application', [
                        'application_id' => $applicationId,
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage(),
                    ]);
                    $failedCount++;
                }
            }

            if ($successCount > 0) {
                session()->flash('success', __('staff.approvals.bulk_approved_success', ['count' => $successCount]));
                $this->dispatch('announce', message: __('staff.approvals.bulk_approved_success', ['count' => $successCount]));
            }

            if ($failedCount > 0) {
                $this->addError('bulk', __('staff.approvals.bulk_approval_failed', ['count' => $failedCount]));
            }

            $this->selectedApplications = [];
            $this->approvalRemarks = '';
            $this->resetPage();
        } catch (\Throwable $e) {
            Log::error('Failed to execute bulk approval', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('bulk', __('staff.approvals.bulk_operation_failed'));
        }
    }

    /**
     * Bulk reject selected applications
     */
    public function bulkReject(LoanApplicationService $loanService, NotificationService $notificationService): void
    {
        if (empty($this->selectedApplications)) {
            $this->addError('bulk', __('staff.approvals.no_selection'));

            return;
        }

        try {
            $successCount = 0;
            $failedCount = 0;

            foreach ($this->selectedApplications as $applicationId) {
                try {
                    $application = LoanApplication::findOrFail($applicationId);

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

                    $successCount++;
                } catch (\Throwable $e) {
                    Log::error('Failed to bulk reject loan application', [
                        'application_id' => $applicationId,
                        'user_id' => Auth::id(),
                        'error' => $e->getMessage(),
                    ]);
                    $failedCount++;
                }
            }

            if ($successCount > 0) {
                session()->flash('success', __('staff.approvals.bulk_rejected_success', ['count' => $successCount]));
                $this->dispatch('announce', message: __('staff.approvals.bulk_rejected_success', ['count' => $successCount]));
            }

            if ($failedCount > 0) {
                $this->addError('bulk', __('staff.approvals.bulk_rejection_failed', ['count' => $failedCount]));
            }

            $this->selectedApplications = [];
            $this->approvalRemarks = '';
            $this->resetPage();
        } catch (\Throwable $e) {
            Log::error('Failed to execute bulk rejection', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            $this->addError('bulk', __('staff.approvals.bulk_operation_failed'));
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
