<?php

declare(strict_types=1);

namespace App\Livewire\Status;

use App\Contracts\TokenServiceInterface;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Traits\OptimizedLivewireComponent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * StatusChecker Component
 *
 * Provides token-based status lookup for both helpdesk tickets and loan applications.
 * Supports the True Hybrid Architecture by allowing guest users to check status
 * without authentication using SHA-512 hashed tokens.
 *
 * Features:
 * - Token-based lookup for tickets and loans
 * - Display status, timeline, public comments
 * - Bilingual error messages (BM/EN) for invalid tokens
 * - WCAG 2.2 AA compliant interface
 *
 * @see D03 SRS-HELP-004 Status token requirements
 * @see D03 SRS-LOAN-004 Approval token requirements
 *
 * @requirements 2.1, 2.2
 */
#[Layout('layouts.front')]
class StatusChecker extends Component
{
    use OptimizedLivewireComponent;

    /**
     * The status token entered by the user
     */
    #[Validate('required|string|min:32|max:128')]
    public string $token = '';

    /**
     * The type of submission to look up (auto-detected or user-selected)
     */
    #[Validate('nullable|in:ticket,loan,auto')]
    public string $type = 'auto';

    /**
     * The found model (HelpdeskTicket or LoanApplication)
     */
    public ?Model $submission = null;

    /**
     * The type of submission found
     */
    public ?string $foundType = null;

    /**
     * Timeline events for the submission
     *
     * @var array<int, array<string, mixed>>
     */
    public array $timeline = [];

    /**
     * Public comments for the submission
     *
     * @var array<int, array<string, mixed>>
     */
    public array $publicComments = [];

    /**
     * Whether the search returned no results
     */
    public bool $notFound = false;

    /**
     * Whether to show the results section
     */
    public bool $showResults = false;

    /**
     * Error message for invalid tokens
     */
    public string $errorMessage = '';

    /**
     * Token service for validation
     */
    protected TokenServiceInterface $tokenService;

    /**
     * Boot the component with dependencies
     */
    public function boot(TokenServiceInterface $tokenService): void
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Mount the component with optional pre-filled token
     */
    public function mount(?string $token = null, ?string $type = null): void
    {
        if ($token) {
            $this->token = $token;
        }

        if ($type && in_array($type, ['ticket', 'loan', 'auto'])) {
            $this->type = $type;
        }

        // Auto-search if token is provided
        if ($token) {
            $this->checkStatus();
        }
    }

    /**
     * Define relationships to eager load for N+1 prevention
     *
     * @return array<int, string>
     */
    protected function getEagerLoadRelationships(): array
    {
        return ['user', 'division'];
    }

    /**
     * Check status using the provided token
     */
    public function checkStatus(): void
    {
        $this->validate();
        $this->resetState();

        $token = trim($this->token);

        // Try to find the submission based on type
        if ($this->type === 'auto' || $this->type === 'ticket') {
            $ticket = $this->tokenService->validateStatusToken($token, 'ticket');
            if ($ticket instanceof HelpdeskTicket) {
                $this->handleTicketFound($ticket);

                return;
            }
        }

        if ($this->type === 'auto' || $this->type === 'loan') {
            $loan = $this->tokenService->validateStatusToken($token, 'loan');
            if ($loan instanceof LoanApplication) {
                $this->handleLoanFound($loan);

                return;
            }
        }

        // No submission found
        $this->handleNotFound();
    }

    /**
     * Reset the component state
     */
    protected function resetState(): void
    {
        $this->submission = null;
        $this->foundType = null;
        $this->timeline = [];
        $this->publicComments = [];
        $this->notFound = false;
        $this->showResults = false;
        $this->errorMessage = '';
        $this->resetErrorBag();
    }

    /**
     * Handle when a ticket is found
     */
    protected function handleTicketFound(HelpdeskTicket $ticket): void
    {
        // Eager load relationships
        $ticket->load(['category', 'division', 'assignedUser', 'comments' => function ($query): void {
            $query->where('is_public', true)->orderBy('created_at', 'desc');
        }]);

        $this->submission = $ticket;
        $this->foundType = 'ticket';
        $this->timeline = $this->buildTicketTimeline($ticket);
        $this->publicComments = $this->buildTicketComments($ticket);
        $this->showResults = true;
    }

    /**
     * Handle when a loan application is found
     */
    protected function handleLoanFound(LoanApplication $loan): void
    {
        // Eager load relationships
        $loan->load(['division', 'loanItems.asset', 'activities' => function ($query): void {
            $query->where('is_public', true)->orderBy('created_at', 'desc')->limit(10);
        }]);

        $this->submission = $loan;
        $this->foundType = 'loan';
        $this->timeline = $this->buildLoanTimeline($loan);
        $this->publicComments = $this->buildLoanComments($loan);
        $this->showResults = true;
    }

