<?php

namespace App\Queries\PublicApi;

use App\Models\SiteSetting;

final class SiteSettingQuery
{
    /**
     * @param  array<int, string>  $keys
     * @return array<string, mixed>
     */
    public function publicSettings(array $keys): array
    {
        return SiteSetting::query()
            ->whereIn('key', $keys)
            ->get()
            ->mapWithKeys(fn (SiteSetting $setting): array => [$setting->key => $setting->value])
            ->all();
    }
}
