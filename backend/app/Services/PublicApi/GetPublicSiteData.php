<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\SiteData;
use App\Queries\PublicApi\PublicProfileQuery;
use App\Queries\PublicApi\SiteSettingQuery;

final readonly class GetPublicSiteData
{
    private const PUBLIC_SETTING_KEYS = [
        'site.profile_headline',
        'site.public_email',
        'site.whatsapp_url',
        'site.whatsapp_message',
        'site.default_seo',
    ];

    public function __construct(
        private SiteSettingQuery $settings,
        private PublicProfileQuery $profile,
    ) {}

    public function handle(string $locale): SiteData
    {
        return new SiteData(
            locale: $locale,
            settings: $this->settings->publicSettings(self::PUBLIC_SETTING_KEYS),
            navigation: [
                ['label' => ['en' => 'Projects', 'ar' => 'Ø§Ù„Ù…Ø´Ø§Ø±ÙŠØ¹'], 'path' => '/projects'],
                ['label' => ['en' => 'Blog', 'ar' => 'Ø§Ù„Ù…Ø¯ÙˆÙ†Ø©'], 'path' => '/blog'],
                ['label' => ['en' => 'Contact', 'ar' => 'ØªÙˆØ§ØµÙ„'], 'path' => '/contact'],
            ],
            services: $this->profile->services(limit: 6),
            skills: $this->profile->skills(limit: 12),
            socialLinks: $this->profile->socialLinks(),
        );
    }
}
