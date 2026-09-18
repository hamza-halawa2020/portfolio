<?php

namespace App\Queries\PublicApi;

use App\Data\PublicApi\TestimonialFilters;
use App\Models\Testimonial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

final readonly class TestimonialQuery
{
    public function __construct(
        private ProjectQuery $projects,
    ) {}

    public function paginateApproved(TestimonialFilters $filters): LengthAwarePaginator
    {
        $query = Testimonial::query()->approved()->with(['project.category', 'project.technologies']);

        if ($filters->featured !== null) {
            $query->where('is_featured', $filters->featured);
        }

        if ($filters->projectSlug !== null) {
            $project = $this->projects->findPublishedFilterProject($filters->projectSlug, $filters->locale);
            throw_if(! $project, ValidationException::withMessages(['project' => 'The selected project is invalid.']));
            $query->where('project_id', $project->id);
        }

        return $query->orderByDesc('is_featured')->latest()->paginate($filters->pagination->perPage);
    }
}
