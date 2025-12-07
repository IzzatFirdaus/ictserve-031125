<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Traits\CitizenCentricDesign;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Hybrid Helpdesk Ticket Form - True Hybrid Architecture v3.5.0
 *
 * Supports both authenticated staff (auto-fill) and guest submissions.
 * ISO Compliance: PK.(S).MOTAC.07.(L1)
 *
 * Features:
 * - Hybrid form with Auth::check() logic for auto-fill
 * - Real-time validation with Livewire 3.7.0 (300ms debounce)
 * - File upload with drag-and-drop (max 5 files, 5MB each)
 * - PDPA acknowledgement checkbox (mandatory)
 * - Form reference code display: PK.(S).MOTAC.07.(L1)
 * - Multi-step wizard with progress indicators
 * - Optimistic UI for immediate feedback
 *
 * @trace D03-FR-011, D12-§9, D14-§2.2
 *
 * @see Requirements 1.1, 1.2, 1.4, 1.5, 24.1
 */
#[Layout('layouts.front')]
class TicketForm extends Component
{
    use CitizenCentricDesign;
    use WithFileUploads;

    /**
     * Form reference code per MOTAC BPM standards
     */
    public const FORM_REFERENCE_CODE = 'PK.(S).MOTAC.07.(L1)';

    // ========================================
    // SUBMITTER INFORMATION (Hybrid Fields)
    // ========================================

    #[Validate('required|string|max:255')]
    public string $submitter_name = '';

    #[Validate('required|email|max:255')]
    public string $submitter_email = '';

    #[Validate('required|string|max:20')]
    public string $submitter_phone = '';

    #[Validate('nullable|string|max:50')]
    public ?string $submitter_staff_id = null;

    #[Validate('required')]
    public ?int $division_id = null;

    #[Validate('required|string|max:50')]
    public string $job_grade = '';

    // ========================================
    // TICKET DETAILS
    // ========================================

    #[Validate('required|exists:ticket_categories,id')]
    public ?int $category_id = null;

    #[Validate('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    // ========================================
    // FILE ATTACHMENTS
    // Max 5 files, 5MB each, PDF/JPG/PNG/DOCX
    // ========================================

