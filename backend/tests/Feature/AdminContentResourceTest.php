<?php

namespace Tests\Feature;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMedia;
use App\Models\Technology;
use App\Models\User;
use App\Services\Admin\Content\BlogPostContentService;
use App\Services\Admin\Content\ProjectContentService;
use App\Services\Admin\Content\ProjectMediaContentService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminContentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_content_resources_are_registered_and_policy_protected(): void
    {
        $admin = User::factory()->administrator()->create();
        $ordinaryUser = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/projects')
            ->assertOk();

        $this->actingAs($ordinaryUser)
            ->get('/admin/projects')
            ->assertForbidden();

        $this->assertTrue(Gate::forUser($admin)->allows('create', Project::class));
        $this->assertFalse(Gate::forUser($ordinaryUser)->allows('create', Project::class));
        $this->assertContains(ProjectResource::class, Filament::getPanel('admin')->getResources());
    }

    public function test_project_content_service_preserves_translations_sanitizes_html_and_saves_seo(): void
    {
        $category = ProjectCategory::factory()->create();
        $technology = Technology::factory()->create();
        $project = Project::factory()->create([
            'title' => ['en' => 'Old English', 'ar' => 'Arabic title'],
            'slug' => ['en' => 'old-english', 'ar' => 'arabic-title'],
            'body' => ['en' => 'Body', 'ar' => 'Arabic body'],
        ]);

        app(ProjectContentService::class)->update($project, [
            'project_category_id' => $category->id,
            'title' => ['en' => 'New English'],
            'slug' => ['en' => 'New English Slug'],
            'summary' => ['en' => 'Summary'],
            'body' => ['en' => '<p>Hello</p><script>alert(1)</script><a href="javascript:alert(1)" onclick="x()">bad</a>'],
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 3,
            'technologies' => [$technology->id],
            'seo' => [
                'title' => ['en' => 'SEO title'],
                'description' => ['en' => 'SEO description'],
                'robots_index' => true,
                'robots_follow' => true,
            ],
        ]);

        $project->refresh();

        $this->assertSame('New English', $project->title['en']);
        $this->assertSame('Arabic title', $project->title['ar']);
        $this->assertSame('new-english-slug', $project->slug['en']);
        $this->assertStringNotContainsString('<script>', $project->body['en']);
        $this->assertStringNotContainsString('javascript:', $project->body['en']);
        $this->assertStringNotContainsString('onclick', $project->body['en']);
        $this->assertTrue($project->technologies()->whereKey($technology)->exists());
        $this->assertSame('SEO title', $project->seoMetadata->title['en']);
    }

    public function test_localized_slug_duplicates_are_rejected_before_persistence(): void
    {
        Project::factory()->create([
            'slug' => ['en' => 'duplicate', 'ar' => 'unique-ar'],
        ]);

        $this->expectException(ValidationException::class);

        app(ProjectContentService::class)->create([
            'title' => ['en' => 'Other', 'ar' => 'Other ar'],
            'slug' => ['en' => 'duplicate', 'ar' => 'other-ar'],
            'summary' => ['en' => 'Summary', 'ar' => 'Summary ar'],
            'body' => ['en' => 'Body', 'ar' => 'Body ar'],
            'status' => 'draft',
            'is_featured' => false,
            'sort_order' => 0,
            'technologies' => [],
            'seo' => [],
        ]);
    }

    public function test_blog_post_service_sanitizes_body_and_calculates_reading_time(): void
    {
        $post = app(BlogPostContentService::class)->create([
            'title' => ['en' => 'Post', 'ar' => 'Post ar'],
            'slug' => ['en' => 'post', 'ar' => 'post-ar'],
            'excerpt' => ['en' => 'Excerpt'],
            'body' => ['en' => str_repeat('word ', 450).'<iframe src="https://example.test"></iframe>'],
            'status' => 'draft',
            'is_featured' => false,
            'published_at' => null,
            'tags' => [],
            'seo' => [],
        ]);

        $this->assertSame(3, $post->reading_time_minutes);
        $this->assertStringNotContainsString('<iframe', $post->body['en']);
    }

    public function test_project_media_service_verifies_mime_and_cleans_replaced_files(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create();
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('projects/media', 'public');
        $newPath = UploadedFile::fake()->image('new.jpg')->store('projects/media', 'public');

        $media = ProjectMedia::factory()->for($project)->create([
            'path' => $oldPath,
            'mime_type' => 'image/jpeg',
            'file_size' => 10,
        ]);

        app(ProjectMediaContentService::class)->update($media, [
            'project_id' => $project->id,
            'path' => $newPath,
            'caption' => ['en' => 'Caption'],
            'alt_text' => ['en' => 'Alt'],
            'sort_order' => 1,
        ]);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
        $this->assertSame('image/jpeg', $media->refresh()->mime_type);

        $badPath = 'projects/media/bad.svg';
        Storage::disk('public')->put($badPath, '<svg><script>alert(1)</script></svg>');

        $this->expectException(ValidationException::class);

        app(ProjectMediaContentService::class)->create([
            'project_id' => $project->id,
            'path' => $badPath,
            'caption' => ['en' => 'Bad'],
            'alt_text' => ['en' => 'Bad'],
            'sort_order' => 2,
        ]);
    }
}
