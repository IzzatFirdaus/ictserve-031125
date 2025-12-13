<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Permintaan untuk penjanaan auto-reply AI
 *
 * @version 3.6.0
 *
 * @compliance D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 3.1, 3.2, 3.4, 3.6
 */
class AutoReplyGenerateRequest extends FormRequest
{
    /**
     * Tentukan sama ada pengguna dibenarkan membuat permintaan ini
     */
    public function authorize(): bool
    {
        // Hanya admin dan superuser boleh menjana auto-reply
        return $this->user()?->hasAnyRole(['admin', 'superuser']) ?? false;
    }

    /**
     * Dapatkan peraturan pengesahan untuk permintaan
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'replyable_type' => [
                'required',
                'string',
                'in:helpdesk_ticket,loan_application,App\Models\HelpdeskTicket,App\Models\LoanApplication',
            ],
            'replyable_id' => ['required', 'integer', 'min:1'],
            'template_id' => ['nullable', 'integer', 'exists:auto_reply_templates,id'],
            'async' => ['nullable', 'boolean'],
            'auto_submit' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Dapatkan mesej ralat tersuai dalam Bahasa Melayu
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'replyable_type.required' => 'Jenis model diperlukan.',
            'replyable_type.string' => 'Jenis model mestilah dalam bentuk teks.',
            'replyable_type.in' => 'Jenis model tidak sah. Hanya tiket helpdesk atau permohonan pinjaman dibenarkan.',
            'replyable_id.required' => 'ID model diperlukan.',
            'replyable_id.integer' => 'ID model mestilah nombor.',
            'replyable_id.min' => 'ID model mestilah lebih besar daripada 0.',
            'template_id.integer' => 'ID templat mestilah nombor.',
            'template_id.exists' => 'Templat yang dipilih tidak wujud.',
        ];
    }

    /**
     * Dapatkan atribut tersuai untuk mesej ralat
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'replyable_type' => 'jenis model',
            'replyable_id' => 'ID model',
            'template_id' => 'ID templat',
            'async' => 'pemprosesan async',
            'auto_submit' => 'hantar automatik',
        ];
    }
}
