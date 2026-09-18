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
use App\Services\PublicApi\VisitorIdentity;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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

        $this->configurePublicWriteRateLimiters();
    }

    private function configurePublicWriteRateLimiters(): void
    {
        RateLimiter::for('public-project-views', fn (Request $request): Limit => Limit::perMinute((int) config('portfolio.rate_limits.project_views', 60))
            ->by('project-views:'.$this->visitorRateLimitKey($request)));

        RateLimiter::for('public-project-likes', fn (Request $request): Limit => Limit::perMinute((int) config('portfolio.rate_limits.project_likes', 30))
            ->by('project-likes:'.$this->visitorRateLimitKey($request)));

        RateLimiter::for('public-testimonials', fn (Request $request): Limit => Limit::perMinutes(60, (int) config('portfolio.rate_limits.testimonials', 5))
            ->by('testimonials:'.$this->visitorRateLimitKey($request)));

        RateLimiter::for('public-contact', fn (Request $request): Limit => Limit::perMinutes(60, (int) config('portfolio.rate_limits.contact', 5))
            ->by('contact:'.$this->visitorRateLimitKey($request)));
    }

    private function visitorRateLimitKey(Request $request): string
    {
        return app(VisitorIdentity::class)->rateLimitKey($request);
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
