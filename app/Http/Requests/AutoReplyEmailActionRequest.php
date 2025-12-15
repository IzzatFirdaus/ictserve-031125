<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoReplyEmailActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'action' => ['required', 'string', 'in:approve,reject'],
            'reason' => ['required_if:action,reject', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'token.required' => 'Token diperlukan.',
            'action.required' => 'Tindakan diperlukan.',
            'action.in' => 'Tindakan tidak sah.',
            'reason.required_if' => 'Sebab penolakan diperlukan.',
        ];
    }
}
