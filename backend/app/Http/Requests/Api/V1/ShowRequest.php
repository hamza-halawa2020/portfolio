<?php

namespace App\Http\Requests\Api\V1;

class ShowRequest extends IndexRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->localeRules();
    }
}
