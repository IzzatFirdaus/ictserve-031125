<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Guest Helpdesk Ticket Form - Multi-step Wizard with Optimistic UI
 *
 * ISO Compliance: PK.(S).MOTAC.07.(L1)
 *
 * Features:
 * - Multi-step wizard with progress indicators
 * - Real-time validation (300ms debounce)
 * - File upload with drag-and-drop (max 5 files)
 * - Searchable division select
 * - Mandatory declaration with exact legacy text
 * - Email confirmation within 60 seconds
 * - Rate limiting and CSRF protection
 * - **Optimistic UI**: Immediate feedback with rollback on error
 *
 * Optimistic UI Pattern:
 * 1. User clicks submit → Immediate success state shown
 * 2. Server processes in background → Email queued
 * 3. On success → Update with actual ticket number
 * 4. On failure → Rollback to form state with error message
 *
 * @trace D03-FR-011, D12-§9, D14-§2.2
 *
 * @requirements 1.1, 1.2, 9.1, 12.3, 13.6, R09 (Optimistic UI)
 */
class GuestTicketForm extends Component
{
    use WithFileUploads;

    // Form Data
    #[Validate('required|string|max:255')]
    public string $guest_name = '';

    #[Validate('required|email|max:255')]
    public string $guest_email = '';

    #[Validate('required|string|max:20')]
    public string $guest_phone = '';

    #[Validate('nullable|string|max:50')]
    public string $guest_staff_id = '';

    #[Validate('required')]
    public ?int $division_id = null;

    #[Validate('required|string|max:50')]
    public string $job_grade = '';

    #[Validate('required')]
    public ?int $category_id = null;

    #[Validate('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    #[Validate('nullable|array|max:5')]
    public array $attachments = [];

    #[Validate('accepted')]
    public bool $declaration_accepted = false;

    // Wizard State
    public int $currentStep = 1;

    public int $totalSteps = 3;

    // UI State - Enhanced for Optimistic UI
    public bool $isSubmitting = false;

    public bool $submitted = false;

    public bool $submissionFailed = false;

    public ?string $ticketNumber = null;

    public ?string $errorMessage = null;

    public string $divisionSearch = '';

    // Optimistic UI State
    public string $optimisticTicketNumber = '';

    public bool $isOptimisticState = false;

    /**
     * Initialize component with optional pre-filled category
     *
     * Supports Service Request routing (Task 3.3.8):
     * - ?category=SERVICE_REQUEST pre-fills the category for "Permintaan Perkhidmatan"
     * - ?category=GENERAL pre-fills for general enquiries
     *
     * @param  string|null  $category  Category code to pre-fill (e.g., 'SERVICE_REQUEST', 'GENERAL')
     */
    public function mount(?string $category = null): void
    {
        if ($category !== null) {
            $ticketCategory = TicketCategory::where('code', strtoupper($category))->first();
            if ($ticketCategory) {
                $this->category_id = $ticketCategory->id;
            }
        }
    }

