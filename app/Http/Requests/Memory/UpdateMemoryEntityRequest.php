<?php

declare(strict_types=1);

namespace App\Http\Requests\Memory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemoryEntityRequest extends FormRequest
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
        $entity = $this->route('memoryEntity');
        $entityId = is_object($entity) && property_exists($entity, 'id') ? $entity->id : null;

        return [
            'name' => 'sometimes|required|string|max:255|unique:memory_entities,name,'.$entityId,
            'entity_type' => 'sometimes|required|string|max:255',
            'labels' => 'nullable|array',
            'labels.*' => 'string|max:255',
            'summary' => 'nullable|string',
            'metadata' => 'nullable|array',
            'source' => 'nullable|string|max:255',
            'source_identifier' => 'nullable|string|max:255',
            'confidence' => 'nullable|numeric|min:0|max:1',
            'discovered_at' => 'nullable|date',
        ];
    }
}
