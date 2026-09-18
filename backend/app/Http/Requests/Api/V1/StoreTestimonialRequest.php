<?php

namespace App\Http\Requests\Api\V1;

class StoreTestimonialRequest extends PublicWriteRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->publicWriteRules(),
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'company' => ['sometimes', 'nullable', 'string', 'max:120'],
            'position' => ['sometimes', 'nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
            'rating' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:5'],
            'contact_email' => ['required', 'email:rfc', 'max:190'],
            'project' => ['sometimes', 'nullable', 'string', 'max:120'],
            'publication_consent' => ['accepted'],
            'status' => ['prohibited'],
            'is_featured' => ['prohibited'],
            'reviewed_at' => ['prohibited'],
            'admin_notes' => ['prohibited'],
            'internal_notes' => ['prohibited'],
        ];
    }
}
