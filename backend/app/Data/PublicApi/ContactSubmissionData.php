<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\StoreContactMessageRequest;

final readonly class ContactSubmissionData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $company,
        public ?string $projectType,
        public ?string $budgetRange,
        public string $message,
        public string $locale,
    ) {}

    public static function fromRequest(StoreContactMessageRequest $request): self
    {
        return new self(
            name: trim((string) $request->validated('name')),
            email: strtolower(trim((string) $request->validated('email'))),
            phone: self::normalizePhone($request->validated('phone')),
            company: self::nullableTrim($request->validated('company')),
            projectType: self::nullableTrim($request->validated('project_type')),
            budgetRange: self::nullableTrim($request->validated('budget_range')),
            message: trim((string) $request->validated('message')),
            locale: $request->locale(),
        );
    }

    private static function nullableTrim(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }

    private static function normalizePhone(mixed $value): ?string
    {
        $value = self::nullableTrim($value);

        return $value === null ? null : preg_replace('/[^\d+()\-\s]/', '', $value);
    }
}
