<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\SitemapUrl;
use App\Models\BlogPost;
use App\Models\Project;
use DOMDocument;
use DOMElement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

final readonly class BuildSitemap
{
    /**
     * @return list<SitemapUrl>
     */
    public function entries(): array
    {
        return [
            ...$this->staticEntries(),
            ...$this->projectEntries(),
            ...$this->blogEntries(),
        ];
    }

    public function xml(): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $urlset = $document->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        $document->appendChild($urlset);

        foreach ($this->entries() as $entry) {
            $url = $document->createElement('url');
            $this->appendText($document, $url, 'loc', app(PublicSeoUrl::class)->absolute($entry->path));
            $this->appendText($document, $url, 'lastmod', $entry->lastModified->toDateString());

            foreach ($entry->alternates as $hreflang => $path) {
                $link = $document->createElement('xhtml:link');
                $link->setAttribute('rel', 'alternate');
                $link->setAttribute('hreflang', $hreflang);
                $link->setAttribute('href', app(PublicSeoUrl::class)->absolute($path));
                $url->appendChild($link);
            }

            $urlset->appendChild($url);
        }

        return $document->saveXML() ?: '';
    }

    /**
     * @return list<SitemapUrl>
     */
    private function staticEntries(): array
    {
        $updated = Carbon::now('UTC');
        $segments = ['', 'projects', 'about', 'services', 'blog', 'contact', 'privacy'];

        return array_map(
            fn (string $segment): SitemapUrl => new SitemapUrl(
                path: $segment === '' ? '/en' : "/en/{$segment}",
                lastModified: $updated,
                alternates: $this->staticAlternates($segment),
            ),
            $segments,
        );
    }

    /**
     * @return array<string, string>
     */
    private function staticAlternates(string $segment): array
    {
        $suffix = $segment === '' ? '' : "/{$segment}";

        return [
            'en' => "/en{$suffix}",
            'ar' => "/ar{$suffix}",
            'x-default' => "/en{$suffix}",
        ];
    }

    /**
     * @return list<SitemapUrl>
     */
    private function projectEntries(): array
    {
        return Project::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with('seoMetadata')
            ->get()
            ->filter(fn (Project $project): bool => $project->seoMetadata?->robots_index !== false)
            ->flatMap(fn (Project $project): array => $this->localizedEntries($project, 'projects'))
            ->values()
            ->all();
    }

    /**
     * @return list<SitemapUrl>
     */
    private function blogEntries(): array
    {
        return BlogPost::query()
            ->published()
            ->where(fn (Builder $query): Builder => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with('seoMetadata')
            ->get()
            ->filter(fn (BlogPost $post): bool => $post->seoMetadata?->robots_index !== false)
            ->flatMap(fn (BlogPost $post): array => $this->localizedEntries($post, 'blog'))
            ->values()
            ->all();
    }

    /**
     * @return list<SitemapUrl>
     */
    private function localizedEntries(Project|BlogPost $model, string $section): array
    {
        $slugs = array_filter([
            'en' => $model->localized('slug', 'en', false),
            'ar' => $model->localized('slug', 'ar', false),
        ]);

        $alternates = [];
        foreach ($slugs as $locale => $slug) {
            $alternates[$locale] = "/{$locale}/{$section}/{$slug}";
        }

        if (isset($alternates['en'])) {
            $alternates['x-default'] = $alternates['en'];
        }

        return array_map(
            fn (string $locale, string $slug): SitemapUrl => new SitemapUrl(
                path: "/{$locale}/{$section}/{$slug}",
                lastModified: $model->updated_at ?? $model->created_at ?? Carbon::now('UTC'),
                alternates: $alternates,
            ),
            array_keys($slugs),
            array_values($slugs),
        );
    }

    private function appendText(DOMDocument $document, DOMElement $parent, string $name, string $value): void
    {
        $element = $document->createElement($name);
        $element->appendChild($document->createTextNode($value));
        $parent->appendChild($element);
    }
}