    /**
     * Handle when no submission is found
     */
    protected function handleNotFound(): void
    {
        $this->notFound = true;
        $this->showResults = false;
        $this->errorMessage = __('status.token_invalid');
    }

    /**
     * Build timeline for helpdesk ticket
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildTicketTimeline(HelpdeskTicket $ticket): array
    {
        $events = collect([
            [
                'key' => 'submitted',
                'label_ms' => 'Tiket Dihantar',
                'label_en' => 'Ticket Submitted',
                'timestamp' => $ticket->created_at,
                'description_ms' => 'Permohonan berjaya diterima oleh pasukan ICTServe.',
                'description_en' => 'Request successfully received by ICTServe team.',
                'icon' => 'heroicon-o-document-plus',
            ],
            [
                'key' => 'assigned',
                'label_ms' => 'Tiket Ditugaskan',
                'label_en' => 'Ticket Assigned',
                'timestamp' => $ticket->assigned_at,
                'description_ms' => $ticket->assignedUser
                    ? "Ditugaskan kepada {$ticket->assignedUser->name}"
                    : 'Sedang ditugaskan kepada pegawai bertugas.',
                'description_en' => $ticket->assignedUser
                    ? "Assigned to {$ticket->assignedUser->name}"
                    : 'Being assigned to duty officer.',
                'icon' => 'heroicon-o-user-plus',
            ],
            [
                'key' => 'in_progress',
                'label_ms' => 'Dalam Proses',
                'label_en' => 'In Progress',
                'timestamp' => $ticket->responded_at,
                'description_ms' => 'Pegawai ICT sedang menyelesaikan isu anda.',
                'description_en' => 'ICT officer is working on your issue.',
                'icon' => 'heroicon-o-cog-6-tooth',
            ],
            [
                'key' => 'resolved',
                'label_ms' => 'Selesai',
                'label_en' => 'Resolved',
                'timestamp' => $ticket->resolved_at,
                'description_ms' => $ticket->resolution_notes ?? 'Isu telah diselesaikan.',
                'description_en' => $ticket->resolution_notes ?? 'Issue has been resolved.',
                'icon' => 'heroicon-o-check-circle',
            ],
            [
                'key' => 'closed',
                'label_ms' => 'Ditutup',
                'label_en' => 'Closed',
                'timestamp' => $ticket->closed_at,
                'description_ms' => 'Tiket ditutup selepas pengesahan.',
                'description_en' => 'Ticket closed after confirmation.',
                'icon' => 'heroicon-o-lock-closed',
            ],
        ]);

        return $events->map(function (array $event) use ($ticket) {
            $timestamp = $event['timestamp'];
            $time = $timestamp instanceof Carbon ? $timestamp->translatedFormat('d M Y, h:i A') : null;

            return [
                'label' => $event['label_ms'].' / '.$event['label_en'],
                'description' => app()->getLocale() === 'ms' ? $event['description_ms'] : $event['description_en'],
                'completed' => $timestamp instanceof Carbon,
                'time' => $time,
                'current' => $this->isCurrentTicketStage($ticket, $event['key']),
                'icon' => $event['icon'],
            ];
        })->values()->all();
    }

    /**
     * Build timeline for loan application
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildLoanTimeline(LoanApplication $loan): array
    {
        $events = collect([
            [
                'key' => 'submitted',
                'label_ms' => 'Permohonan Dihantar',
                'label_en' => 'Application Submitted',
                'timestamp' => $loan->created_at,
                'description_ms' => 'Permohonan pinjaman berjaya dihantar.',
                'description_en' => 'Loan application successfully submitted.',
                'icon' => 'heroicon-o-document-plus',
            ],
            [
                'key' => 'pending_approval',
                'label_ms' => 'Menunggu Kelulusan',
                'label_en' => 'Pending Approval',
                'timestamp' => $loan->created_at,
                'description_ms' => 'Permohonan sedang menunggu kelulusan penyelia.',
                'description_en' => 'Application awaiting supervisor approval.',
                'icon' => 'heroicon-o-clock',
            ],
            [
                'key' => 'approved',
                'label_ms' => 'Diluluskan',
                'label_en' => 'Approved',
                'timestamp' => $loan->approved_at,
                'description_ms' => $loan->approved_by_name
                    ? "Diluluskan oleh {$loan->approved_by_name}"
                    : 'Permohonan telah diluluskan.',
                'description_en' => $loan->approved_by_name
                    ? "Approved by {$loan->approved_by_name}"
                    : 'Application has been approved.',
                'icon' => 'heroicon-o-check-badge',
            ],
            [
                'key' => 'ready_for_pickup',
                'label_ms' => 'Sedia Untuk Diambil',
                'label_en' => 'Ready for Pickup',
                'timestamp' => $loan->status->value === 'ready_for_pickup' ? now() : null,
                'description_ms' => 'Peralatan sedia untuk diambil di BPM.',
                'description_en' => 'Equipment ready for pickup at BPM.',
                'icon' => 'heroicon-o-inbox-arrow-down',
            ],
            [
                'key' => 'in_use',
                'label_ms' => 'Sedang Digunakan',
                'label_en' => 'In Use',
                'timestamp' => $loan->status->value === 'in_use' ? now() : null,
                'description_ms' => 'Peralatan sedang dipinjam.',
                'description_en' => 'Equipment currently on loan.',
                'icon' => 'heroicon-o-computer-desktop',
            ],
            [
                'key' => 'returned',
                'label_ms' => 'Dipulangkan',
                'label_en' => 'Returned',
                'timestamp' => $loan->status->value === 'returned' ? now() : null,
                'description_ms' => 'Peralatan telah dipulangkan.',
                'description_en' => 'Equipment has been returned.',
                'icon' => 'heroicon-o-arrow-uturn-left',
            ],
        ]);

        return $events->map(function (array $event) use ($loan) {
            $timestamp = $event['timestamp'];
            $time = $timestamp instanceof Carbon ? $timestamp->translatedFormat('d M Y, h:i A') : null;

            return [
                'label' => $event['label_ms'].' / '.$event['label_en'],
                'description' => app()->getLocale() === 'ms' ? $event['description_ms'] : $event['description_en'],
                'completed' => $this->isLoanStageCompleted($loan, $event['key']),
                'time' => $time,
                'current' => $this->isCurrentLoanStage($loan, $event['key']),
                'icon' => $event['icon'],
            ];
        })->values()->all();
    }

    /**
     * Build public comments for ticket
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildTicketComments(HelpdeskTicket $ticket): array
    {
        return $ticket->comments
            ->where('is_public', true)
            ->map(fn ($comment) => [
                'author' => $comment->user?->name ?? __('status.system'),
                'content' => $comment->content,
                'created_at' => $comment->created_at->translatedFormat('d M Y, h:i A'),
            ])
            ->values()
            ->all();
    }

    /**
     * Build public comments/activities for loan
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildLoanComments(LoanApplication $loan): array
    {
        return $loan->activities
            ->where('is_public', true)
            ->map(fn ($activity) => [
                'author' => $activity->causer?->name ?? __('status.system'),
                'content' => $activity->description,
                'created_at' => $activity->created_at->translatedFormat('d M Y, h:i A'),
            ])
            ->values()
            ->all();
    }

    /**
     * Check if the given stage is the current stage for a ticket
     */
    protected function isCurrentTicketStage(HelpdeskTicket $ticket, string $key): bool
    {
        return match ($key) {
            'submitted' => in_array($ticket->status, ['open', 'new']),
            'assigned' => $ticket->status === 'assigned',
            'in_progress' => $ticket->status === 'in_progress',
            'resolved' => $ticket->status === 'resolved',
            'closed' => $ticket->status === 'closed',
            default => false,
        };
    }

