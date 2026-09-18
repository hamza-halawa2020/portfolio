<?php

namespace App\Http\Requests\Api\V1;

class ProjectInteractionRequest extends PublicWriteRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->publicWriteRules();
    }
}
