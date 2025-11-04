<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\DualApprovalService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalQueue extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public ?string $search = null;

    /**
     * @var array<int, string|null>
     */
    public array $remarks = [];

    protected $queryString = [
        'search' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        abort_unless(Auth::user()?->canApprove(), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function approve(int $applicationId, DualApprovalService $service): void
    {
        $this->handleDecision($applicationId, $service, approve: true);
    }

    public function decline(int $applicationId, DualApprovalService $service): void
    {
        $this->handleDecision($applicationId, $service, approve: false);
    }

    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $user = Auth::user();

        return LoanApplication::query()
            ->with(['division'])
            ->where('status', LoanStatus::UNDER_REVIEW)
            ->whereRaw('LOWER(approver_email) = ?', [strtolower((string) $user->email)])
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('application_number', 'like', '%'.$this->search.'%')
                        ->orWhere('applicant_name', 'like', '%'.$this->search.'%')
                        ->orWhere('purpose', 'like', '%'.$this->search.'%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.loans.approval-queue', [
            'applications' => $this->applications(),
        ])->layout('layouts.portal');
    }

    private function handleDecision(int $applicationId, DualApprovalService $service, bool $approve): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        $user = Auth::user();

        if (! $this->userCanActOn($application, $user?->email)) {
            abort(403);
        }

        $remarks = $this->remarks[$applicationId] ?? null;

        $response = $service->processPortalApproval(
            $application,
            $user,
            $approve,
            $remarks
        );

        if ($response['success'] ?? false) {
            session()->flash('message', $response['message'] ?? __('Tindakan berjaya.'));
            unset($this->remarks[$applicationId]);
            $this->resetPage();
        } else {
            session()->flash('error', $response['message'] ?? __('Tindakan gagal. Sila cuba lagi.'));
        }
    }

    private function userCanActOn(LoanApplication $application, ?string $email): bool
    {
        if ($application->status !== LoanStatus::UNDER_REVIEW) {
            return false;
        }

        return strtolower((string) $application->approver_email) === strtolower((string) $email);
    }
}