    /**
     * Check if the given stage is the current stage for a loan
     */
    protected function isCurrentLoanStage(LoanApplication $loan, string $key): bool
    {
        $status = $loan->status->value;

        return match ($key) {
            'submitted' => $status === 'draft',
            'pending_approval' => in_array($status, ['pending_supervisor_approval', 'under_review']),
            'approved' => $status === 'approved',
            'ready_for_pickup' => $status === 'ready_for_pickup',
            'in_use' => $status === 'in_use',
            'returned' => $status === 'returned',
            default => false,
        };
    }

    /**
     * Check if a loan stage has been completed
     */
    protected function isLoanStageCompleted(LoanApplication $loan, string $key): bool
    {
        $statusOrder = [
            'draft' => 1,
            'pending_supervisor_approval' => 2,
            'under_review' => 2,
            'approved' => 3,
            'ready_for_pickup' => 4,
            'in_use' => 5,
            'returned' => 6,
        ];

        $stageOrder = [
            'submitted' => 1,
            'pending_approval' => 2,
            'approved' => 3,
            'ready_for_pickup' => 4,
            'in_use' => 5,
            'returned' => 6,
        ];

        $currentOrder = $statusOrder[$loan->status->value] ?? 0;
        $stageOrderValue = $stageOrder[$key] ?? 0;

        return $currentOrder >= $stageOrderValue;
    }

    /**
     * Clear the search and reset the form
     */
    public function clearSearch(): void
    {
        $this->token = '';
        $this->type = 'auto';
        $this->resetState();
    }

    /**
     * Render the component
     */
    #[\Livewire\Attributes\Layout('layouts.guest')]
    public function render(): \Illuminate\View\View: View
    {
        return view('livewire.status.status-checker');
    }
}
