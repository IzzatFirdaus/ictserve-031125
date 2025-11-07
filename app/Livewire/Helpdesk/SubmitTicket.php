<?php

declare(strict_types=1);

namespace App\Livewire\Helpdesk;

use App\Models\Asset;
use App\Models\Division;
use App\Models\TicketCategory;
use App\Services\HybridHelpdeskService;
use App\Traits\OptimizedFormPerformance;
use App\Traits\OptimizedLivewireComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Submit Helpdesk Ticket - Livewire 3 Multi-Step Wizard
 *
 * WCAG 2.2 AA compliant wizard with reactive state management.
 * Optimized for Livewire 3 with #[Reactive], #[Computed], and performance traits.
 *
 * @trace D03-FR-001.1, D03-FR-011.1-11.7
 * @trace D04-§6.1, D10-§7, D12-§9
 *
 * @requirements 1.1, 1.2, 11.1-11.7, 21.5
 *
 * @wcag-level AA
 *
 * @version 1.1.0
 */
class SubmitTicket extends Component
{
    use OptimizedFormPerformance;
    use OptimizedLivewireComponent;
    use WithFileUploads;

    // Wizard state
    #[Reactive]
    public int $currentStep = 1;

    #[Reactive]
    public int $totalSteps = 4;

    // Step 1: Contact Information
    #[Validate('required|string|max:255')]
    #[Reactive]
    public string $guest_name = '';

    #[Validate('required|email|max:255')]
    #[Reactive]
    public string $guest_email = '';

    #[Validate('required|string|max:20')]
    #[Reactive]
    public string $guest_phone = '';

    #[Validate('nullable|string|max:50')]
    #[Reactive]
    public ?string $staff_id = null;

    #[Validate('required|exists:divisions,id')]
    #[Reactive]
    public ?int $division_id = null;

    // Step 2: Issue Details
    #[Validate('required|exists:ticket_categories,id')]
    #[Reactive]
    public ?int $category_id = null;

    #[Validate('required|in:low,normal,high,urgent')]
    #[Reactive]
    public string $priority = 'normal';

    #[Validate('required|string|max:255')]
    #[Reactive]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    #[Reactive]
    public string $description = '';

    #[Validate('nullable|exists:assets,id')]
    #[Reactive]
    public ?int $asset_id = null;

    #[Validate('nullable|string|max:1000')]
    #[Reactive]
    public ?string $internal_notes = null;

    // Step 3: Attachments
    #[Validate('nullable|array')]
    #[Reactive]
    public array $attachments = [];

    // Submission state
    #[Reactive]
    public bool $isSubmitting = false;

    #[Reactive]
    public ?string $ticketNumber = null;

    /**
     * Get available ticket categories (cached computed property).
     * Livewire 3 optimized with persistent caching.
     */
    #[Computed(persist: true, cache: true)]
    public function categories()
    {
        $locale = app()->getLocale();
        $nameColumn = $locale === 'ms' ? 'name_ms' : 'name_en';
        $descriptionColumn = $locale === 'ms' ? 'description_ms' : 'description_en';

        return TicketCategory::query()
            ->where('is_active', true)
            ->select('id', 'name_ms', 'name_en', 'description_ms', 'description_en')
            ->orderBy($nameColumn)
            ->get()
            ->map(function (TicketCategory $category) use ($nameColumn, $descriptionColumn) {
                $category->setAttribute('name', $category->getAttribute($nameColumn));
                $category->setAttribute('description', $category->getAttribute($descriptionColumn));

                return $category;
            });
    }

    /**
     * Get available divisions (cached computed property).
     * Livewire 3 optimized with persistent caching.
     */
    #[Computed(persist: true, cache: true)]
    public function divisions()
    {
        $nameColumn = app()->getLocale() === 'ms' ? 'name_ms' : 'name_en';

        return Division::query()
            ->where('is_active', true)
            ->select('id', 'name_ms', 'name_en')
            ->orderBy($nameColumn)
            ->get();
    }

    /**
     * Get available assets (lazy loaded, cached).
     * Livewire 3 optimized with conditional loading and caching.
     */
    #[Computed(persist: true, cache: true)]
    public function assets()
    {
        // Only load assets when needed (step 2 or later)
        if ($this->currentStep < 2) {
            return collect([]);
        }

        return Asset::query()
            ->where('status', 'available')
            ->select('id', 'name', 'asset_tag')
            ->orderBy('name')
            ->limit(50)
            ->get();
    }

