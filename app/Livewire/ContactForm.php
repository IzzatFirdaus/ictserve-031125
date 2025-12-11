<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Contact Form Component - Routes submissions to Helpdesk as "General Enquiry" tickets
 *
 * This component implements Task 3.3.6 from the updated-frontend spec:
 * - Routes "Hantar Mesej Kepada Kami" submissions to Helpdesk module
 * - Creates tracked tickets instead of sending emails to potentially ignored inboxes
 * - Displays generated Ticket ID for user tracking (Task 3.3.7)
 *
 * Features:
 * - Real-time validation with 300ms debounce
 * - Optimistic UI for immediate feedback
 * - Email confirmation within 60 seconds
 * - Rate limiting and CSRF protection
 * - Bilingual support (Bahasa Melayu/English)
 *
 * @trace D03-FR-021, D12-§9, R21 (Contact Form Integration)
 *
 * @requirements R21.1, R21.2, R21.3, R21.4, R21.5
 */
class ContactForm extends Component
{
    // Form Data
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $message = '';

    // UI State
    public bool $isSubmitting = false;

    public bool $submitted = false;

    public bool $submissionFailed = false;

    public ?string $ticketNumber = null;

    public ?string $errorMessage = null;

    // Optimistic UI State
    public string $optimisticTicketNumber = '';

    public bool $isOptimisticState = false;

    /**
     * Submit the contact form as a Helpdesk ticket
     *
     * Creates a ticket with "GENERAL" category (Lain-lain / Others)
     * which serves as the "General Enquiry" category for contact submissions.
     */
    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->submissionFailed = false;
        $this->errorMessage = null;

        try {
            // Step 1: Validate form data
            $this->validate();

            // Step 2: Generate optimistic ticket number immediately
            $this->optimisticTicketNumber = $this->generateOptimisticTicketNumber();

            // Step 3: Enter optimistic state - show success immediately
            $this->isOptimisticState = true;
            $this->submitted = true;
            $this->ticketNumber = $this->optimisticTicketNumber;

            // Dispatch optimistic success event
            $this->dispatch('contact-submission-started', [
                'ticketNumber' => $this->optimisticTicketNumber,
                'email' => $this->email,
            ]);

            // Step 4: Get or create the "GENERAL" category for contact submissions
            $generalCategory = TicketCategory::where('code', 'GENERAL')->first();

            // Step 5: Create the helpdesk ticket
            $ticket = HelpdeskTicket::create([
                'ticket_number' => HelpdeskTicket::generateTicketNumber(),
                'guest_name' => $this->name,
                'guest_email' => $this->email,
                'guest_phone' => $this->phone ?: null,
                'category_id' => $generalCategory?->id,
                'priority' => 'normal',
                'subject' => $this->subject,
                'description' => $this->formatContactMessage(),
                'status' => 'open',
                'declaration_accepted' => true, // Contact form implies acceptance
            ]);

            // Calculate SLA due dates based on category
            $ticket->calculateSLADueDates();

            // Send email confirmation (async queue - 60 second SLA)
            Mail::to($this->email)->queue(
                new \App\Mail\ContactFormSubmitted($ticket)
            );

            // Step 6: Update with actual ticket number
            $this->ticketNumber = $ticket->ticket_number;
            $this->isOptimisticState = false;

            // Dispatch final success event
            $this->dispatch('contact-submission-confirmed', [
                'ticketNumber' => $ticket->ticket_number,
                'ticketId' => $ticket->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->rollbackOptimisticState(__('Please correct the errors and try again.'));
            throw $e;
        } catch (\Exception $e) {
            $this->rollbackOptimisticState(__('An error occurred while submitting your message. Please try again.'));

            logger()->error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'email' => $this->email,
                'optimistic_ticket' => $this->optimisticTicketNumber,
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Format the contact message with metadata
     */
    protected function formatContactMessage(): string
    {
        $source = __('Source: Contact Form');
        $submittedAt = __('Submitted at: :time', ['time' => now()->format('Y-m-d H:i:s')]);

        return "{$this->message}\n\n---\n{$source}\n{$submittedAt}";
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

        $this->dispatch('contact-submission-rollback', [
            'message' => $message,
        ]);
    }

    /**
     * Generate optimistic ticket number for immediate display
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
     * Reset form for new submission
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->submissionFailed = false;
        $this->errorMessage = null;
        $this->isOptimisticState = false;
        $this->optimisticTicketNumber = '';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.contact-form');
    }
}
