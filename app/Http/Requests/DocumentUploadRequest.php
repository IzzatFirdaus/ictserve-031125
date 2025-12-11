<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Permintaan untuk muat naik dokumen AI
 *
 * @version 3.6.0
 *
 * @compliance D15 v3.6.0 (Bahasa Melayu sahaja)
 *
 * @requirements 2.1, 2.5, 7.1
 */
class DocumentUploadRequest extends FormRequest
{
    /**
     * Tentukan sama ada pengguna dibenarkan membuat permintaan ini
     */
    public function authorize(): bool
    {
        // Hanya admin dan superuser boleh muat naik dokumen
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
            'file' => [
                'required',
                'file',
                'mimes:pdf,docx,txt',
                'max:10240', // 10MB
            ],
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
            'file.required' => 'Sila pilih fail untuk dimuat naik.',
            'file.file' => 'Muat naik mestilah fail yang sah.',
            'file.mimes' => 'Hanya fail PDF, DOCX, dan TXT dibenarkan.',
            'file.max' => 'Saiz fail tidak boleh melebihi 10MB.',
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
            'file' => 'fail dokumen',
        ];
    }
}
