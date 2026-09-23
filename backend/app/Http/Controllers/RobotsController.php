<?php

namespace App\Http\Controllers;

use App\Services\PublicApi\PublicSeoUrl;
use Illuminate\Http\Response;

final class RobotsController extends Controller
{
    public function __invoke(PublicSeoUrl $url): Response
    {
        $production = app()->environment('production') && (bool) config('portfolio.seo.indexing_enabled');

        $body = $production
            ? implode("\n", [
                'User-agent: *',
                'Allow: /',
                'Disallow: /admin',
                'Disallow: /api',
                'Sitemap: '.$url->absolute('/sitemap.xml'),
                '',
            ])
            : implode("\n", [
                'User-agent: *',
                'Disallow: /',
                '',
            ]);

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
