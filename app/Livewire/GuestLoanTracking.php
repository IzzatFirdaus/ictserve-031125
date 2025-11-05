<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * GuestLoanTracking Component
 *
 * Allows guest users to securely track loan application progress using
 * application number + email, presenting a bilingual timeline.
 *
 * @requirements 1.2, 1.4, 11.6, 21.5
 */
class GuestLoanTracking extends Component
{
    /**
     * Status progression order for timeline calculation.
     *
     * @var array<string, int>
     */
    private array $statusRank = [
        LoanStatus::SUBMITTED->value => 1,
        LoanStatus::UNDER_REVIEW->value => 2,
        LoanStatus::PENDING_INFO->value => 2,
        LoanStatus::APPROVED->value => 3,
        LoanStatus::READY_ISSUANCE->value => 3,
        LoanStatus::ISSUED->value => 4,
        LoanStatus::IN_USE->value => 4,
        LoanStatus::RETURN_DUE->value => 5,
        LoanStatus::RETURNING->value => 5,
        LoanStatus::RETURNED->value => 6,
        LoanStatus::COMPLETED->value => 7,
        LoanStatus::OVERDUE->value => 6,
        LoanStatus::MAINTENANCE_REQUIRED->value => 6,
    ];

    #[Validate('required|string|max:30')]
    public string $applicationNumber = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    public ?LoanApplication $application = null;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $timeline = [];

    public bool $showResults = false;

    public bool $notFound = false;

    public function mount(?string $applicationNumber = null, ?string $email = null): void
    {
        if ($applicationNumber) {
            $this->applicationNumber = $applicationNumber;
        }

        if ($email) {
            $this->email = $email;
        }

        if ($applicationNumber && $email) {
            $this->track();
        }
    }

    public function track(): void
    {
        $this->validate();
        $this->resetErrorBag();

        $application = LoanApplication::query()
            ->with(['division', 'loanItems.asset', 'transactions' => fn ($query) => $query->orderBy('processed_at')])
            ->where('application_number', strtoupper(Str::of($this->applicationNumber)->trim()->toString()))
            ->first();

        if (! $application || ! $this->canView($application)) {
            $this->application = null;
            $this->timeline = [];
            $this->showResults = false;
            $this->notFound = true;

            return;
        }

        $this->application = $application;
        $this->timeline = $this->buildTimeline($application);
        $this->showResults = true;
        $this->notFound = false;
    }

    protected function canView(LoanApplication $application): bool
    {
        $email = strtolower(trim($this->email));

        if ($application->user) {
            return strtolower($application->user->email) === $email
                || strtolower($application->applicant_email) === $email;
        }

        return strtolower($application->applicant_email) === $email;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function buildTimeline(LoanApplication $application): array
    {
        $transactions = $application->transactions;

        $issueTransaction = $transactions
            ->first(fn (LoanTransaction $transaction) => $transaction->isIssueTransaction());

        $returnTransaction = $transactions
            ->first(fn (LoanTransaction $transaction) => $transaction->isReturnTransaction());

        $status = $application->status instanceof LoanStatus
            ? $application->status
            : LoanStatus::tryFrom((string) $application->status);

        $events = collect([
            [
                'label' => __('Permohonan Dihantar / Submitted'),
                'description' => __('Permohonan telah diterima oleh pasukan ICTServe.'),
                'timestamp' => $application->created_at,
                'key' => LoanStatus::SUBMITTED->value,
            ],
            [
                'label' => __('Semakan & Kelulusan'),
                'description' => __('Permohonan sedang disemak oleh pegawai kelulusan.'),
                'timestamp' => $application->status === LoanStatus::UNDER_REVIEW ? $application->updated_at : $application->approved_at,
                'key' => LoanStatus::UNDER_REVIEW->value,
            ],
            [
                'label' => __('Diluluskan / Approved'),
                'description' => $application->approved_by_name
                    ? __('Diluluskan oleh :name', ['name' => $application->approved_by_name])
                    : __('Permohonan telah diluluskan.'),
                'timestamp' => $application->approved_at,
                'key' => LoanStatus::APPROVED->value,
            ],
            [
                'label' => __('Dikeluarkan / Issued'),
                'description' => __('Aset telah dikeluarkan kepada pemohon.'),
                'timestamp' => $issueTransaction?->processed_at,
                'key' => LoanStatus::ISSUED->value,
            ],
            [
                'label' => __('Pemulangan / Returning'),
                'description' => __('Aset dalam proses pemulangan.'),
                'timestamp' => $returnTransaction?->processed_at,
                'key' => LoanStatus::RETURNING->value,
            ],
            [
                'label' => __('Selesai / Completed'),
                'description' => __('Permohonan ditutup selepas pemeriksaan akhir.'),
                'timestamp' => $application->status === LoanStatus::COMPLETED ? $application->updated_at : null,
                'key' => LoanStatus::COMPLETED->value,
            ],
        ])->filter(fn (array $event) => $event['timestamp'] !== null || $status?->value === $event['key']);

        return $events->map(function (array $event) use ($status): array {
            $timestamp = $event['timestamp'];
            $isCompleted = $this->rank($status?->value ?? LoanStatus::SUBMITTED->value) >= $this->rank($event['key']);
            $isCurrent = $status?->value === $event['key'];

            return [
                'label' => $event['label'],
                'description' => $event['description'],
                'time' => $timestamp?->translatedFormat('d M Y, h:i A'),
                'completed' => $isCompleted,
                'current' => $isCurrent,
            ];
        })->values()->all();
    }

    private function rank(string $status): int
    {
        return $this->statusRank[$status] ?? 0;
    }

    public function render()
    {
        $layout = (auth()->check() || request()->routeIs('loan.authenticated.*'))
            ? 'layouts.portal'
            : 'layouts.front';

        return view('livewire.guest-loan-tracking')->layout($layout);
    }
}
