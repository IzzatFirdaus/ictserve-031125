<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * Helpdesk Ticket Form
 *
 * Optimized Livewire form object for helpdesk ticket submission
 * with validation rules and bilingual support
 *
 * @requirements 1.1, 1.2, 11.5, 15.1, 15.2, 21.4
 * @wcag-level AA
 * @version 1.0.0
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

    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $description = '';

    #[Validate('required|in:low,medium,high,critical')]
    public string $priority = 'medium';

    /**
     * Get validation messages with bilingual support
     */
    public function messages(): array
    {
        return [
            'name.required' => __('helpdesk.name_required'),
            'email.required' => __('helpdesk.email_required'),
            'email.email' => __('helpdesk.email_invalid'),
            'phone.required' => __('helpdesk.phone_required'),
            'category_id.required' => __('helpdesk.category_required'),
            'subject.required' => __('helpdesk.subject_required'),
            'description.required' => __('helpdesk.description_required'),
            'description.min' => __('helpdesk.description_min'),
            'description.max' => __('helpdesk.description_max'),
        ];
    }

    /**
     * Get validation attributes with bilingual support
     */
    public function validationAttributes(): array
    {
        return [
            'name' => __('helpdesk.full_name'),
            'email' => __('helpdesk.email_address'),
            'phone' => __('helpdesk.phone_number'),
            'staff_id' => __('helpdesk.staff_id'),
            'division_id' => __('helpdesk.division'),
            'category_id' => __('helpdesk.issue_category'),
            'subject' => __('helpdesk.subject'),
            'description' => __('helpdesk.problem_description'),
        ];
    }

    /**
     * Reset form to initial state
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
        $this->priority = 'medium';

        $this->resetValidation();
    }
}
