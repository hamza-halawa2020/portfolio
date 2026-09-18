<?php

namespace Database\Seeders;

use App\Enums\PublicationStatus;
use App\Enums\TestimonialStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Experience;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectMedia;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SocialLink;
use App\Models\Technology;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class DevelopmentPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $category = ProjectCategory::query()->updateOrCreate(
            ['slug->en' => 'fictional-saas'],
            [
                'name' => ['en' => 'Fictional SaaS', 'ar' => 'برمجيات خدمية تجريبية'],
                'slug' => ['en' => 'fictional-saas', 'ar' => 'barmajiyat-khadamiya-tajreebiya'],
                'description' => ['en' => 'Demo category for local development.', 'ar' => 'تصنيف تجريبي للتطوير المحلي.'],
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        $laravel = Technology::query()->updateOrCreate(
            ['slug' => 'laravel'],
            ['name' => 'Laravel', 'icon' => null, 'sort_order' => 1, 'is_active' => true],
        );

        $angular = Technology::query()->updateOrCreate(
            ['slug' => 'angular'],
            ['name' => 'Angular', 'icon' => null, 'sort_order' => 2, 'is_active' => true],
        );

        $project = Project::query()->updateOrCreate(
            ['slug->en' => 'fictional-portfolio-platform'],
            [
                'project_category_id' => $category->id,
                'title' => ['en' => 'Fictional Portfolio Platform', 'ar' => 'منصة أعمال تجريبية'],
                'slug' => ['en' => 'fictional-portfolio-platform', 'ar' => 'minasat-aamal-tajreebiya'],
                'summary' => ['en' => 'A fictional bilingual portfolio case study.', 'ar' => 'دراسة حالة تجريبية ثنائية اللغة.'],
                'body' => ['en' => 'This fictional project is seeded for local development only.', 'ar' => 'هذا المشروع التجريبي مخصص للتطوير المحلي فقط.'],
                'role' => ['en' => 'Full-stack Laravel developer', 'ar' => 'مطوّر Laravel شامل'],
                'duration' => ['en' => '6 weeks', 'ar' => 'ستة أسابيع'],
                'industry' => ['en' => 'Creative services', 'ar' => 'خدمات إبداعية'],
                'challenge' => ['en' => 'Organize bilingual content safely.', 'ar' => 'تنظيم المحتوى ثنائي اللغة بأمان.'],
                'solution' => ['en' => 'Use Laravel APIs and Angular SSR.', 'ar' => 'استخدام Laravel API و Angular SSR.'],
                'features' => ['en' => 'Dashboard content management and public pages.', 'ar' => 'إدارة محتوى ولوحات عامة.'],
                'development_challenges' => ['en' => 'Keeping local fixtures fictional and private.', 'ar' => 'الحفاظ على بيانات تجريبية وآمنة.'],
                'results' => ['en' => 'Reusable local development content.', 'ar' => 'محتوى تطوير محلي قابل لإعادة الاستخدام.'],
                'metrics' => ['en' => 'Fictional metrics only.', 'ar' => 'مؤشرات تجريبية فقط.'],
                'cover_image_path' => 'projects/fictional-cover.webp',
                'status' => PublicationStatus::Published,
                'is_featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ],
        );
        $project->technologies()->syncWithoutDetaching([$laravel->id, $angular->id]);

        ProjectMedia::query()->updateOrCreate(
            ['project_id' => $project->id, 'path' => 'projects/fictional-cover.webp'],
            [
                'type' => 'image',
                'poster_path' => null,
                'caption' => ['en' => 'Fictional project cover.', 'ar' => 'غلاف مشروع تجريبي.'],
                'alt_text' => ['en' => 'Abstract preview for a fictional project.', 'ar' => 'معاينة تجريبية لمشروع خيالي.'],
                'mime_type' => 'image/webp',
                'file_size' => 128000,
                'sort_order' => 1,
            ],
        );

        Service::query()->updateOrCreate(
            ['slug->en' => 'laravel-api-development'],
            [
                'title' => ['en' => 'Laravel API Development', 'ar' => 'تطوير واجهات Laravel'],
                'slug' => ['en' => 'laravel-api-development', 'ar' => 'tatweer-wajihat-laravel'],
                'description' => ['en' => 'Fictional service entry for local development.', 'ar' => 'خدمة تجريبية للتطوير المحلي.'],
                'icon' => null,
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Skill::query()->updateOrCreate(
            ['group' => 'backend', 'sort_order' => 1],
            ['name' => ['en' => 'Laravel', 'ar' => 'لارافيل'], 'level' => 90, 'is_active' => true],
        );

        Experience::query()->updateOrCreate(
            ['sort_order' => 1],
            [
                'title' => ['en' => 'Fictional Senior Developer', 'ar' => 'مطوّر أول تجريبي'],
                'company' => ['en' => 'Demo Studio', 'ar' => 'استوديو تجريبي'],
                'location' => ['en' => 'Remote', 'ar' => 'عن بعد'],
                'description' => ['en' => 'Fictional experience for layout and dashboard testing.', 'ar' => 'خبرة تجريبية لاختبار الواجهات.'],
                'starts_at' => now()->subYears(2)->toDateString(),
                'ends_at' => null,
                'is_current' => true,
            ],
        );

        $blogCategory = BlogCategory::query()->updateOrCreate(
            ['slug->en' => 'engineering-notes'],
            [
                'name' => ['en' => 'Engineering Notes', 'ar' => 'ملاحظات هندسية'],
                'slug' => ['en' => 'engineering-notes', 'ar' => 'mulahazat-handasiya'],
                'description' => ['en' => 'Fictional blog category.', 'ar' => 'تصنيف مقالات تجريبي.'],
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        BlogPost::query()->updateOrCreate(
            ['slug->en' => 'building-fictional-platforms'],
            [
                'blog_category_id' => $blogCategory->id,
                'title' => ['en' => 'Building Fictional Platforms', 'ar' => 'بناء منصات تجريبية'],
                'slug' => ['en' => 'building-fictional-platforms', 'ar' => 'binaa-minasat-tajreebiya'],
                'excerpt' => ['en' => 'A local-only fictional article.', 'ar' => 'مقال تجريبي للتطوير المحلي فقط.'],
                'body' => ['en' => 'This content is fictional and safe for development.', 'ar' => 'هذا المحتوى تجريبي وآمن للتطوير.'],
                'cover_image_path' => 'blog/fictional-cover.webp',
                'status' => PublicationStatus::Published,
                'is_featured' => true,
                'reading_time_minutes' => 3,
                'published_at' => now(),
            ],
        );

        Testimonial::query()->updateOrCreate(
            ['contact_email' => 'fictional.client@example.test'],
            [
                'project_id' => $project->id,
                'name' => 'Fictional Client',
                'company' => 'Example Studio',
                'position' => 'Product Lead',
                'profile_image_path' => null,
                'content' => ['en' => 'A fictional testimonial for local development.', 'ar' => 'شهادة تجريبية للتطوير المحلي.'],
                'rating' => 5,
                'status' => TestimonialStatus::Approved,
                'is_featured' => true,
                'consented_at' => now(),
                'reviewed_at' => now(),
            ],
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'site.profile_headline'],
            ['value' => ['en' => 'Fictional Laravel Developer', 'ar' => 'مطوّر Laravel تجريبي']],
        );

        SocialLink::query()->updateOrCreate(
            ['label' => 'GitHub'],
            ['url' => 'https://example.test/github', 'icon' => null, 'sort_order' => 1, 'is_active' => true],
        );
    }
}
