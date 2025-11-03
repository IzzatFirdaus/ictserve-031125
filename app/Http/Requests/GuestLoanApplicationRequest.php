<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Guest Loan Application Request
 *
 * Validates guest loan application submissions with comprehensive rules.
 *
 * @see D03-FR-001.2 Guest application validation
 * @see D03-FR-017.1 Guest form validation
 */
class GuestLoanApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Guest access allowed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Applicant information
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'required|string|max:20',
            'staff_id' => 'required|string|max:20',
            'grade' => 'required|string|in:41,44,48,52,54',
            'division_id' => 'required|exists:divisions,id',

            // Application details
            'purpose' => 'required|string|min:10|max:1000',
            'location' => 'required|string|max:255',
            'return_location' => 'nullable|string|max:255',
            'loan_start_date' => 'required|date|after:today',
            'loan_end_date' => 'required|date|after:loan_start_date',

            // Asset selection
            'items' => 'required|array|min:1',
            'items.*' => 'required|exists:assets,id',

            // Optional fields
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'special_instructions' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'applicant_name.required' => __('loan.validation.applicant_name_required'),
            'applicant_email.required' => __('loan.validation.applicant_email_required'),
            'applicant_email.email' => __('loan.validation.applicant_email_invalid'),
            'applicant_phone.required' => __('loan.validation.applicant_phone_required'),
            'staff_id.required' => __('loan.validation.staff_id_required'),
            'grade.required' => __('loan.validation.grade_required'),
            'grade.in' => __('loan.validation.grade_invalid'),
            'division_id.required' => __('loan.validation.division_required'),
            'division_id.exists' => __('loan.validation.division_invalid'),
            'purpose.required' => __('loan.validation.purpose_required'),
            'purpose.min' => __('loan.validation.purpose_min'),
            'location.required' => __('loan.validation.location_required'),
            'loan_start_date.required' => __('loan.validation.start_date_required'),
            'loan_start_date.after' => __('loan.validation.start_date_future'),
            'loan_end_date.required' => __('loan.validation.end_date_required'),
            'loan_end_date.after' => __('loan.validation.end_date_after_start'),
            'items.required' => __('loan.validation.items_required'),
            'items.min' => __('loan.validation.items_min'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'applicant_name' => __('loan.fields.applicant_name'),
            'applicant_email' => __('loan.fields.applicant_email'),
            'applicant_phone' => __('loan.fields.applicant_phone'),
            'staff_id' => __('loan.fields.staff_id'),
            'grade' => __('loan.fields.grade'),
            'division_id' => __('loan.fields.division'),
            'purpose' => __('loan.fields.purpose'),
            'location' => __('loan.fields.location'),
            'return_location' => __('loan.fields.return_location'),
            'loan_start_date' => __('loan.fields.loan_start_date'),
            'loan_end_date' => __('loan.fields.loan_end_date'),
            'items' => __('loan.fields.items'),
        ];
    }
}
