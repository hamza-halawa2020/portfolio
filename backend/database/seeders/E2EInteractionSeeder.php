<?php

namespace Database\Seeders;

use App\Enums\PublicationStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class E2EInteractionSeeder extends Seeder
{
    public function run(): void
    {
        abort_if(App::isProduction(), 403, 'The E2E interaction seeder cannot run in production.');

        $category = ProjectCategory::factory()->create([
            'name' => ['en' => 'E2E Case Studies', 'ar' => 'دراسات اختبارية'],
            'slug' => ['en' => 'e2e-case-studies', 'ar' => 'dirasat-ikhtibariya'],
            'sort_order' => 1,
        ]);

        $technology = Technology::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'sort_order' => 1,
        ]);

        $projectDefinitions = [
            'e2e-project-view' => 'E2E view project',
            'e2e-project-view-failure' => 'E2E view failure project',
            'e2e-project-like' => 'E2E like project',
            'e2e-project-rapid' => 'E2E rapid like project',
            'e2e-project-like-failure' => 'E2E like failure project',
            'e2e-project-testimonial' => 'E2E testimonial project',
        ];

        foreach (['', '-desktop-chromium', '-mobile-chromium'] as $slugSuffix) {
            foreach ($projectDefinitions as $slug => $title) {
                $project = Project::factory()
                    ->for($category, 'category')
                    ->published()
                    ->create([
                        'title' => ['en' => $title, 'ar' => "مشروع {$title}"],
                        'slug' => ['en' => "{$slug}{$slugSuffix}", 'ar' => "{$slug}{$slugSuffix}-ar"],
                        'summary' => ['en' => "Summary for {$title}.", 'ar' => "ملخص {$title}."],
                        'body' => ['en' => "Body for {$title}.", 'ar' => "تفاصيل {$title}."],
                        'challenge' => ['en' => "Challenge for {$title}.", 'ar' => "تحدي {$title}."],
                        'solution' => ['en' => "Solution for {$title}.", 'ar' => "حل {$title}."],
                        'results' => ['en' => "Results for {$title}.", 'ar' => "نتائج {$title}."],
                        'status' => PublicationStatus::Published,
                        'cover_image_path' => null,
                        'sort_order' => 1,
                    ]);

                $project->technologies()->attach($technology);
            }
        }

        $blogCategory = BlogCategory::factory()->create([
            'name' => ['en' => 'E2E Notes', 'ar' => 'ملاحظات اختبارية'],
            'slug' => ['en' => 'e2e-notes', 'ar' => 'mulahazat-ikhtibariya'],
        ]);

        BlogPost::factory()
            ->for($blogCategory, 'category')
            ->published()
            ->create([
                'title' => ['en' => 'E2E public note', 'ar' => 'مقال اختباري عام'],
                'slug' => ['en' => 'e2e-public-note', 'ar' => 'maqal-ikhtibari-aam'],
                'cover_image_path' => null,
            ]);

        Service::factory()->create([
            'title' => ['en' => 'E2E service', 'ar' => 'خدمة اختبارية'],
            'slug' => ['en' => 'e2e-service', 'ar' => 'khidma-ikhtibariya'],
            'description' => ['en' => 'Fictional E2E service.', 'ar' => 'خدمة اختبارية خيالية.'],
            'sort_order' => 1,
        ]);

        foreach ([
            'site.profile_headline' => ['en' => 'E2E Portfolio', 'ar' => 'ملف اختباري'],
            'site.public_email' => ['en' => 'hello@example.test', 'ar' => 'hello@example.test'],
            'site.whatsapp_url' => ['en' => 'https://wa.me/15550101010', 'ar' => 'https://wa.me/15550101010'],
            'site.whatsapp_message' => [
                'en' => 'Hello, I would like to discuss an E2E project.',
                'ar' => 'مرحبا، أود مناقشة مشروع اختباري.',
            ],
            'site.default_seo' => ['en' => 'E2E SEO', 'ar' => 'SEO اختباري'],
        ] as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
