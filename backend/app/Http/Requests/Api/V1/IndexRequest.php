<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\V1\Concerns\ResolvesApiLocale;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use ResolvesApiLocale;

    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return $this->prepareLocaleValidation();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->localeRules(),
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:24'],
        ];
    }

    public function perPage(int $default = 12): int
    {
        return min((int) $this->integer('per_page', $default), 24);
    }
}
