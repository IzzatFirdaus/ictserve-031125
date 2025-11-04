<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Services\LoanApplicationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class LoanHistory extends Component
{
    use WithPagination;

    #[Validate('nullable|string|max:255')]
    public ?string $search = null;

    #[Validate('nullable|string|max:50')]
    public ?string $status = null;

    protected $queryString = [
        'search' => ['except' => null],
        'status' => ['except' => null],
        'page' => ['except' => 1],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function claim(int $applicationId, LoanApplicationService $service): void
    {
        $application = LoanApplication::findOrFail($applicationId);
        $user = Auth::user();

        try {
            $service->claimGuestApplication($application, $user);
            session()->flash('message', __('Permohonan berjaya dituntut.'));
        } catch (\Throwable $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->resetPage();
    }

    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $user = Auth::user();

        return LoanApplication::query()
            ->with(['division'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($guest) use ($user) {
                        $guest->whereNull('user_id')
                            ->where('applicant_email', $user->email);
                    });
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('application_number', 'like', '%'.$this->search.'%')
                        ->orWhere('purpose', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(12);
    }

    public function statuses(): array
    {
        return collect(LoanStatus::cases())
            ->mapWithKeys(fn (LoanStatus $status) => [$status->value => $status->label()])
            ->prepend(__('Semua Status'), '')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.loans.loan-history');
    }
}
