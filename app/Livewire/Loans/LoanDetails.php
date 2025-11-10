<?php

declare(strict_types=1);

namespace App\Livewire\Loans;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use App\Services\LoanApplicationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoanDetails extends Component
{
    public LoanApplication $application;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $timeline = [];

    public function mount(LoanApplication $application): void
    {
        abort_unless($this->canAccess($application), 403);

        $this->application = $application->load([
            'division',
            'loanItems.asset',
            'transactions' => fn ($query) => $query->orderBy('processed_at'),
        ]);

        $this->timeline = $this->buildTimeline();
    }

    public function refreshApplication(): void
    {
        $this->application->refresh()->load([
            'division',
            'loanItems.asset',
            'transactions' => fn ($query) => $query->orderBy('processed_at'),
        ]);

        $this->timeline = $this->buildTimeline();
    }

    public function claim(LoanApplicationService $service): void
    {
        try {
            $service->claimGuestApplication($this->application, Auth::user());
            $this->refreshApplication();
            session()->flash('message', __('Permohonan berjaya dipautkan ke akaun anda.'));
        } catch (\Throwable $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    protected function canAccess(LoanApplication $application): bool
    {
        $user = Auth::user();

        // Check if user is the applicant
        if ($application->user_id === $user->id) {
            return true;
        }

        if (strtolower($application->applicant_email) === strtolower($user->email)) {
            return true;
        }

        // Check if user is the assigned approver
        if ($application->approver_email && strtolower($application->approver_email) === strtolower($user->email)) {
            return true;
        }

        return false;
    }

    protected function buildTimeline(): array
    {
        $transactions = $this->application->transactions;

        $issueTransaction = $transactions
            ->first(fn (LoanTransaction $transaction) => $transaction->isIssueTransaction());

        $returnTransaction = $transactions
            ->first(fn (LoanTransaction $transaction) => $transaction->isReturnTransaction());

        $status = $this->application->status instanceof LoanStatus
            ? $this->application->status
            : LoanStatus::tryFrom((string) $this->application->status);

        $events = collect([
            [
                'key' => LoanStatus::SUBMITTED->value,
                'label' => __('Permohonan Dihantar'),
                'description' => __('Permohonan sedang diproses oleh pasukan ICTServe.'),
                'timestamp' => $this->application->created_at,
            ],
            [
                'key' => LoanStatus::UNDER_REVIEW->value,
                'label' => __('Semakan Kelulusan'),
                'description' => __('Permohonan sedang disemak oleh pegawai kelulusan.'),
                'timestamp' => $status?->value === LoanStatus::UNDER_REVIEW->value ? $this->application->updated_at : $this->application->approved_at,
            ],
            [
                'key' => LoanStatus::APPROVED->value,
                'label' => __('Permohonan Diluluskan'),
                'description' => $this->application->approved_by_name
                    ? __('Diluluskan oleh :name.', ['name' => $this->application->approved_by_name])
                    : __('Permohonan diluluskan dan menunggu penyediaan aset.'),
                'timestamp' => $this->application->approved_at,
            ],
            [
                'key' => LoanStatus::ISSUED->value,
                'label' => __('Aset Dikeluarkan'),
                'description' => __('Aset telah diserahkan kepada pemohon.'),
                'timestamp' => $issueTransaction?->processed_at,
            ],
            [
                'key' => LoanStatus::RETURNING->value,
                'label' => __('Dalam Pemulangan'),
                'description' => __('Aset sedang dalam proses pemulangan.'),
                'timestamp' => $returnTransaction?->processed_at,
            ],
            [
                'key' => LoanStatus::COMPLETED->value,
                'label' => __('Permohonan Selesai'),
                'description' => __('Proses pinjaman selesai selepas pemeriksaan akhir.'),
                'timestamp' => $status?->value === LoanStatus::COMPLETED->value ? $this->application->updated_at : null,
            ],
        ])->filter(fn (array $event) => $event['timestamp'] !== null || $status?->value === $event['key']);

        return $events->map(function (array $event) use ($status): array {
            $rank = $this->statusRank($status?->value ?? LoanStatus::SUBMITTED->value);
            $eventRank = $this->statusRank($event['key']);

            return [
                'label' => $event['label'],
                'description' => $event['description'],
                'time' => $event['timestamp']?->translatedFormat('d M Y, h:i A'),
                'completed' => $rank > $eventRank,
                'current' => $rank === $eventRank,
            ];
        })->values()->all();
    }

    private function statusRank(string $status): int
    {
        return match ($status) {
            LoanStatus::SUBMITTED->value => 1,
            LoanStatus::UNDER_REVIEW->value,
            LoanStatus::PENDING_INFO->value => 2,
            LoanStatus::APPROVED->value,
            LoanStatus::READY_ISSUANCE->value => 3,
            LoanStatus::ISSUED->value,
            LoanStatus::IN_USE->value => 4,
            LoanStatus::RETURN_DUE->value,
            LoanStatus::RETURNING->value => 5,
            LoanStatus::RETURNED->value,
            LoanStatus::OVERDUE->value,
            LoanStatus::MAINTENANCE_REQUIRED->value => 6,
            LoanStatus::COMPLETED->value => 7,
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.loans.loan-details')->layout('layouts.portal');
    }
}
