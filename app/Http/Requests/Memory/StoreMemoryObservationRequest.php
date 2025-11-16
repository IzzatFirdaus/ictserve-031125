<?php

declare(strict_types=1);

namespace App\Http\Requests\Memory;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemoryObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'content_hash' => 'nullable|string|max:255',
            'metadata' => 'nullable|array',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'recorded_at' => 'nullable|date',
            'memory_adapter_id' => 'nullable|uuid|exists:memory_adapters,id',
        ];
    }
}
