<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\TestimonialSubmissionData;
use App\Enums\TestimonialStatus;
use App\Events\PublicApi\TestimonialSubmitted;
use App\Models\Testimonial;
use App\Queries\PublicApi\ProjectQuery;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class SubmitTestimonial
{
    public function __construct(
        private ProjectQuery $projects,
    ) {}

    public function handle(TestimonialSubmissionData $data): Testimonial
    {
        $project = $data->projectSlug === null
            ? null
            : $this->projects->findPublishedFilterProject($data->projectSlug, $data->locale);

        if ($data->projectSlug !== null && ! $project) {
            throw ValidationException::withMessages(['project' => 'The selected project is invalid.']);
        }

        $this->rejectRecentDuplicate($data);

        return DB::transaction(function () use ($data, $project): Testimonial {
            $testimonial = Testimonial::query()->create([
                'project_id' => $project?->id,
                'name' => $data->name,
                'company' => $data->company,
                'position' => $data->position,
                'content' => ['en' => $data->content, 'ar' => $data->content],
                'rating' => $data->rating,
                'contact_email' => $data->contactEmail,
                'status' => TestimonialStatus::Pending,
                'is_featured' => false,
                'consented_at' => now(),
            ]);

            TestimonialSubmitted::dispatch($testimonial->id);

            return $testimonial;
        });
    }

    private function rejectRecentDuplicate(TestimonialSubmissionData $data): void
    {
        $exists = Testimonial::query()
            ->where('contact_email', $data->contactEmail)
            ->where('created_at', '>=', now()->subDay())
            ->where('content->en', $data->content)
            ->exists();

        throw_if($exists, ValidationException::withMessages([
            'submission' => 'Submission received successfully.',
        ]));
    }
}
