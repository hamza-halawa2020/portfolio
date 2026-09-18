<?php

namespace Tests\Feature;

use App\Data\PublicApi\PaginationOptions;
use App\Data\PublicApi\ProjectFilters;
use App\Enums\PublicationStatus;
use App\Http\Resources\Api\V1\ProjectDetailResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMedia;
use App\Models\Technology;
use App\Queries\PublicApi\ProjectQuery;
use App\Services\PublicApi\GetPublishedProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicApiArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_controllers_do_not_build_eloquent_queries(): void
    {
        foreach ($this->publicApiControllerFiles() as $file) {
            $source = file_get_contents($file);

            $this->assertStringNotContainsString('::query(', $source, $file);
            $this->assertStringNotContainsString('->where(', $source, $file);
            $this->assertStringNotContainsString('->with(', $source, $file);
            $this->assertStringNotContainsString('->withCount(', $source, $file);
            $this->assertStringNotContainsString('->orderBy', $source, $file);
            $this->assertStringNotContainsString('->paginate(', $source, $file);
            $this->assertStringNotContainsString('App\\Models\\', $source, $file);
        }
    }

    public function test_public_api_controllers_delegate_to_services(): void
    {
        foreach ($this->publicApiControllerFiles() as $file) {
            $source = file_get_contents($file);

            $this->assertStringContainsString('App\\Services\\PublicApi\\', $source, $file);
            $this->assertMatchesRegularExpression('/\\$service->handle\\(/', $source, $file);
        }
    }

    public function test_project_service_applies_published_visibility_independently_from_http(): void
    {
        Project::factory()->published()->create(['slug' => ['en' => 'public-project']]);
        Project::factory()->create(['slug' => ['en' => 'draft-project'], 'status' => PublicationStatus::Draft]);

        $service = app(GetPublishedProject::class);

        $this->assertSame('public-project', $service->handle('public-project', 'en')->localized('slug', 'en'));

        $this->getJson('/api/v1/projects/draft-project')->assertNotFound();
    }

    public function test_project_query_applies_filters_and_eager_loading(): void
    {
        $category = ProjectCategory::factory()->create(['slug' => ['en' => 'apps']]);
        $technology = Technology::factory()->create(['slug' => 'laravel']);
        $project = Project::factory()->for($category, 'category')->published()->featured()->create([
            'title' => ['en' => 'Filtered API'],
            'slug' => ['en' => 'filtered-api'],
        ]);
        $project->technologies()->attach($technology);
        Project::factory()->published()->create(['title' => ['en' => 'Other API']]);

        $filters = new ProjectFilters(
            locale: 'en',
            pagination: new PaginationOptions(perPage: 12),
            search: 'Filtered',
            categorySlug: 'apps',
            technologySlug: 'laravel',
            featured: true,
            sort: 'latest',
        );

        $result = app(ProjectQuery::class)->paginatePublished($filters);

        $this->assertSame(1, $result->total());
        $this->assertTrue($result->first()->relationLoaded('category'));
        $this->assertTrue($result->first()->relationLoaded('technologies'));
        $this->assertArrayHasKey('views_count', $result->first()->getAttributes());
    }

    public function test_project_resource_does_not_trigger_lazy_loading_queries(): void
    {
        Model::preventLazyLoading();

        $project = Project::factory()->published()->create();
        ProjectMedia::factory()->for($project)->create();

        $project = Project::query()
            ->with(['category', 'technologies', 'media', 'seoMetadata'])
            ->withCount(['views', 'likes'])
            ->findOrFail($project->id);
        $project->setRelation('relatedProjects', Project::query()->whereKey($project->id)->with(['category', 'technologies'])->withCount(['views', 'likes'])->get());

        DB::enableQueryLog();
        (new ProjectDetailResource($project))->resolve(request());

        $this->assertSame([], DB::getQueryLog());

        Model::preventLazyLoading(false);
    }

    public function test_invalid_transport_input_is_rejected_before_query_filters_run(): void
    {
        DB::enableQueryLog();

        $this->getJson('/api/v1/projects?search=x')->assertUnprocessable();

        $this->assertSame([], DB::getQueryLog());
    }

    /**
     * @return array<int, string>
     */
    private function publicApiControllerFiles(): array
    {
        return array_values(array_filter(glob(app_path('Http/Controllers/Api/V1/*.php'))));
    }
}
