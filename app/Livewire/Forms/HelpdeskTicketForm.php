<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * Helpdesk Ticket Form - Livewire 3 Form Object
 *
 * Optimized form object with reactive validation and bilingual support.
 * Uses modern Livewire 3 patterns with #[Validate] attributes.
 *
 * @trace D03-FR-011.1, D03-FR-011.2, D03-FR-011.5
 * @trace D04-§6.3, D10-§7, D12-§9
 *
 * @requirements 1.1, 1.2, 11.5, 15.1, 15.2, 21.4
 *
 * @wcag-level AA
 *
 * @version 1.1.0
 */
class HelpdeskTicketForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:50')]
    public ?string $staff_id = null;

    #[Validate('required|exists:divisions,id')]
    public ?int $division_id = null;

    #[Validate('required|exists:ticket_categories,id')]
    public ?int $category_id = null;

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    #[Validate('required|in:low,normal,high,urgent')]
    public string $priority = 'normal';

    /**
     * Get validation messages with bilingual support.
     * Optimized for Livewire 3 real-time validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('helpdesk.validation.name_required'),
            'email.required' => __('helpdesk.validation.email_required'),
            'email.email' => __('helpdesk.validation.email_invalid'),
            'phone.required' => __('helpdesk.validation.phone_required'),
            'category_id.required' => __('helpdesk.validation.category_required'),
            'subject.required' => __('helpdesk.validation.subject_required'),
            'description.required' => __('helpdesk.validation.description_required'),
            'description.min' => __('helpdesk.validation.description_min'),
            'description.max' => __('helpdesk.validation.description_max'),
        ];
    }

    /**
     * Get validation attributes with bilingual support.
     * Used for cleaner error messages in Livewire 3.
     */
    public function validationAttributes(): array
    {
        return [
            'name' => __('helpdesk.fields.full_name'),
            'email' => __('helpdesk.fields.email_address'),
            'phone' => __('helpdesk.fields.phone_number'),
            'staff_id' => __('helpdesk.fields.staff_id'),
            'division_id' => __('helpdesk.fields.division'),
            'category_id' => __('helpdesk.fields.issue_category'),
            'subject' => __('helpdesk.fields.subject'),
            'description' => __('helpdesk.fields.problem_description'),
        ];
    }

    /**
     * Reset form to initial state with Livewire 3 optimization.
     */
    public function reset(...$properties): void
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->staff_id = null;
        $this->division_id = null;
        $this->category_id = null;
        $this->subject = '';
        $this->description = '';
        $this->priority = 'normal';

        $this->resetValidation();
    }

    /**
     * Get form data as array for submission.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'staff_id' => $this->staff_id,
            'division_id' => $this->division_id,
            'category_id' => $this->category_id,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
        ];
    }
}
