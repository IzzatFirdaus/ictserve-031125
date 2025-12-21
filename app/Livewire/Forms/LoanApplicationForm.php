<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

/**
 * Component name: Loan Application Form
 * Description: Optimized Livewire form object for asset loan application with validation rules in Bahasa Melayu (v3.6.0)
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-012.1, D03-FR-012.2, D03-FR-012.4
 * @trace D04 §6.3 (Asset Loan System)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D15 (Bahasa Melayu sahaja v3.6.0)
 *
 * @requirements 1.4, 1.5, 11.5, 15.1, 15.2, 21.4
 *
 * @wcag-level AA
 *
 * @version 3.6.0
 *
 * @created 2025-11-03
 *
 * @updated 2025-12-21 (Bahasa Melayu sahaja)
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
     * Get validation messages in Bahasa Melayu (v3.6.0)
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama penuh diperlukan.',
            'email.required' => 'Alamat e-mel diperlukan.',
            'email.email' => 'Format alamat e-mel tidak sah.',
            'phone.required' => 'Nombor telefon diperlukan.',
            'division_id.required' => 'Bahagian diperlukan.',
            'grade_id.required' => 'Gred diperlukan.',
            'asset_id.required' => 'Aset diperlukan.',
            'purpose.required' => 'Tujuan pinjaman diperlukan.',
            'purpose.min' => 'Tujuan pinjaman mestilah sekurang-kurangnya 10 aksara.',
            'purpose.max' => 'Tujuan pinjaman tidak boleh melebihi 1000 aksara.',
            'start_date.required' => 'Tarikh mula diperlukan.',
            'start_date.after' => 'Tarikh mula mestilah selepas hari ini.',
            'end_date.required' => 'Tarikh tamat diperlukan.',
            'end_date.after' => 'Tarikh tamat mestilah selepas tarikh mula.',
        ];
    }

    /**
     * Get validation attributes in Bahasa Melayu (v3.6.0)
     *
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'name' => 'Nama Penuh',
            'email' => 'Alamat E-mel',
            'phone' => 'Nombor Telefon',
            'staff_id' => 'ID Staf',
            'division_id' => 'Bahagian',
            'grade_id' => 'Gred',
            'asset_id' => 'Aset',
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
