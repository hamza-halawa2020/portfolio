<?php

namespace App\Services\Admin\Content;

use Illuminate\Support\Facades\Cache;

class PublicContentCacheInvalidator
{
    /**
     * @param  array<int, string>  $keys
     */
    public function forget(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    public function testimonials(): void
    {
        $this->forget([
            'public-api:testimonials',
            'public-api:site',
        ]);
    }

    public function contactInbox(): void
    {
        $this->forget([
            'admin:contact-inbox',
            'admin:analytics',
        ]);
    }
}
