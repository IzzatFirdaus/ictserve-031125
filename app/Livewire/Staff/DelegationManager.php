<?php

declare(strict_types=1);

namespace App\Livewire\Staff;

use App\Models\ApprovalDelegation;
use App\Services\DelegationService;
use App\Traits\OptimizedLivewireComponent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * DelegationManager Component
 *
 * Manages approval delegations for Grade 41+ approvers.
 * Allows creating, viewing, and deactivating temporary delegations.
 *
 * @property-read \Illuminate\Pagination\LengthAwarePaginator $delegations
 * @property-read \Illuminate\Database\Eloquent\Collection $availableApprovers
 * @property-read \Illuminate\Database\Eloquent\Collection $delegationsToMe
 */
class DelegationManager extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    // Form properties
    public int $delegated_approver_id = 0;

    public string $start_date = '';

    public string $end_date = '';

    public string $reason = '';

    // UI state
    public bool $showCreateModal = false;

    public bool $showConfirmDeactivate = false;

    public ?int $delegationToDeactivate = null;

    // Filters
    public string $status_filter = 'all';

    /**
     * @return array<string, string|array<string>>
     */
    protected function rules(): array
    {
        return [
            'delegated_approver_id' => ['required', 'integer', 'exists:users,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'delegated_approver_id.required' => __('delegation.validation.approver_required'),
            'delegated_approver_id.exists' => __('delegation.validation.approver_not_found'),
            'start_date.required' => __('delegation.validation.start_required'),
            'start_date.after_or_equal' => __('delegation.validation.start_not_past'),
            'end_date.required' => __('delegation.validation.end_required'),
            'end_date.after' => __('delegation.validation.end_after_start'),
            'reason.required' => __('delegation.validation.reason_required'),
            'reason.min' => __('delegation.validation.reason_min'),
            'reason.max' => __('delegation.validation.reason_max'),
        ];
    }

    public function mount(): void
    {
        // Set default dates
        $this->start_date = now()->addDay()->format('Y-m-d');
        $this->end_date = now()->addWeek()->format('Y-m-d');
    }

    #[Computed]
    public function delegations()
    {
        $query = ApprovalDelegation::query()
            ->where('original_approver_id', Auth::id())
            ->with(['delegatedApprover', 'createdBy']);

        // Apply status filter
        match ($this->status_filter) {
            'active' => $query->active(),
            'upcoming' => $query->upcoming(),
            'expired' => $query->expired(),
            'inactive' => $query->where('is_active', false),
            default => $query,
        };

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    #[Computed]
    public function availableApprovers()
    {
        $service = app(DelegationService::class);

        return $service->getAvailableApprovers(Auth::id());
    }

    #[Computed]
    public function delegationsToMe()
    {
        $service = app(DelegationService::class);

        return $service->getDelegationsToUser(Auth::id());
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createDelegation(): void
    {
        $this->validate();

        try {
            $service = app(DelegationService::class);

            $service->createDelegation(
                originalApproverId: Auth::id(),
                delegatedApproverId: $this->delegated_approver_id,
                startDate: Carbon::parse($this->start_date),
                endDate: Carbon::parse($this->end_date),
                reason: $this->reason,
                createdBy: Auth::id()
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('delegation.created_successfully'),
            ]);

            $this->closeCreateModal();
            unset($this->delegations);
        } catch (\InvalidArgumentException $e) {
            $this->addError('form', $e->getMessage());
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('delegation.creation_failed'),
            ]);
        }
    }

    public function confirmDeactivate(int $delegationId): void
    {
        $this->delegationToDeactivate = $delegationId;
        $this->showConfirmDeactivate = true;
    }

    public function cancelDeactivate(): void
    {
        $this->delegationToDeactivate = null;
        $this->showConfirmDeactivate = false;
    }

    public function deactivateDelegation(): void
    {
        if (! $this->delegationToDeactivate) {
            return;
        }

        try {
            $delegation = ApprovalDelegation::where('id', $this->delegationToDeactivate)
                ->where('original_approver_id', Auth::id())
                ->firstOrFail();

            $service = app(DelegationService::class);
            $service->deactivateDelegation($delegation);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('delegation.deactivated_successfully'),
            ]);

            $this->showConfirmDeactivate = false;
            $this->delegationToDeactivate = null;
            unset($this->delegations);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('delegation.deactivation_failed'),
            ]);
        }
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        unset($this->delegations);
    }

    private function resetForm(): void
    {
        $this->delegated_approver_id = 0;
        $this->start_date = now()->addDay()->format('Y-m-d');
        $this->end_date = now()->addWeek()->format('Y-m-d');
        $this->reason = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.staff.delegation-manager');
    }
}
