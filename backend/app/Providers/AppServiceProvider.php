<?php

namespace App\Providers;

use App\Models\AnalyticsEvent;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactAttachment;
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
use App\Policies\DashboardPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->dashboardPolicyModels() as $model) {
            Gate::policy($model, DashboardPolicy::class);
        }
    }

    /**
     * @return array<int, class-string>
     */
    private function dashboardPolicyModels(): array
    {
        return [
            AnalyticsEvent::class,
            BlogCategory::class,
            BlogPost::class,
            ContactAttachment::class,
            ContactMessage::class,
            DailyAnalyticsSummary::class,
            Experience::class,
            Project::class,
            ProjectCategory::class,
            ProjectLike::class,
            ProjectMedia::class,
            ProjectView::class,
            SeoMetadata::class,
            Service::class,
            SiteSetting::class,
            Skill::class,
            SocialLink::class,
            Tag::class,
            Technology::class,
            Testimonial::class,
        ];
    }
}
