<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TestimonialIndexRequest;
use App\Http\Resources\Api\V1\TestimonialResource;
use App\Models\Project;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TestimonialController extends Controller
{
    use RespondsWithPublicApi;

    public function __invoke(TestimonialIndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $query = Testimonial::query()->approved()->with(['project.category', 'project.technologies']);

        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('project')) {
            $project = $this->findByLocalizedSlug(Project::query()->published()->get(), (string) $request->query('project'), $locale);
            throw_if(! $project, ValidationException::withMessages(['project' => 'The selected project is invalid.']));
            $query->where('project_id', $project->id);
        }

        $testimonials = $query->orderByDesc('is_featured')->latest()->paginate($request->perPage())->withQueryString();

        return TestimonialResource::collection($testimonials)
            ->additional(['meta' => ['locale' => $locale]])
            ->response()
            ->header('Cache-Control', "public, max-age={$this->publicReadTtl}");
    }
}
