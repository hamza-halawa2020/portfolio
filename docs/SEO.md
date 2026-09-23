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

Detailed metadata, canonical URLs, `hreflang`, Open Graph, Twitter/X cards, JSON-LD, sitemap, robots, root redirect behavior, and localized URLs are implemented through SEO-001. Old-slug redirect policy and Lighthouse/Core Web Vitals scoring remain future verification work.

FE-001 foundation status:

- Localized public route structure exists under `/en/...` and `/ar/...`.
- Angular SSR server rendering is configured for home, projects, project details, about, services, blog, blog details, contact, privacy, and wildcard not-found routes.
- Dynamic project and blog detail routes use `RenderMode.Server`, so they are not silently omitted from production output when slugs are unknown at build time.
- A centralized Angular `SeoService` writes SSR-visible title, description, canonical URL, robots metadata, and static-page language alternates.
- Canonical URLs use `publicSiteConfig.publicOrigin`; it can be set through `PORTFOLIO_PUBLIC_ORIGIN` during SSR or `globalThis.PORTFOLIO_PUBLIC_CONFIG.publicOrigin` in a host-provided runtime config. The default placeholder is `https://example.com` and must be replaced before production indexing.
- Raw SSR checks on 2026-09-23 verified localized `lang`, `dir`, title, meta description, canonical, static-page `hreflang`, H1, and HTTP 404 behavior.
- FE-001 placeholder pages are intentionally `noindex` until real public page content and final production SEO decisions are implemented.
- SEO-001 added Open Graph, Twitter/X cards, JSON-LD, sitemap, robots.txt, production indexing rules, localized dynamic slug alternates, and root redirect behavior. Lighthouse/crawl validation remains future QA/deployment work when tooling is available.

FE-002 localization/theme status:

- Arabic and English SSR output continues to render with correct initial `lang` and `dir` attributes.
- Localization fallback is deterministic and server-safe: missing dynamic localized values fall back to English instead of changing after hydration based on browser-only detection.
- Static page language alternates remain emitted only for real corresponding static routes.
- Project and blog detail language switching has a typed strategy for future API-provided localized slug mappings. Until mappings are available, dynamic detail routes do not emit `hreflang` alternates and language switching falls back to the target listing page.
- Theme preference state is client preference only and is not serialized as private visitor-specific data in SSR HTML.

BE-003 backend API status:

- Project and blog detail endpoints expose dashboard-managed SEO metadata through public API resources.
- Published-only filtering is enforced before project and blog detail payloads are returned.
- Draft, future, archived, or missing project/post slugs return HTTP 404.
- SEO-001 completed sitemap, robots, canonical URL rendering, `hreflang`, Open Graph, Twitter/X card rendering, JSON-LD rendering, and root redirect behavior. Old-slug redirects remain future editorial policy work.

PAGE-001 public page status:

- Core public pages now render API-backed primary content through Angular SSR for home, projects, project detail, about, services, blog, blog detail, contact, privacy, and 404 routes.
- Completed public content pages use `index, follow` robots metadata in raw SSR HTML. Localized 404/error-style routes remain non-indexable where appropriate.
- Page titles, descriptions, canonical URLs, and static-route alternates are rendered by the existing Angular `SeoService`.
- Detail pages use localized route slugs for fetching, but do not emit dynamic cross-locale `hreflang` alternates until API responses provide explicit corresponding localized slug mappings.
- PAGE-001 raw HTML verification passed for English and Arabic API-backed routes, including `lang`, `dir`, rendered content cards, and `index, follow` robots metadata.
- Open Graph, Twitter/X cards, JSON-LD, sitemap.xml, robots.txt, and root redirect behavior were implemented in SEO-001. Lighthouse/Core Web Vitals and full crawl validation remain QA/deployment verification items when Lighthouse is available.

SEO-001 implementation status:

- Angular SSR now emits localized title, description, robots, absolute canonical, Open Graph, Twitter Card, `hreflang` for real equivalents, and `x-default` where applicable.
- Detail pages use `localized_slugs` from the API for dynamic project/blog alternates and language switching. Missing dynamic mappings result in no dynamic alternates rather than reusing a slug across languages.
- JSON-LD is generated during SSR and refreshed after client navigation. Home emits `WebSite` and `Person`; nested pages emit `BreadcrumbList`; blog details emit `BlogPosting`; project details emit `CreativeWork` using real page data only.
- JSON-LD values are serialized with `<` escaped to reduce script-injection risk. Managed JSON-LD blocks are cleared before new blocks are written to prevent duplicates.
- `/sitemap.xml` is generated by Laravel from authoritative database records. It includes localized static routes, published/indexable projects, and published/indexable blog posts with `lastmod`, localized alternates, and `x-default`.
- `/robots.txt` is generated by Laravel and is environment-aware. Local/testing/staging block indexing. Production allows intended public pages and references the sitemap only when `APP_ENV=production` and `PUBLIC_INDEXING_ENABLED=true`.
- The static Laravel `public/robots.txt` file was removed so it cannot shadow the environment-aware controller.
- The Angular SSR root route redirects permanently with HTTP 301 to `/en`.
- Lighthouse could not be run in this environment because Lighthouse is not installed locally and `npx lighthouse --version` timed out while trying to resolve it. Playwright/browser checks and direct raw-HTML checks were used as the local equivalent for metadata, hydration, console, and navigation behavior.

## URL and Localization Strategy

- Arabic and English pages use separate URLs.
- Each public page sets correct `lang` and `dir`.
- Canonical URLs are localized.
- `hreflang` links connect Arabic and English equivalents only where real routes or API-provided localized slug mappings exist.
- Slugs are localized where practical.

## Metadata Strategy

- Dashboard-managed SEO metadata exists for pages, projects, posts, categories, and services.
- Editable fields include localized title, localized meta description, canonical override, Open Graph title/description/image, robots index/follow, structured-data fields, and redirect URL.
- Defaults come from site settings when a specific page lacks metadata.
- BE-003 exposes project and blog SEO metadata in read API detail responses. SEO-001 consumes it in Angular SSR for detail titles, descriptions, robots flags, canonical overrides, and social image metadata when valid.

## Structured Data Strategy

- `Person` for the portfolio owner.
- `WebSite` for the website.
- `BlogPosting` for blog details.
- `CreativeWork` for project details where real project data exists.
- `BreadcrumbList` for detail and nested pages.
- JSON-LD must appear in initial SSR HTML.

## Sitemap Behavior

- Generate XML sitemap for indexable localized public URLs from Laravel.
- Include projects and posts only when published and indexable.
- Exclude dashboard/private routes and noindex content.

## Robots.txt Behavior

- Allow public pages.
- Disallow dashboard, private API paths, and internal preview paths.
- Reference the XML sitemap only in production indexing mode.

## Redirect Behavior

- Changed slugs can store redirect URLs.
- Root `/` redirects to `/en` with HTTP 301 in the Angular SSR server.
- Old localized slug redirects and removed-content 410 behavior remain dependent on future redirect policy/editorial data. Current unknown, draft, future, and unpublished content returns 404.

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
