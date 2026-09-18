<?php

namespace App\Http\Requests\Api\V1;

use App\Data\PublicApi\VisitorContext;
use App\Http\Requests\Api\V1\Concerns\ResolvesApiLocale;
use Illuminate\Foundation\Http\FormRequest;

abstract class PublicWriteRequest extends FormRequest
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

    public function visitorContext(): VisitorContext
    {
        /** @var VisitorContext $context */
        $context = $this->attributes->get('visitor_context');

        return $context;
    }

    /**
     * @return array<string, mixed>
     */
    protected function publicWriteRules(): array
    {
        return [
            ...$this->localeRules(),
            'website' => ['sometimes', 'prohibited'],
        ];
    }
}
