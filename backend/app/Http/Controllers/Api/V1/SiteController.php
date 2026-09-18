<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPublicApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexRequest;
use App\Http\Resources\Api\V1\ServiceResource;
use App\Http\Resources\Api\V1\SkillResource;
use App\Http\Resources\Api\V1\SocialLinkResource;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SocialLink;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    use RespondsWithPublicApi;

    private const PUBLIC_SETTING_KEYS = [
        'site.profile_headline',
        'site.public_email',
        'site.whatsapp_url',
        'site.default_seo',
    ];

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $locale = $request->locale();
        $this->prepareLocale($request, $locale);

        $settings = SiteSetting::query()
            ->whereIn('key', self::PUBLIC_SETTING_KEYS)
            ->get()
            ->mapWithKeys(fn (SiteSetting $setting): array => [$setting->key => $setting->value])
            ->all();

        return $this->data([
            'settings' => $settings,
            'navigation' => [
                ['label' => ['en' => 'Projects', 'ar' => 'المشاريع'], 'path' => '/projects'],
                ['label' => ['en' => 'Blog', 'ar' => 'المدونة'], 'path' => '/blog'],
                ['label' => ['en' => 'Contact', 'ar' => 'تواصل'], 'path' => '/contact'],
            ],
            'services' => ServiceResource::collection(Service::query()->visible()->ordered()->limit(6)->get())->resolve($request),
            'skills' => SkillResource::collection(Skill::query()->visible()->ordered()->limit(12)->get())->resolve($request),
            'social_links' => SocialLinkResource::collection(SocialLink::query()->visible()->ordered()->get())->resolve($request),
        ], $locale);
    }
}
