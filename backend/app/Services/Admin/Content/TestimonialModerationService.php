<?php

namespace App\Services\Admin\Content;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TestimonialModerationService
{
    public function __construct(
        private readonly ContentPayload $payload,
        private readonly PublicContentCacheInvalidator $cacheInvalidator,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Testimonial $testimonial, array $data): Testimonial
    {
        return DB::transaction(function () use ($testimonial, $data): Testimonial {
            $testimonial->fill([
                'project_id' => Arr::get($data, 'project_id'),
                'name' => Arr::get($data, 'name', $testimonial->name),
                'company' => Arr::get($data, 'company'),
                'position' => Arr::get($data, 'position'),
                'profile_image_path' => Arr::get($data, 'profile_image_path'),
                'content' => $this->payload->localized($testimonial, $data, 'content'),
                'rating' => Arr::get($data, 'rating'),
                'status' => Arr::get($data, 'status', $testimonial->status),
                'is_featured' => (bool) Arr::get($data, 'is_featured', $testimonial->is_featured),
                'reviewed_at' => $this->reviewedAtFor(Arr::get($data, 'status', $testimonial->status), $testimonial),
            ])->save();

            $this->cacheInvalidator->testimonials();

            return $testimonial->refresh();
        });
    }

    public function approve(Testimonial $testimonial): Testimonial
    {
        return $this->changeStatus($testimonial, TestimonialStatus::Approved);
    }

    public function reject(Testimonial $testimonial): Testimonial
    {
        return $this->changeStatus($testimonial, TestimonialStatus::Rejected);
    }

    public function archive(Testimonial $testimonial): Testimonial
    {
        return $this->changeStatus($testimonial, TestimonialStatus::Archived);
    }

    public function feature(Testimonial $testimonial, bool $featured = true): Testimonial
    {
        return DB::transaction(function () use ($testimonial, $featured): Testimonial {
            $testimonial->forceFill(['is_featured' => $featured])->save();
            $this->cacheInvalidator->testimonials();

            return $testimonial->refresh();
        });
    }

    private function changeStatus(Testimonial $testimonial, TestimonialStatus $status): Testimonial
    {
        return DB::transaction(function () use ($testimonial, $status): Testimonial {
            $testimonial->forceFill([
                'status' => $status,
                'reviewed_at' => now(),
                'is_featured' => $status === TestimonialStatus::Approved ? $testimonial->is_featured : false,
            ])->save();

            $this->cacheInvalidator->testimonials();

            return $testimonial->refresh();
        });
    }

    private function reviewedAtFor(mixed $status, Testimonial $testimonial): mixed
    {
        $status = $status instanceof TestimonialStatus ? $status : TestimonialStatus::from((string) $status);

        return $status === TestimonialStatus::Pending ? null : ($testimonial->reviewed_at ?? now());
    }
}
