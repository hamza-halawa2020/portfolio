# SEO.md

## SEO Architecture

SEO is a core project requirement. Public pages must be rendered through Angular SSR and must expose important content and metadata in the initial HTML.

## SSR and Prerendering Strategy

- SSR all public pages.
- Prerender stable static pages such as privacy where practical.
- Project and blog detail pages must be indexable and server-rendered.
- Do not rely on client-side JavaScript for titles, descriptions, or primary content.

FND-004 foundation status:

- Angular SSR is enabled.
- Hydration is configured with `provideClientHydration()`.
- Server entry point exists at `frontend/src/server.ts`.
- Server routes configuration exists at `frontend/src/app/app.routes.server.ts`.
- Production build generated browser and server output under `frontend/dist/portfolio-frontend`.
- Initial prerendered HTML contains `lang="en"`, title `Portfolio Platform`, semantic `<main>`, hydration state, and no `noindex`.

Detailed metadata, canonical URLs, `hreflang`, Open Graph, Twitter/X cards, JSON-LD, sitemap, robots, redirects, and localized URLs remain for the dedicated SEO implementation tasks.

BE-003 backend API status:

- Project and blog detail endpoints expose dashboard-managed SEO metadata through public API resources.
- Published-only filtering is enforced before project and blog detail payloads are returned.
- Draft, future, archived, or missing project/post slugs return HTTP 404.
- Sitemap, robots, canonical URL rendering, `hreflang`, Open Graph, Twitter/X card rendering, JSON-LD rendering, and redirects remain deferred to the dedicated SEO milestone and Angular SSR page work.

## URL and Localization Strategy

- Arabic and English pages use separate URLs.
- Each public page sets correct `lang` and `dir`.
- Canonical URLs are localized.
- `hreflang` links connect Arabic and English equivalents.
- Slugs are localized where practical.

## Metadata Strategy

- Dashboard-managed SEO metadata exists for pages, projects, posts, categories, and services.
- Editable fields include localized title, localized meta description, canonical override, Open Graph title/description/image, robots index/follow, structured-data fields, and redirect URL.
- Defaults come from site settings when a specific page lacks metadata.
- BE-003 exposes project and blog SEO metadata in read API detail responses; Angular SSR still needs to consume it and render tags into raw HTML.

## Structured Data Strategy

- `Person` for the portfolio owner.
- `WebSite` for the website.
- `Article` for blog details.
- `BreadcrumbList` for detail and nested pages.
- JSON-LD must appear in initial SSR HTML.

## Sitemap Behavior

- Generate XML sitemap for indexable localized public URLs.
- Include projects and posts only when published and indexable.
- Exclude dashboard/private routes and noindex content.

## Robots.txt Behavior

- Allow public pages.
- Disallow dashboard, private API paths, and internal preview paths.
- Reference the XML sitemap.

## Redirect Behavior

- Changed slugs can store redirect URLs.
- Old localized slugs should return 301 to current canonical URLs where configured.
- Removed content should return 404 or 410 according to final content policy.

## Image and Video Optimization

- Use WebP or AVIF images where possible.
- Use explicit dimensions and responsive image sizes.
- Lazy-load below-the-fold media.
- Use alt text for images.
- Use WebM/MP4 for videos, with poster images and no large autoplay.

## SEO Acceptance Criteria

- Titles, descriptions, canonical links, and `hreflang` links exist in raw HTML.
- Open Graph, Twitter/X cards, and JSON-LD exist where required.
- Sitemap and robots.txt validate.
- Arabic and English pages have separate valid URLs.
- 200, 301, 404, and 410 status codes behave correctly where applicable.
- Lighthouse SEO checks pass.
- Core Web Vitals are acceptable.
- No broken internal links, missing image alt text, duplicate titles/descriptions, accidental `noindex`, or indexable dashboard pages.

## SEO Testing Procedure

1. Fetch SSR HTML with JavaScript disabled or via plain HTTP client.
2. Inspect `<title>`, meta description, canonical, `hreflang`, Open Graph, Twitter/X card, and JSON-LD.
3. Validate structured data.
4. Validate `/sitemap.xml` and `/robots.txt`.
5. Verify localized URL pairs.
6. Verify 200, 301, 404, and 410 behavior.
7. Run Lighthouse SEO and Core Web Vitals checks.
8. Crawl internal links and image alt text.
9. Check for duplicate titles/descriptions and accidental `noindex`.
10. Confirm dashboard pages are not indexable.
