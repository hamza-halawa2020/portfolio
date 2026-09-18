<?php

namespace App\Http\Requests\Api\V1;

class BlogPostIndexRequest extends IndexRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'category' => ['sometimes', 'string', 'max:120'],
            'tag' => ['sometimes', 'string', 'max:120'],
            'featured' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'min:2', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:latest,oldest,featured'],
        ];
    }
}
