<?php

namespace App\Services\Admin\Content;

use App\Models\SiteSetting;
use App\Models\SocialLink;

class SiteContentService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createSiteSetting(array $data): SiteSetting
    {
        return SiteSetting::query()->create($this->normalizeSetting($data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSiteSetting(SiteSetting $setting, array $data): SiteSetting
    {
        $setting->update($this->normalizeSetting($data));

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createSocialLink(array $data): SocialLink
    {
        return SocialLink::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSocialLink(SocialLink $link, array $data): SocialLink
    {
        $link->update($data);

        return $link;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeSetting(array $data): array
    {
        $value = $data['value'] ?? [];

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : ['en' => $value];
        }

        $data['value'] = $value;

        return $data;
    }
}