    /**
     * Get all divisions for the select dropdown
     */
    #[Computed]
    public function divisions()
    {
        return Division::query()
            ->when($this->divisionSearch, function ($query) {
                $query->where('name', 'like', '%'.$this->divisionSearch.'%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all ticket categories
     */
    #[Computed]
    public function categories()
    {
        return TicketCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Move to next step with validation
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    /**
     * Move to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Validate only the current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
                'guest_staff_id' => 'nullable|string|max:50',
                'division_id' => 'required',
                'job_grade' => 'required|string|max:50',
            ]),
            2 => $this->validate([
                'category_id' => 'required',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10|max:5000',
                'priority' => 'required|in:low,normal,high,urgent',
            ]),
            3 => $this->validate([
                'declaration_accepted' => 'accepted',
            ]),
            default => null,
        };
    }

    /**
     * Submit the helpdesk ticket with Optimistic UI
     *
     * Optimistic UI Flow:
     * 1. Validate form data
     * 2. Generate optimistic ticket number immediately
     * 3. Dispatch optimistic success event (UI shows success)
     * 4. Process server-side operations
     * 5. On success: Dispatch final success with actual ticket number
     * 6. On failure: Dispatch rollback event (UI returns to form)
     */
    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->submissionFailed = false;
        $this->errorMessage = null;

        try {
            // Step 1: Validate form data BEFORE optimistic state
            $this->validate();

            // Step 2: Generate optimistic ticket number immediately
            $this->optimisticTicketNumber = $this->generateOptimisticTicketNumber();

            // Step 3: Enter optimistic state - show success immediately
            $this->isOptimisticState = true;
            $this->submitted = true;
            $this->ticketNumber = $this->optimisticTicketNumber;

            // Dispatch optimistic success event for Alpine.js
            $this->dispatch('optimistic-submission-started', [
                'ticketNumber' => $this->optimisticTicketNumber,
                'email' => $this->guest_email,
            ]);

            // Step 4: Process server-side operations
            $ticket = HelpdeskTicket::create([
                'ticket_number' => HelpdeskTicket::generateTicketNumber(),
                'guest_name' => $this->guest_name,
                'guest_email' => $this->guest_email,
                'guest_phone' => $this->guest_phone,
                'guest_staff_id' => $this->guest_staff_id ?: null,
                'division_id' => $this->division_id,
                'job_grade' => $this->job_grade,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'subject' => $this->subject,
                'description' => $this->description,
                'status' => 'open',
                'declaration_accepted' => $this->declaration_accepted,
            ]);

            // Handle file uploads if any
            if (! empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('helpdesk-attachments/'.$ticket->ticket_number, 'public');

                    $ticket->attachments()->create([
                        'filename' => $attachment->getClientOriginalName(),
                        'path' => $path,
                        'size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            // Calculate SLA due dates
            $ticket->calculateSLADueDates();

            // Send email confirmation (async queue - 60 second SLA)
            Mail::to($this->guest_email)->queue(
                new \App\Mail\HelpdeskTicketCreated($ticket)
            );

            // Step 5: Update with actual ticket number
            $this->ticketNumber = $ticket->ticket_number;
            $this->isOptimisticState = false;

            // Dispatch final success event
            $this->dispatch('submission-confirmed', [
                'ticketNumber' => $ticket->ticket_number,
                'ticketId' => $ticket->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors - rollback optimistic state
            $this->rollbackOptimisticState(__('Please correct the errors and try again.'));
            throw $e; // Re-throw to show validation errors
        } catch (\Exception $e) {
            // Server error - rollback optimistic state
            $this->rollbackOptimisticState(__('An error occurred while submitting your ticket. Please try again.'));

            logger()->error('Guest ticket submission failed', [
                'error' => $e->getMessage(),
                'email' => $this->guest_email,
                'optimistic_ticket' => $this->optimisticTicketNumber,
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Rollback optimistic state on error
     */
    protected function rollbackOptimisticState(string $message): void
    {
        $this->submitted = false;
        $this->submissionFailed = true;
        $this->isOptimisticState = false;
        $this->errorMessage = $message;
        $this->ticketNumber = null;
        $this->optimisticTicketNumber = '';

        // Dispatch rollback event for Alpine.js
        $this->dispatch('submission-rollback', [
            'message' => $message,
        ]);
    }

    /**
     * Generate optimistic ticket number for immediate display
     * Format: HD[YYYY][MM][DD]-[RANDOM]
     */
    protected function generateOptimisticTicketNumber(): string
    {
        return 'HD'.date('Ymd').'-'.strtoupper(substr(md5((string) microtime(true)), 0, 6));
    }

    /**
     * Retry submission after failure
     */
    public function retrySubmission(): void
    {
        $this->submissionFailed = false;
        $this->errorMessage = null;
        $this->submit();
    }

    /**
     * Reset and start new submission
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->currentStep = 1;
        $this->submissionFailed = false;
        $this->errorMessage = null;
        $this->isOptimisticState = false;
        $this->optimisticTicketNumber = '';
    }

    public function render()
    {
        return view('livewire.helpdesk.guest-ticket-form')->layout('layouts.front');
    }
}
