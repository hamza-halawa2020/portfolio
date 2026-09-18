<?php

namespace App\Data\PublicApi;

use App\Http\Requests\Api\V1\StoreTestimonialRequest;

final readonly class TestimonialSubmissionData
{
    public function __construct(
        public string $name,
        public ?string $company,
        public ?string $position,
        public string $content,
        public ?int $rating,
        public string $contactEmail,
        public ?string $projectSlug,
        public string $locale,
    ) {}

    public static function fromRequest(StoreTestimonialRequest $request): self
    {
        return new self(
            name: trim((string) $request->validated('name')),
            company: self::nullableTrim($request->validated('company')),
            position: self::nullableTrim($request->validated('position')),
            content: trim((string) $request->validated('content')),
            rating: $request->validated('rating') === null ? null : (int) $request->validated('rating'),
            contactEmail: strtolower(trim((string) $request->validated('contact_email'))),
            projectSlug: self::nullableTrim($request->validated('project')),
            locale: $request->locale(),
        );
    }

    private static function nullableTrim(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
