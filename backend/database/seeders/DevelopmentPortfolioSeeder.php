<?php

namespace Database\Seeders;

use App\Enums\AnalyticsEventType;
use App\Enums\ContactMessageStatus;
use App\Enums\ProjectMediaType;
use App\Enums\PublicationStatus;
use App\Enums\TestimonialStatus;
use App\Models\AnalyticsEvent;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\DailyAnalyticsSummary;
use App\Models\Experience;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectLike;
use App\Models\ProjectMedia;
use App\Models\ProjectView;
use App\Models\SeoMetadata;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\Admin\LocalDevelopmentAdministratorProvisioner;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DevelopmentPortfolioSeeder extends Seeder
{
    private array $projects = [];

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('DevelopmentPortfolioSeeder may only run in local or testing environments.');
        }

        $this->writeFixtureImages();
        app(LocalDevelopmentAdministratorProvisioner::class)->provisionFromConfig();
        $this->seedFixtureUser();

        $projectCategories = $this->seedProjectCategories();
        $technologies = $this->seedTechnologies();
        $this->seedProjects($projectCategories, $technologies);
        $this->seedProjectInteractions();
        $this->seedTestimonials();

        $blogCategories = $this->seedBlogCategories();
        $tags = $this->seedTags();
        $this->seedBlogPosts($blogCategories, $tags);

        $this->seedServices();
        $this->seedExperience();
        $this->seedSkills();
        $this->seedContactMessages();
        $this->seedSiteSettings();
        $this->seedSocialLinks();
        $this->seedPageSeoMetadata();
        $this->seedAnalyticsFixtures();
    }

    private function writeFixtureImages(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=');

        foreach ([
            'projects/fixtures/admin-analytics.png',
            'projects/fixtures/client-portal.png',
            'projects/fixtures/booking-dashboard.png',
            'projects/fixtures/learning-hub.png',
            'projects/fixtures/commerce-api.png',
            'projects/fixtures/operations-console.png',
            'blog/fixtures/backend-quality.png',
        ] as $path) {
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $png);
            }
        }
    }

    private function seedFixtureUser(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'fixture.viewer@example.test'],
            [
                'name' => 'Fictional Viewer',
                'email_verified_at' => now(),
                'password' => 'not-used-for-login',
                'is_admin' => false,
                'admin_granted_at' => null,
                'admin_granted_by' => null,
            ],
        );
    }

    private function seedProjectCategories(): array
    {
        $records = [
            ['saas', 'SaaS Platforms', 'منصات البرمجيات', 'Product dashboards and subscription tools.', 'لوحات منتجات وأدوات اشتراك.'],
            ['portals', 'Client Portals', 'بوابات العملاء', 'Secure self-service experiences.', 'تجارب خدمة ذاتية آمنة.'],
            ['automation', 'Workflow Automation', 'أتمتة سير العمل', 'Internal tools that reduce manual work.', 'أدوات داخلية تقلل العمل اليدوي.'],
        ];

        return collect($records)->mapWithKeys(function (array $record, int $index): array {
            [$key, $en, $ar, $descEn, $descAr] = $record;

            return [$key => ProjectCategory::query()->updateOrCreate(
                ['slug->en' => "dev-{$key}"],
                [
                    'name' => ['en' => $en, 'ar' => $ar],
                    'slug' => ['en' => "dev-{$key}", 'ar' => "dev-{$key}-ar"],
                    'description' => ['en' => $descEn, 'ar' => $descAr],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            )];
        })->all();
    }

    private function seedTechnologies(): array
    {
        return collect(['Laravel', 'Angular', 'MySQL', 'Redis', 'Filament', 'Playwright', 'TypeScript', 'Tailwind'])
            ->mapWithKeys(function (string $name, int $index): array {
                $technology = Technology::query()->firstOrNew(['name' => $name]);

                if (! $technology->exists) {
                    $technology->slug = 'dev-'.Str::slug($name);
                }

                $technology->forceFill([
                    'icon' => $technology->icon,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])->save();

                return [Str::slug($name) => $technology];
            })->all();
    }

    private function seedProjects(array $categories, array $technologies): void
    {
        $records = [
            ['admin-analytics', 'Admin Analytics Console', 'لوحة تحليلات الإدارة', 'saas', ['laravel', 'filament', 'mysql'], true],
            ['client-portal', 'Client Delivery Portal', 'بوابة تسليم العملاء', 'portals', ['laravel', 'angular', 'typescript'], true],
            ['booking-dashboard', 'Booking Operations Dashboard', 'لوحة عمليات الحجز', 'automation', ['laravel', 'mysql', 'redis'], false],
            ['learning-hub', 'Bilingual Learning Hub', 'مركز تعلم ثنائي اللغة', 'portals', ['angular', 'typescript', 'playwright'], false],
            ['commerce-api', 'Commerce API Platform', 'منصة واجهات التجارة', 'saas', ['laravel', 'mysql', 'redis'], false],
            ['operations-console', 'Operations Console', 'وحدة تحكم العمليات', 'automation', ['filament', 'laravel', 'playwright'], false],
        ];

        foreach ($records as $index => [$key, $titleEn, $titleAr, $categoryKey, $technologyKeys, $featured]) {
            $project = Project::query()->updateOrCreate(
                ['slug->en' => "dev-{$key}"],
                [
                    'project_category_id' => $categories[$categoryKey]->id,
                    'title' => ['en' => $titleEn, 'ar' => $titleAr],
                    'slug' => ['en' => "dev-{$key}", 'ar' => "dev-{$key}-ar"],
                    'summary' => ['en' => "Fictional case study for {$titleEn}.", 'ar' => "دراسة حالة تجريبية: {$titleAr}."],
                    'body' => ['en' => "<p>{$titleEn} is development fixture content for local testing.</p>", 'ar' => "<p>{$titleAr} محتوى تجريبي للتطوير المحلي.</p>"],
                    'role' => ['en' => 'Laravel full-stack developer', 'ar' => 'مطور Laravel شامل'],
                    'duration' => ['en' => (6 + $index).' weeks', 'ar' => (6 + $index).' أسابيع'],
                    'industry' => ['en' => 'Fictional professional services', 'ar' => 'خدمات مهنية تجريبية'],
                    'challenge' => ['en' => 'Create maintainable bilingual workflows.', 'ar' => 'إنشاء سير عمل ثنائي اللغة قابل للصيانة.'],
                    'solution' => ['en' => 'Use Laravel services, Filament, and Angular-ready APIs.', 'ar' => 'استخدام خدمات Laravel و Filament وواجهات مناسبة لـ Angular.'],
                    'features' => ['en' => 'Dashboards, filters, audit-friendly content, and safe public APIs.', 'ar' => 'لوحات، مرشحات، محتوى قابل للمراجعة، وواجهات عامة آمنة.'],
                    'development_challenges' => ['en' => 'Keeping fixtures coherent without real client data.', 'ar' => 'الحفاظ على بيانات تجريبية مترابطة بدون بيانات عملاء حقيقية.'],
                    'results' => ['en' => 'Reusable development records for dashboard and API testing.', 'ar' => 'سجلات تطوير قابلة لإعادة الاستخدام لاختبار اللوحة والواجهة.'],
                    'metrics' => ['en' => 'Fictional conversion and delivery metrics only.', 'ar' => 'مؤشرات تحويل وتسليم تجريبية فقط.'],
                    'cover_image_path' => "projects/fixtures/{$key}.png",
                    'status' => $index < 5 ? PublicationStatus::Published : PublicationStatus::Draft,
                    'is_featured' => $featured,
                    'sort_order' => $index + 1,
                    'published_at' => $index < 5 ? now()->subDays(20 - $index) : null,
                ],
            );

            $project->technologies()->syncWithoutDetaching(
                collect($technologyKeys)->map(fn (string $technologyKey): int => $technologies[$technologyKey]->id)->all(),
            );

            ProjectMedia::query()->updateOrCreate(
                ['project_id' => $project->id, 'path' => "projects/fixtures/{$key}.png"],
                [
                    'type' => ProjectMediaType::Image,
                    'poster_path' => null,
                    'caption' => ['en' => "{$titleEn} fixture image.", 'ar' => "صورة تجريبية لـ {$titleAr}."],
                    'alt_text' => ['en' => "Generated local preview for {$titleEn}.", 'ar' => "معاينة محلية مولدة لـ {$titleAr}."],
                    'mime_type' => 'image/png',
                    'file_size' => Storage::disk('public')->size("projects/fixtures/{$key}.png"),
                    'sort_order' => 1,
                ],
            );

            $this->upsertSeo($project, $titleEn, "Development SEO metadata for {$titleEn}.");
            $this->projects[$key] = $project;
        }
    }

    private function seedProjectInteractions(): void
    {
        foreach (array_values($this->projects) as $projectIndex => $project) {
            for ($day = 0; $day < 21; $day++) {
                $date = CarbonImmutable::today()->subDays($day);
                $views = max(1, 6 - $projectIndex + ($day % 3));

                for ($visitor = 1; $visitor <= $views; $visitor++) {
                    $hash = hash('sha256', "dev-project-view:{$project->id}:{$date->toDateString()}:{$visitor}");
                    ProjectView::query()->updateOrCreate(
                        [
                            'project_id' => $project->id,
                            'visitor_id_hash' => $hash,
                            'viewed_on' => $date->startOfDay()->toDateTimeString(),
                        ],
                        [
                            'ip_hash' => hash('sha256', "dev-ip:{$visitor}"),
                            'user_agent_hash' => hash('sha256', "dev-agent:{$visitor}"),
                            'viewed_at' => $date->setTime(9 + ($visitor % 8), $visitor % 60),
                        ],
                    );
                }
            }

            for ($visitor = 1; $visitor <= max(1, 4 - $projectIndex); $visitor++) {
                $hash = hash('sha256', "dev-project-like:{$project->id}:{$visitor}");
                ProjectLike::query()->updateOrCreate(
                    ['project_id' => $project->id, 'visitor_id_hash' => $hash],
                    [
                        'ip_hash' => hash('sha256', "dev-like-ip:{$visitor}"),
                        'user_agent_hash' => hash('sha256', "dev-like-agent:{$visitor}"),
                        'liked_at' => now()->subDays($visitor),
                    ],
                );
            }
        }
    }

    private function seedTestimonials(): void
    {
        $records = [
            ['a', 'Nadia Sample', TestimonialStatus::Approved, true, 'admin-analytics'],
            ['b', 'Omar Example', TestimonialStatus::Approved, false, 'client-portal'],
            ['c', 'Lina Fiction', TestimonialStatus::Pending, false, 'booking-dashboard'],
            ['d', 'Samir Demo', TestimonialStatus::Rejected, false, 'learning-hub'],
            ['e', 'Mona Fixture', TestimonialStatus::Archived, false, 'commerce-api'],
        ];

        foreach ($records as [$key, $name, $status, $featured, $projectKey]) {
            Testimonial::query()->updateOrCreate(
                ['contact_email' => "dev-testimonial-{$key}@example.test"],
                [
                    'project_id' => $this->projects[$projectKey]?->id,
                    'name' => $name,
                    'company' => 'Fictional Studio',
                    'position' => 'Product Lead',
                    'profile_image_path' => null,
                    'content' => ['en' => "Development testimonial {$key} for dashboard testing.", 'ar' => "شهادة تجريبية {$key} لاختبار لوحة التحكم."],
                    'rating' => $status === TestimonialStatus::Rejected ? 3 : 5,
                    'status' => $status,
                    'is_featured' => $featured,
                    'consented_at' => now()->subDays(12),
                    'reviewed_at' => $status === TestimonialStatus::Pending ? null : now()->subDays(5),
                ],
            );
        }
    }

    private function seedBlogCategories(): array
    {
        return collect([
            ['engineering', 'Engineering Notes', 'ملاحظات هندسية'],
            ['delivery', 'Delivery Lessons', 'دروس التسليم'],
        ])->mapWithKeys(fn (array $record, int $index): array => [
            $record[0] => BlogCategory::query()->updateOrCreate(
                ['slug->en' => "dev-{$record[0]}"],
                [
                    'name' => ['en' => $record[1], 'ar' => $record[2]],
                    'slug' => ['en' => "dev-{$record[0]}", 'ar' => "dev-{$record[0]}-ar"],
                    'description' => ['en' => 'Development-only blog category.', 'ar' => 'تصنيف مقالات تجريبي فقط.'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            ),
        ])->all();
    }

    private function seedTags(): array
    {
        return collect(['Laravel', 'Angular', 'Testing', 'Architecture', 'Performance'])
            ->mapWithKeys(fn (string $name): array => [
                Str::slug($name) => Tag::query()->updateOrCreate(
                    ['slug->en' => 'dev-'.Str::slug($name)],
                    ['name' => ['en' => $name, 'ar' => "وسم {$name}"], 'slug' => ['en' => 'dev-'.Str::slug($name), 'ar' => 'dev-'.Str::slug($name).'-ar']],
                ),
            ])->all();
    }

    private function seedBlogPosts(array $categories, array $tags): void
    {
        $titles = [
            ['service-layer-laravel', 'Keeping Laravel Controllers Thin', 'إبقاء متحكمات Laravel بسيطة', 'engineering', ['laravel', 'architecture']],
            ['angular-ssr-notes', 'Angular SSR Notes for Portfolios', 'ملاحظات Angular SSR للمعارض', 'engineering', ['angular', 'performance']],
            ['testing-public-apis', 'Testing Public APIs Without Leaks', 'اختبار الواجهات العامة بدون تسريب', 'engineering', ['testing', 'laravel']],
            ['filament-content-workflows', 'Filament Content Workflow Patterns', 'أنماط إدارة المحتوى في Filament', 'delivery', ['laravel', 'architecture']],
            ['bilingual-content-modeling', 'Bilingual Content Modeling', 'نمذجة المحتوى ثنائي اللغة', 'delivery', ['architecture']],
            ['safe-development-fixtures', 'Safe Development Fixtures', 'بيانات تطوير آمنة', 'delivery', ['testing']],
        ];

        foreach ($titles as $index => [$slug, $titleEn, $titleAr, $categoryKey, $tagKeys]) {
            $post = BlogPost::query()->updateOrCreate(
                ['slug->en' => "dev-{$slug}"],
                [
                    'blog_category_id' => $categories[$categoryKey]->id,
                    'title' => ['en' => $titleEn, 'ar' => $titleAr],
                    'slug' => ['en' => "dev-{$slug}", 'ar' => "dev-{$slug}-ar"],
                    'excerpt' => ['en' => "Development excerpt for {$titleEn}.", 'ar' => "مقتطف تجريبي: {$titleAr}."],
                    'body' => ['en' => "<p>{$titleEn} is fictional local development content.</p>", 'ar' => "<p>{$titleAr} محتوى تطوير محلي تجريبي.</p>"],
                    'cover_image_path' => 'blog/fixtures/backend-quality.png',
                    'status' => $index < 5 ? PublicationStatus::Published : PublicationStatus::Draft,
                    'is_featured' => $index < 2,
                    'reading_time_minutes' => 3 + ($index % 4),
                    'published_at' => $index < 5 ? now()->subDays(10 - $index) : null,
                ],
            );

            $post->tags()->syncWithoutDetaching(
                collect($tagKeys)->map(fn (string $tagKey): int => $tags[$tagKey]->id)->all(),
            );

            $this->upsertSeo($post, $titleEn, "Development SEO metadata for {$titleEn}.");
        }
    }

    private function seedServices(): void
    {
        foreach (['Laravel APIs', 'Filament Dashboards', 'Angular SSR', 'Testing Strategy'] as $index => $title) {
            $slug = Str::slug($title);
            $service = Service::query()->updateOrCreate(
                ['slug->en' => "dev-{$slug}"],
                [
                    'title' => ['en' => $title, 'ar' => "خدمة {$title}"],
                    'slug' => ['en' => "dev-{$slug}", 'ar' => "dev-{$slug}-ar"],
                    'description' => ['en' => "Development fixture service for {$title}.", 'ar' => 'خدمة تجريبية للتطوير المحلي.'],
                    'icon' => null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            $this->upsertSeo($service, $title, "Development SEO metadata for {$title}.");
        }
    }

    private function seedExperience(): void
    {
        foreach ([
            ['Senior Laravel Developer', 'Remote Product Studio', true],
            ['Full-stack Consultant', 'Fictional Delivery Lab', false],
            ['Backend Engineer', 'Demo Systems', false],
        ] as $index => [$title, $company, $current]) {
            Experience::query()->updateOrCreate(
                ['sort_order' => $index + 1],
                [
                    'title' => ['en' => $title, 'ar' => "دور {$title}"],
                    'company' => ['en' => $company, 'ar' => "شركة {$company}"],
                    'location' => ['en' => 'Remote', 'ar' => 'عن بعد'],
                    'description' => ['en' => 'Fictional experience for local layout and dashboard testing.', 'ar' => 'خبرة تجريبية لاختبار التخطيط ولوحة التحكم.'],
                    'starts_at' => now()->subYears(5 - $index)->toDateString(),
                    'ends_at' => $current ? null : now()->subYears(4 - $index)->toDateString(),
                    'is_current' => $current,
                ],
            );
        }
    }

    private function seedSkills(): void
    {
        foreach (['Laravel', 'PHP', 'Angular', 'TypeScript', 'MySQL', 'Testing', 'Filament', 'API Design'] as $index => $name) {
            Skill::query()->updateOrCreate(
                ['group' => $index < 4 ? 'engineering' : 'delivery', 'sort_order' => $index + 1],
                ['name' => ['en' => $name, 'ar' => "مهارة {$name}"], 'level' => 70 + ($index % 4) * 5, 'is_active' => true],
            );
        }
    }

    private function seedContactMessages(): void
    {
        foreach (ContactMessageStatus::cases() as $index => $status) {
            ContactMessage::query()->updateOrCreate(
                ['email' => "dev-contact-{$status->value}@example.test"],
                [
                    'name' => 'Fictional Contact '.($index + 1),
                    'phone' => null,
                    'company' => 'Example Inquiry Studio',
                    'project_type' => ['website', 'api', 'dashboard', 'consulting'][$index] ?? 'website',
                    'budget_range' => ['small', 'medium', 'enterprise', 'maintenance'][$index] ?? 'small',
                    'message' => 'Development-only private contact message. No email delivery is claimed.',
                    'status' => $status,
                    'admin_notes' => $status === ContactMessageStatus::New ? null : 'Development-only private admin note.',
                    'consented_at' => now()->subDays($index + 1),
                    'created_at' => now()->subDays($index + 1),
                ],
            );
        }
    }

    private function seedSiteSettings(): void
    {
        foreach ([
            'site.profile_headline' => ['en' => 'Fictional Laravel Developer', 'ar' => 'مطور Laravel تجريبي'],
            'site.public_email' => ['en' => 'hello@example.test', 'ar' => 'hello@example.test'],
            'site.whatsapp_url' => ['en' => 'https://example.test/whatsapp', 'ar' => 'https://example.test/whatsapp'],
            'site.default_seo' => ['en' => 'Development portfolio fixtures', 'ar' => 'بيانات معرض أعمال تجريبية'],
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function seedSocialLinks(): void
    {
        foreach (['GitHub', 'LinkedIn', 'Email'] as $index => $label) {
            SocialLink::query()->updateOrCreate(
                ['label' => $label],
                ['url' => 'https://example.test/'.Str::slug($label), 'icon' => null, 'sort_order' => $index + 1, 'is_active' => true],
            );
        }
    }

    private function seedPageSeoMetadata(): void
    {
        foreach (['home', 'about', 'projects', 'blog', 'contact'] as $page) {
            SeoMetadata::query()->updateOrCreate(
                ['page_key' => "dev-{$page}"],
                [
                    'title' => ['en' => Str::headline($page).' | Development Portfolio', 'ar' => "صفحة {$page} التجريبية"],
                    'description' => ['en' => "Development SEO metadata for {$page}.", 'ar' => "بيانات SEO تجريبية لصفحة {$page}."],
                    'canonical_url' => "https://example.test/{$page}",
                    'og_title' => ['en' => Str::headline($page), 'ar' => "صفحة {$page}"],
                    'og_description' => ['en' => 'Development-only metadata.', 'ar' => 'بيانات وصفية تجريبية فقط.'],
                    'og_image_path' => null,
                    'robots_index' => true,
                    'robots_follow' => true,
                    'structured_data' => ['@type' => 'WebPage', 'fixture' => true],
                    'redirect_url' => null,
                ],
            );
        }
    }

    private function seedAnalyticsFixtures(): void
    {
        for ($day = 0; $day < 28; $day++) {
            $date = CarbonImmutable::today()->subDays($day);
            $visits = 10 + ($day % 8);

            for ($visitor = 1; $visitor <= $visits; $visitor++) {
                AnalyticsEvent::query()->updateOrCreate(
                    [
                        'event_type' => AnalyticsEventType::PageView,
                        'visitor_id_hash' => hash('sha256', "dev-analytics:{$date->toDateString()}:{$visitor}"),
                        'url' => '/dev-fixture/'.($visitor % 5),
                        'occurred_at' => $date->setTime(8 + ($visitor % 10), $visitor % 60),
                    ],
                    [
                        'ip_hash' => hash('sha256', "dev-analytics-ip:{$visitor}"),
                        'user_agent_hash' => hash('sha256', "dev-analytics-agent:{$visitor}"),
                        'referrer_domain' => $visitor % 3 === 0 ? 'example.test' : null,
                        'device_category' => ['desktop', 'mobile', 'tablet'][$visitor % 3],
                        'browser' => ['Chrome', 'Firefox', 'Safari', 'Edge'][$visitor % 4],
                        'country' => ['EG', 'US', 'AE'][$visitor % 3],
                    ],
                );
            }

            foreach ([
                'page_views' => $visits,
                'unique_visitors' => $visits,
                'project_views' => max(1, $visits - 3),
                'project_likes' => max(1, intdiv($visits, 4)),
                'contact_submissions' => $day % 5 === 0 ? 1 : 0,
            ] as $metric => $value) {
                DailyAnalyticsSummary::query()->updateOrCreate(
                    ['date' => $date->startOfDay()->toDateTimeString(), 'metric' => $metric, 'dimension' => 'development_fixture'],
                    ['value' => $value],
                );
            }
        }
    }

    private function upsertSeo(object $model, string $title, string $description): void
    {
        SeoMetadata::query()->updateOrCreate(
            ['seoable_type' => $model::class, 'seoable_id' => $model->id],
            [
                'page_key' => null,
                'title' => ['en' => $title, 'ar' => "SEO {$title}"],
                'description' => ['en' => $description, 'ar' => 'وصف SEO تجريبي.'],
                'canonical_url' => 'https://example.test/'.Str::slug($title),
                'og_title' => ['en' => $title, 'ar' => "مشاركة {$title}"],
                'og_description' => ['en' => $description, 'ar' => 'وصف مشاركة تجريبي.'],
                'og_image_path' => null,
                'robots_index' => true,
                'robots_follow' => true,
                'structured_data' => ['@type' => 'CreativeWork', 'fixture' => true],
                'redirect_url' => null,
            ],
        );
    }
}
