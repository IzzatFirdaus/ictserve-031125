<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Permintaan untuk pertanyaan FAQ AI
 *
 * @version 3.6.0
 *
 * @compliance D15 v3.6.0 (Bahasa Melayu sahaja - tiada parameter bahasa)
 *
 * @requirements 1.1, 1.4, 7.1
 */
class FaqQueryRequest extends FormRequest
{
    /**
     * Tentukan sama ada pengguna dibenarkan membuat permintaan ini
     */
    public function authorize(): bool
    {
        // Benarkan semua pengguna (tetamu dan authenticated) - True Hybrid Architecture
        return true;
    }

    /**
     * Dapatkan peraturan pengesahan untuk permintaan
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:500', 'min:3'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'force_refresh' => ['nullable', 'boolean'],
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
            'query.required' => 'Sila masukkan pertanyaan anda.',
            'query.string' => 'Pertanyaan mestilah dalam bentuk teks.',
            'query.max' => 'Pertanyaan tidak boleh melebihi 500 aksara.',
            'query.min' => 'Pertanyaan mestilah sekurang-kurangnya 3 aksara.',
            'session_id.string' => 'ID sesi tidak sah.',
            'session_id.max' => 'ID sesi terlalu panjang.',
            'email.email' => 'Format e-mel tidak sah.',
            'email.max' => 'E-mel tidak boleh melebihi 255 aksara.',
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
            'query' => 'pertanyaan',
            'session_id' => 'ID sesi',
            'email' => 'alamat e-mel',
        ];
    }
}