    /**
     * Advance to next step
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Go back to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Go to specific step
     */
    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= $this->currentStep && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * Validate current step
     */
    protected function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validate([
                'category_id' => 'required|exists:ticket_categories,id',
                'priority' => 'required|in:low,normal,high,urgent',
                'subject' => 'required|string|max:255',
                'description' => 'required|string|min:10|max:5000',
            ]),
            3 => $this->validate([
                'attachments' => 'nullable|array|max:5',
                'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx',
            ]),
            default => null,
        };
    }

    /**
     * Validate step 1 based on authentication status
     */
    protected function validateStep1(): void
    {
        // Authenticated users don't need to fill guest fields
        if (auth()->check()) {
            // No validation needed for authenticated users on step 1
            return;
        }

        // Guest users must fill all contact fields
        $this->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'division_id' => 'required|exists:divisions,id',
        ]);
    }

    /**
     * Submit the ticket
     *
     * Implements conditional logic for guest vs authenticated submission
     *
     * @trace Requirements 1.1, 1.2, 1.3, 4.2
     */
    public function submit(): void
    {
        $this->isSubmitting = true;

        try {
            // Final validation
            $this->validate();

            DB::beginTransaction();

            $service = app(HybridHelpdeskService::class);

            // Conditional logic: Check if user is authenticated
            if (auth()->check()) {
                // Authenticated submission - use enhanced features
                $ticket = $service->createAuthenticatedTicket([
                    'category_id' => $this->category_id,
                    'priority' => $this->priority,
                    'title' => $this->subject,
                    'description' => $this->description,
                    'damage_type' => null, // Not applicable for standard helpdesk
                    'asset_id' => $this->asset_id,
                    'internal_notes' => $this->internal_notes, // Use from component property
                ], auth()->user());
            } else {
                // Guest submission - use guest fields
                $ticket = $service->createGuestTicket([
                    'guest_name' => $this->guest_name,
                    'guest_email' => $this->guest_email,
                    'guest_phone' => $this->guest_phone,
                    'guest_staff_id' => $this->staff_id,
                    'guest_grade' => null, // Can be enhanced later
                    'guest_division' => null, // Can be enhanced later
                    'category_id' => $this->category_id,
                    'priority' => $this->priority,
                    'title' => $this->subject,
                    'description' => $this->description,
                    'damage_type' => null, // Not applicable for standard helpdesk
                    'asset_id' => $this->asset_id,
                ]);
            }

            // Handle file attachments for both submission types
            if (! empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('helpdesk-attachments', 'private');
                    $ticket->attachments()->create([
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            $this->ticketNumber = $ticket->ticket_number;
            $this->currentStep = $this->totalSteps;

            // Dispatch appropriate event based on submission type
            $this->dispatch('ticket-submitted', [
                'ticketNumber' => $this->ticketNumber,
                'isAuthenticated' => auth()->check(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->isSubmitting = false;

            // Proper error handling with validation feedback
            $errorMessage = __('helpdesk.submission_failed');
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errorMessage = __('helpdesk.validation_failed');
            }

            $this->dispatch('submission-failed', message: $errorMessage);

            // Log error for debugging
            Log::error('Ticket submission failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'guest_email' => $this->guest_email ?? null,
            ]);

            throw $e;
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Reset form
     */
    public function resetForm(): void
    {
        $this->reset();
        $this->currentStep = 1;
        $this->dispatch('form-reset');
    }

    /**
     * Custom validation messages for Livewire 3 real-time validation.
     */
    protected function messages(): array
    {
        return [
            'guest_name.required' => __('helpdesk.validation.name_required'),
            'guest_email.required' => __('helpdesk.validation.email_required'),
            'guest_email.email' => __('helpdesk.validation.email_invalid'),
            'guest_phone.required' => __('helpdesk.validation.phone_required'),
            'division_id.required' => __('helpdesk.validation.division_required'),
            'category_id.required' => __('helpdesk.validation.category_required'),
            'subject.required' => __('helpdesk.validation.subject_required'),
            'description.required' => __('helpdesk.validation.description_required'),
            'description.min' => __('helpdesk.validation.description_min'),
            'description.max' => __('helpdesk.validation.description_max'),
            'attachments.*.max' => __('helpdesk.validation.file_too_large'),
            'attachments.*.mimes' => __('helpdesk.validation.invalid_file_type'),
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        $layout = (auth()->check() || request()->routeIs('helpdesk.authenticated.*'))
            ? 'layouts.portal'
            : 'layouts.front';

        return view('livewire.helpdesk.submit-ticket', [
            'divisions' => $this->divisions,
            'categories' => $this->categories,
            'assets' => $this->assets,
        ])->layout($layout);
    }
}
