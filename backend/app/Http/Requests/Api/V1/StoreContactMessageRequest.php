<?php

namespace App\Http\Requests\Api\V1;

class StoreContactMessageRequest extends PublicWriteRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->publicWriteRules(),
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:40'],
            'company' => ['sometimes', 'nullable', 'string', 'max:120'],
            'project_type' => ['sometimes', 'nullable', 'string', 'max:120'],
            'budget_range' => ['sometimes', 'nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'privacy_consent' => ['accepted'],
            'attachment' => ['prohibited'],
            'status' => ['prohibited'],
            'admin_notes' => ['prohibited'],
        ];
    }
}
