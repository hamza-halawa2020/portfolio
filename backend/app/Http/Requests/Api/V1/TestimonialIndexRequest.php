<?php

namespace App\Http\Requests\Api\V1;

class TestimonialIndexRequest extends IndexRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'featured' => ['sometimes', 'boolean'],
            'project' => ['sometimes', 'string', 'max:120'],
        ];
    }
}