    #[Validate([
        'attachments' => 'nullable|array|max:5',
        'attachments.*' => 'file|max:5120|mimes:pdf,jpg,jpeg,png,docx',
    ])]
    public array $attachments = [];

    // ========================================
    // PDPA DECLARATION
    // ========================================

    #[Validate('accepted')]
    public bool $declaration_accepted = false;

    // ========================================
    // WIZARD STATE
    // ========================================

    public int $currentStep = 1;

    public int $totalSteps = 3;

    // ========================================
    // UI STATE
    // ========================================

    public bool $isSubmitting = false;

    public bool $submitted = false;

    public bool $submissionFailed = false;

    public ?string $ticketNumber = null;

    public ?string $errorMessage = null;

    public string $divisionSearch = '';

    // Optimistic UI State
    public string $optimisticTicketNumber = '';

    public bool $isOptimisticState = false;

    // ========================================
    // HYBRID ARCHITECTURE STATE
    // ========================================

    public bool $isAuthenticated = false;

    public ?int $userId = null;

    /**
     * Initialize component with hybrid auto-fill logic
     *
     * Per Requirement 1.2: When authenticated user accesses form,
     * auto-fill name, email, phone, division, and grade from user profile.
     *
     * @param  string|null  $category  Category code to pre-fill
     */
    public function mount(?string $category = null): void
    {
        // Check authentication status
        $this->isAuthenticated = Auth::check();

        if ($this->isAuthenticated) {
            /** @var User $user */
            $user = Auth::user();
            $this->userId = $user->id;

            // Auto-fill from authenticated user profile
            $this->submitter_name = $user->name;
            $this->submitter_email = $user->email;
            $this->submitter_phone = $user->phone ?? '';
            $this->submitter_staff_id = $user->staff_number ?? null;
            $this->division_id = $user->division_id ?? null;
            $this->job_grade = $user->grade ?? '';

            // Provide feedback about auto-fill (citizen-centric)
            $this->provideFeedback(
                __('helpdesk.autofill_success'),
                'info',
                3000
            );
        }

        // Pre-fill category if provided via query parameter
        if ($category !== null) {
            $ticketCategory = TicketCategory::where('code', strtoupper($category))->first();
            if ($ticketCategory) {
                $this->category_id = $ticketCategory->id;

                // Provide feedback about pre-filled category
                $this->provideFeedback(
                    __('helpdesk.category_prefilled'),
                    'info',
                    2000
                );
            }
        }

        // Show initial progress
        $this->showProgress($this->currentStep, $this->totalSteps, __('helpdesk.step_submitter_info'));

        // Track form access for continuous improvement
        $this->trackUserInteraction('helpdesk_form_accessed', [
            'is_authenticated' => $this->isAuthenticated,
            'category_prefilled' => $category !== null,
        ]);
    }

    // ========================================
    // COMPUTED PROPERTIES
    // ========================================

    /**
     * Get all divisions for the select dropdown
     */
    #[Computed]
    public function divisions()
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        return Division::query()
            ->when($this->divisionSearch, function ($query) use ($nameColumn): void {
                $query->where($nameColumn, 'like', "%{$this->divisionSearch}%");
            })
            ->orderBy($nameColumn)
            ->get();
    }

    /**
     * Get all ticket categories
     */
    #[Computed]
    public function categories()
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ms' ? 'name_ms' : 'name_en';

        return TicketCategory::query()
            ->whereNull('parent_id')
            ->orderBy($nameColumn)
            ->get();
    }

    /**
     * Get the authenticated user if available
     */
    #[Computed]
    public function authenticatedUser(): ?User
    {
        return $this->isAuthenticated ? Auth::user() : null;
    }

    // ========================================
    // WIZARD NAVIGATION
    // ========================================

    /**
     * Move to next step with validation
     * Implements citizen-centric navigation with clear feedback
     */
    public function nextStep(): void
    {
        try {
            $this->validateCurrentStep();

            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;

                // Provide clear feedback about progress
                $stepLabels = [
                    1 => __('helpdesk.step_submitter_info'),
                    2 => __('helpdesk.step_ticket_details'),
                    3 => __('helpdesk.step_review_submit'),
                ];

                $this->showProgress(
                    $this->currentStep,
                    $this->totalSteps,
                    $stepLabels[$this->currentStep] ?? ''
                );

                // Track navigation for UX improvement
                $this->trackUserInteraction('wizard_next_step', [
                    'from_step' => $this->currentStep - 1,
                    'to_step' => $this->currentStep,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Provide clear validation feedback
            $this->provideFeedback(
                __('helpdesk.please_fix_errors'),
                'error',
                5000
            );
            throw $e;
        }
    }

    /**
     * Move to previous step
     * Implements intuitive backward navigation
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;

            // Update progress indicator
            $stepLabels = [
                1 => __('helpdesk.step_submitter_info'),
                2 => __('helpdesk.step_ticket_details'),
                3 => __('helpdesk.step_review_submit'),
            ];

            $this->showProgress(
                $this->currentStep,
                $this->totalSteps,
                $stepLabels[$this->currentStep] ?? ''
            );

            // Track backward navigation
            $this->trackUserInteraction('wizard_previous_step', [
                'from_step' => $this->currentStep + 1,
                'to_step' => $this->currentStep,
            ]);
        }
    }

    /**
     * Go to a specific step (for progress indicator clicks)
     * Allows users to review and edit previous steps
     */
    public function goToStep(int $step): void
    {
        // Only allow going back or to completed steps
        if ($step < $this->currentStep && $step >= 1) {
            $this->currentStep = $step;

            // Provide feedback about navigation
            $this->provideFeedback(
                __('helpdesk.navigated_to_step', ['step' => $step]),
                'info',
                2000
            );

            // Track direct navigation
            $this->trackUserInteraction('wizard_direct_navigation', [
                'target_step' => $step,
            ]);
        }
    }

    /**
     * Validate only the current step
     * Real-time validation with Livewire 3.7.0
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'submitter_name' => 'required|string|max:255',
                'submitter_email' => 'required|email|max:255',
                'submitter_phone' => 'required|string|max:20',
                'submitter_staff_id' => 'nullable|string|max:50',
                'division_id' => 'required',
                'job_grade' => 'required|string|max:50',
            ]),
            2 => $this->validate([
                'category_id' => 'required|exists:ticket_categories,id',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10|max:5000',
                'priority' => 'required|in:low,normal,high,urgent',
                'attachments' => 'nullable|array|max:5',
                'attachments.*' => 'file|max:5120|mimes:pdf,jpg,jpeg,png,docx',
            ]),
            3 => $this->validate([
                'declaration_accepted' => 'accepted',
            ]),
            default => null,
        };
    }

    // ========================================
    // FILE UPLOAD HANDLING
    // ========================================

    /**
     * Remove an attachment from the list
     */
    public function removeAttachment(int $index): void
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    // ========================================
    // FORM SUBMISSION
    // ========================================

    /**
     * Submit the helpdesk ticket with Optimistic UI
     *
     * Implements hybrid data association per D03 SRS-DATA-001:
     * - Authenticated: Links to user_id
     * - Guest: Sets user_id to NULL, stores submitter data
     *
     * @see Requirements 1.5, 1.7
     */
    public function submit(): void
    {
        $this->isSubmitting = true;
        $this->submissionFailed = false;
        $this->errorMessage = null;

        try {
            // Step 1: Validate all form data
            $this->validate();

            // Step 2: Generate optimistic ticket number for immediate feedback
            $this->optimisticTicketNumber = $this->generateOptimisticTicketNumber();

            // Step 3: Enter optimistic state - show success immediately
            $this->isOptimisticState = true;
            $this->submitted = true;
            $this->ticketNumber = $this->optimisticTicketNumber;

            // Dispatch optimistic success event for Alpine.js
            $this->dispatch('optimistic-submission-started', [
                'ticketNumber' => $this->optimisticTicketNumber,
                'email' => $this->submitter_email,
            ]);

            // Step 4: Create ticket with hybrid data association
            $ticket = $this->createTicket();

            // Step 5: Handle file uploads
            $this->processAttachments($ticket);

            // Step 6: Calculate SLA due dates
            $ticket->calculateSLADueDates();

            // Step 7: Generate status token for guest tracking
            $statusToken = $ticket->generateStatusToken();

            // Step 8: Send email confirmation (async queue - 60 second SLA)
            $this->sendConfirmationEmail($ticket, $statusToken);

            // Step 9: Update with actual ticket number
            $this->ticketNumber = $ticket->ticket_number;
            $this->isOptimisticState = false;

            // Dispatch final success event
            $this->dispatch('submission-confirmed', [
                'ticketNumber' => $ticket->ticket_number,
                'ticketId' => $ticket->id,
                'statusToken' => $statusToken,
            ]);

            // Provide success confirmation with next steps (citizen-centric)
            $this->provideSuccessConfirmation(
                __('helpdesk.ticket_created_success', ['number' => $ticket->ticket_number]),
                [
                    [
                        'label' => __('helpdesk.track_ticket'),
                        'url' => route('helpdesk.track', ['token' => $statusToken]),
                        'icon' => 'heroicon-o-magnifying-glass',
                    ],
                    [
                        'label' => __('helpdesk.submit_another'),
                        'action' => 'resetForm',
                        'icon' => 'heroicon-o-plus-circle',
                    ],
                    [
                        'label' => __('helpdesk.view_dashboard'),
                        'url' => route('dashboard'),
                        'icon' => 'heroicon-o-home',
                        'condition' => $this->isAuthenticated,
                    ],
                ]
            );

            // Track successful submission
            $this->trackUserInteraction('ticket_submitted_successfully', [
                'ticket_number' => $ticket->ticket_number,
                'category_id' => $this->category_id,
                'priority' => $this->priority,
                'has_attachments' => ! empty($this->attachments),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->rollbackOptimisticState(__('helpdesk.validation_errors'));

            // Provide actionable validation feedback
            $this->provideFeedback(
                __('helpdesk.please_review_errors'),
                'error',
                0 // Persistent until fixed
            );

            throw $e;
        } catch (\Exception $e) {
            $this->rollbackOptimisticState(__('helpdesk.submission_failed'));

            // Provide clear error feedback with suggestion
            $this->provideValidationFeedback(
                'submission',
                __('helpdesk.submission_failed'),
                __('helpdesk.try_again_or_contact_support')
            );

            logger()->error('Hybrid ticket submission failed', [
                'error' => $e->getMessage(),
                'email' => $this->submitter_email,
                'is_authenticated' => $this->isAuthenticated,
                'user_id' => $this->userId,
                'optimistic_ticket' => $this->optimisticTicketNumber,
            ]);

            // Track submission failure for improvement
            $this->trackUserInteraction('ticket_submission_failed', [
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Create the helpdesk ticket with hybrid data association
     */
    protected function createTicket(): HelpdeskTicket
    {
        return HelpdeskTicket::create([
            // Ticket identification
            'ticket_number' => HelpdeskTicket::generateTicketNumberV3(),
            'form_reference_code' => self::FORM_REFERENCE_CODE,

            // Hybrid data association
            'user_id' => $this->isAuthenticated ? $this->userId : null,

            // Submitter information (always stored for audit trail)
            'guest_name' => $this->submitter_name,
            'guest_email' => $this->submitter_email,
            'guest_phone' => $this->submitter_phone,
            'guest_staff_id' => $this->submitter_staff_id,
            'job_grade' => $this->job_grade,
            'division_id' => $this->division_id,

            // Ticket details
            'category_id' => $this->category_id,
            'priority' => $this->priority,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => 'open',

            // PDPA compliance
            'declaration_accepted' => $this->declaration_accepted,

            // Source tracking
            'source' => $this->isAuthenticated ? 'authenticated_portal' : 'guest_portal',
        ]);
    }

    /**
     * Process and store file attachments
     *
     * Per Requirement 1.4: Max 5 files, 5MB each, PDF/JPG/PNG/DOCX
     */
    protected function processAttachments(HelpdeskTicket $ticket): void
    {
        if (empty($this->attachments)) {
            return;
        }

        foreach ($this->attachments as $attachment) {
            $path = $attachment->store(
                "helpdesk-attachments/{$ticket->ticket_number}",
                'public'
            );

            $ticket->attachments()->create([
                'filename' => $attachment->getClientOriginalName(),
                'path' => $path,
                'size' => $attachment->getSize(),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }
    }

    /**
     * Send confirmation email with status token
     *
     * Per Requirement 1.7: Email within 60 seconds
     *
     * @param  HelpdeskTicket  $ticket  The created ticket
     * @param  string  $statusToken  The status token for tracking (included in email)
     */
    protected function sendConfirmationEmail(HelpdeskTicket $ticket, string $statusToken): void
    {
        // Status token is passed to the mailable for inclusion in the tracking URL
        Mail::to($this->submitter_email)->queue(
            new \App\Mail\HelpdeskTicketCreated($ticket, $statusToken)
        );
    }

    // ========================================
    // OPTIMISTIC UI HELPERS
    // ========================================

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

        $this->dispatch('submission-rollback', [
            'message' => $message,
        ]);
    }

    /**
     * Generate optimistic ticket number for immediate display
     * Format: HD-YYYYMM-TEMP
     */
    protected function generateOptimisticTicketNumber(): string
    {
        return 'HD-'.date('Ym').'-'.strtoupper(substr(md5((string) microtime(true)), 0, 4));
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
     * Reset form and start new submission
     */
    public function resetForm(): void
    {
        // Preserve authentication state
        $wasAuthenticated = $this->isAuthenticated;
        $userId = $this->userId;

        $this->reset([
            'submitter_name',
            'submitter_email',
            'submitter_phone',
            'submitter_staff_id',
            'division_id',
            'job_grade',
            'category_id',
            'priority',
            'subject',
            'description',
            'attachments',
            'declaration_accepted',
            'currentStep',
            'submitted',
            'submissionFailed',
            'ticketNumber',
            'errorMessage',
            'isOptimisticState',
            'optimisticTicketNumber',
        ]);

        $this->currentStep = 1;
        $this->priority = 'normal';
        $this->isAuthenticated = $wasAuthenticated;
        $this->userId = $userId;

        // Re-apply auto-fill if authenticated
        if ($this->isAuthenticated && Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            $this->submitter_name = $user->name;
            $this->submitter_email = $user->email;
            $this->submitter_phone = $user->phone ?? '';
            $this->submitter_staff_id = $user->staff_number ?? null;
            $this->division_id = $user->division_id ?? null;
            $this->job_grade = $user->grade ?? '';
        }
    }

    // ========================================
    // VALIDATION MESSAGES
    // ========================================

    /**
     * Get custom validation messages with bilingual support
     */
    public function messages(): array
    {
        return [
            'submitter_name.required' => __('helpdesk.name_required'),
            'submitter_email.required' => __('helpdesk.email_required'),
            'submitter_email.email' => __('helpdesk.email_invalid'),
            'submitter_phone.required' => __('helpdesk.phone_required'),
            'division_id.required' => __('helpdesk.division_required'),
            'job_grade.required' => __('helpdesk.job_grade_required'),
            'category_id.required' => __('helpdesk.category_required'),
            'category_id.exists' => __('helpdesk.category_required'),
            'subject.required' => __('helpdesk.subject_required'),
            'description.required' => __('helpdesk.description_required'),
            'description.min' => __('helpdesk.description_min'),
            'description.max' => __('helpdesk.description_max'),
            'attachments.max' => __('validation.max.array'),
            'attachments.*.max' => __('validation.max.file'),
            'attachments.*.mimes' => __('validation.mimes'),
            'declaration_accepted.accepted' => __('helpdesk.declaration_required'),
        ];
    }

    /**
     * Get validation attribute names with bilingual support
     */
    public function validationAttributes(): array
    {
        return [
            'submitter_name' => __('helpdesk.full_name'),
            'submitter_email' => __('helpdesk.email_address'),
            'submitter_phone' => __('helpdesk.phone_number'),
            'submitter_staff_id' => __('helpdesk.staff_id'),
            'division_id' => __('helpdesk.division'),
            'job_grade' => __('helpdesk.job_grade'),
            'category_id' => __('helpdesk.category'),
            'subject' => __('helpdesk.subject'),
            'description' => __('helpdesk.description'),
            'attachments' => __('helpdesk.attachments'),
            'declaration_accepted' => __('helpdesk.declaration'),
        ];
    }

    // ========================================
    // RENDER
    // ========================================

    public function render()
    {
        return view('livewire.helpdesk.ticket-form');
    }
}
