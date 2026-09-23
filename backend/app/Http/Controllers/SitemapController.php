<?php

namespace App\Http\Controllers;

use App\Services\PublicApi\BuildSitemap;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(BuildSitemap $sitemap): Response
    {
        return response($sitemap->xml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
