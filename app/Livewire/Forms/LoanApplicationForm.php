<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * Component name: Loan Application Form
 * Description: Optimized Livewire form object for asset loan application with validation rules and bilingual support
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-012.1, D03-FR-012.2, D03-FR-012.4
 * @trace D04 §6.3 (Asset Loan System)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @requirements 1.4, 1.5, 11.5, 15.1, 15.2, 21.4
 *
 * @wcag-level AA
 *
 * @version 1.0.0
 *
 * @created 2025-11-03
 */
class LoanApplicationForm extends Form
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

    #[Validate('required|exists:grades,id')]
    public ?int $grade_id = null;

    #[Validate('required|exists:assets,id')]
    public ?int $asset_id = null;

    #[Validate('required|string|min:10|max:1000')]
    public string $purpose = '';

    #[Validate('required|date|after:today')]
    public ?string $start_date = null;

    #[Validate('required|date|after:start_date')]
    public ?string $end_date = null;

    /**
     * Get validation messages with bilingual support
     */
    public function messages(): array
    {
        return [
            'name.required' => __('loans.name_required'),
            'email.required' => __('loans.email_required'),
            'email.email' => __('loans.email_invalid'),
            'phone.required' => __('loans.phone_required'),
            'division_id.required' => __('loans.division_required'),
            'grade_id.required' => __('loans.grade_required'),
            'asset_id.required' => __('loans.asset_required'),
            'purpose.required' => __('loans.purpose_required'),
            'purpose.min' => __('loans.purpose_min'),
            'purpose.max' => __('loans.purpose_max'),
            'start_date.required' => __('loans.start_date_required'),
            'start_date.after' => __('loans.start_date_after'),
            'end_date.required' => __('loans.end_date_required'),
            'end_date.after' => __('loans.end_date_after'),
        ];
    }

    /**
     * Get validation attributes with bilingual support
     */
    public function validationAttributes(): array
    {
        return [
            'name' => __('loans.full_name'),
            'email' => __('loans.email_address'),
            'phone' => __('loans.phone_number'),
            'staff_id' => __('loans.staff_id'),
            'division_id' => __('loans.division'),
            'grade_id' => __('loans.grade'),
            'asset_id' => __('loans.asset'),
            'purpose' => __('loans.purpose'),
            'start_date' => __('loans.start_date'),
            'end_date' => __('loans.end_date'),
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
        $this->grade_id = null;
        $this->asset_id = null;
        $this->purpose = '';
        $this->start_date = null;
        $this->end_date = null;

        $this->resetValidation();
    }
}
