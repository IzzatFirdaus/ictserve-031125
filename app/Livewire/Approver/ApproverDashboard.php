<?php

declare(strict_types=1);

namespace App\Livewire\Approver;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Component name: Approver Dashboard
 * Description: Interface for Grade 41+ officers to review and approve/reject loan applications
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-023 (Approval Workflow)
 * @trace D04 §5.4 (Approver Interface)
 */
class ApproverDashboard extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public ?string $search = null;

    #[Validate('nullable|string|max:50')]
    public ?string $status = 'pending'; // Default to pending approvals

    public ?LoanApplication $selectedApplication = null;

    #[Validate('required|string|min:5|max:1000')]
    public string $remarks = '';

    public bool $showApprovalModal = false;

    public bool $showRejectionModal = false;

    protected $queryString = [
        'search' => ['except' => null],
        'status' => ['except' => 'pending'],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        // Ensure user is eligible to be an approver (Grade 41+)
        $user = Auth::user();
        if (! $user || ! $user->grade || $user->grade->level < 41) {
            abort(403, 'Unauthorized. Only Grade 41+ officers can access this area.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function openApprovalModal(LoanApplication $application): void
    {
        $this->selectedApplication = $application;
        $this->remarks = '';
        $this->showApprovalModal = true;
    }

    public function openRejectionModal(LoanApplication $application): void
    {
        $this->selectedApplication = $application;
        $this->remarks = '';
        $this->showRejectionModal = true;
    }

    public function approve(LoanApplicationService $service): void
    {
        $this->validate([
            'remarks' => 'nullable|string|max:1000', // Remarks optional for approval
        ]);

        $service->approveApplication(
            $this->selectedApplication,
            Auth::user(),
            $this->remarks
        );

        $this->showApprovalModal = false;
        $this->selectedApplication = null;
        $this->remarks = '';

        session()->flash('message', __('loan.messages.application_approved'));
    }

    public function reject(LoanApplicationService $service): void
    {
        $this->validate([
            'remarks' => 'required|string|min:5|max:1000', // Remarks mandatory for rejection
        ]);

        $service->rejectApplication(
            $this->selectedApplication,
            Auth::user(),
            $this->remarks
        );

        $this->showRejectionModal = false;
        $this->selectedApplication = null;
        $this->remarks = '';

        session()->flash('message', __('loan.messages.application_rejected'));
    }

    public function render(): \Illuminate\View\View
    {
        $user = Auth::user();

        $applications = LoanApplication::query()
            ->where('approver_id', $user->id)
            ->when($this->status === 'pending', function ($query) {
                $query->where('status', LoanStatus::SUBMITTED);
            })
            ->when($this->status === 'history', function ($query) {
                $query->whereIn('status', [LoanStatus::APPROVED, LoanStatus::REJECTED]);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('application_number', 'like', '%'.$this->search.'%')
                        ->orWhere('applicant_name', 'like', '%'.$this->search.'%')
                        ->orWhere('purpose', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.approver.approver-dashboard', [
            'applications' => $applications,
        ])->layout('layouts.portal');
    }
}
