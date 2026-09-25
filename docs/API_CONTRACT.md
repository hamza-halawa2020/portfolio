# API_CONTRACT.md

The public API is implemented in Laravel under `/api/v1`. Public read endpoints, anonymous public write workflows, sitemap, and robots responses are available.

Implementation architecture:

- Controllers accept validated Form Requests, call one public API application service, and return API Resources.
- Application services live under `App\Services\PublicApi`.
- Eloquent query construction and filtering live under `App\Queries\PublicApi`.
- Typed filter/result data lives under `App\Data\PublicApi`.
- Public write workflows use Form Requests, DTOs, services, transactions, API Resources, privacy-conscious visitor identity resolution, and endpoint-specific rate limiting.

## Global Rules

- Base path: `/api/v1`.
- Authentication: implemented public endpoints are public and unauthenticated. Public writes use an encrypted HTTP-only first-party visitor cookie, validation, rate limiting, and spam controls.
- Locale: pass `locale=en|ar` or `Accept-Language: en|ar`; invalid locales return HTTP 422. English is the fallback locale.
- Response shape: successful responses use top-level `data`; list responses include Laravel pagination `links` and `meta`, plus `meta.locale`.
- Cache headers: safe public read responses include `Cache-Control` directives for `public` and `max-age=60`. Public write responses use `Cache-Control: no-store`.
- Errors: validation returns HTTP 422 with field errors; missing or unpublished resources return HTTP 404.
- Privacy: public resources do not expose contact messages, verification emails, admin notes, visitor identifiers, raw IPs, hash fields, media filesystem paths, MIME types, or file sizes.
- CORS: configured for explicit local Angular/browser origins with credentials enabled. Production wildcard origins are not allowed.

## Implemented Read Endpoints

| Method | Path | Purpose | Parameters |
| --- | --- | --- | --- |
| GET | `/api/v1/site` | Public settings allowlist, navigation, services, skills, social links | `locale`, `page`, `per_page` |
| GET | `/api/v1/about` | Public about payload with experience, skills, technologies, social links | `locale`, `page`, `per_page` |
| GET | `/api/v1/projects` | Published project cards | `locale`, `page`, `per_page`, `search`, `category`, `technology`, `featured`, `sort` |
| GET | `/api/v1/projects/{slug}` | Published project case study detail with SEO metadata and localized slug mappings | `locale`; `{slug}` may be localized with English fallback |
| GET | `/api/v1/project-categories` | Visible project categories used by published projects | `locale`, `page`, `per_page` |
| GET | `/api/v1/technologies` | Visible technologies used by published projects | `locale`, `page`, `per_page` |
| GET | `/api/v1/testimonials` | Approved testimonials only | `locale`, `page`, `per_page`, `featured`, `project` |
| GET | `/api/v1/posts` | Published blog post cards | `locale`, `page`, `per_page`, `search`, `category`, `tag`, `featured`, `sort` |
| GET | `/api/v1/posts/{slug}` | Published blog post detail with SEO metadata and localized slug mappings | `locale`; `{slug}` may be localized with English fallback |
| GET | `/api/v1/blog-categories` | Visible blog categories used by published posts | `locale`, `page`, `per_page` |
| GET | `/api/v1/tags` | Tags used by published posts | `locale`, `page`, `per_page` |
| GET | `/api/v1/services` | Visible services | `locale`, `page`, `per_page` |
| GET | `/api/v1/social-links` | Visible social links | `locale`, `page`, `per_page` |

## Implemented Write Endpoints

| Method | Path | Purpose | Request body | Response |
| --- | --- | --- | --- | --- |
| GET | `/api/v1/projects/{slug}/likes` | Return authoritative project like count and current visitor liked state | Optional `locale`; visitor identity comes only from the encrypted visitor cookie | `200` with `count` and `liked`; no-store |
| POST | `/api/v1/projects/{slug}/views` | Register a unique public project view | Optional `locale`; optional honeypot `website` must be absent | `201` when counted, `200` when already counted for the visitor/day |
| POST | `/api/v1/projects/{slug}/likes` | Add the current visitor's like | Optional `locale`; optional honeypot `website` must be absent | `201` when created, `200` when already liked |
| DELETE | `/api/v1/projects/{slug}/likes` | Remove the current visitor's like | Optional `locale`; optional honeypot `website` must be absent | `200` with authoritative like count |
| POST | `/api/v1/testimonials` | Submit a private testimonial for moderation | `name`, `content`, `contact_email`, `publication_consent`; optional `company`, `position`, `rating`, `project`, `locale`; honeypot `website` must be absent | `202` acknowledgement only |
| POST | `/api/v1/contact` | Submit a private contact message | `name`, `email`, `message`, `privacy_consent`; optional `phone`, `company`, `project_type`, `budget_range`, `locale`; honeypot `website` must be absent | `202` acknowledgement only |

Write workflow rules:

- `{slug}` resolves only published projects and accepts localized slugs with English fallback.
- Visitor identifiers are generated server-side only. Request bodies cannot provide visitor IDs.
- The visitor cookie is first-party, encrypted by the application service, HTTP-only, `SameSite=Lax`, and secure in production.
- Stored interaction data uses HMAC hashes for visitor ID, IP, and user agent. Raw IP addresses and user agents are not stored.
- Project views are unique by project, visitor hash, and UTC calendar date, matching the existing `project_views` schema unique constraint.
- Project likes are idempotent and unique by project and visitor hash.
- Testimonial submissions are pending by default, never expose `contact_email`, and reject public moderation fields such as `status`, `is_featured`, and review/admin fields.
- Dashboard moderation can approve, reject, archive, and feature testimonials. Only approved testimonials are returned by `GET /api/v1/testimonials`; pending, rejected, and archived testimonials remain private.
- Contact submissions are private dashboard records, never returned by public APIs, and reject `status`, `admin_notes`, and `attachment` until safe private attachment storage is configured.
- Duplicate testimonial/contact submissions receive generic validation feedback to avoid exposing moderation internals.
- `TestimonialSubmitted` and `ContactMessageSubmitted` events dispatch after database commit for future queued notification listeners.

## Parameter Rules

- `page`: integer, minimum `1`.
- `per_page`: integer, minimum `1`, maximum `24`.
- `locale`: one of `en`, `ar`.
- `featured`: boolean-compatible query value.
- `search`: string with at least 2 characters and at most 100 characters.
- Project `sort`: `ordered`, `latest`, `oldest`, `featured`.
- Blog post `sort`: `latest`, `oldest`, `featured`.
- Project `category`, testimonial `project`, blog `category`, and blog `tag` filters resolve localized slugs.
- Project `technology` filter resolves the stable technology slug.

## Project Responses

Project list cards include localized `title`, `slug`, `summary`, `cover_image_url`, category, technologies, featured flag, publication timestamp, and aggregate `view_count` and `like_count`.

Project details additionally include localized `body`, `localized_slugs.en`, `localized_slugs.ar`, ordered public media URLs with localized alt/caption, related published projects, and SEO metadata.

Published-only rules:

- `status` must be `published`.
- `published_at` must be null or in the past.
- Draft, archived, future, or unknown slugs return HTTP 404.

## Blog Responses

Blog list cards include localized `title`, `slug`, `excerpt`, category, tags, featured flag, reading time, cover image URL, and publication timestamp.

Blog details additionally include localized `body`, `localized_slugs.en`, `localized_slugs.ar`, related published posts, and SEO metadata.

Published-only rules:

- `status` must be `published`.
- `published_at` must be null or in the past.
- Draft, archived, future, or unknown slugs return HTTP 404.

## Site And About Responses

`GET /api/v1/site` exposes only these site setting keys:

- `site.profile_headline`
- `site.public_email`
- `site.whatsapp_url`
- `site.whatsapp_message`
- `site.default_seo`

`GET /api/v1/about` returns public profile data composed from visible experience, skills, technologies, and social links. Private contact messages and dashboard-only settings are not exposed.

## SEO Endpoints

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/sitemap.xml` | XML sitemap for localized static routes plus published, indexable projects and posts. Excludes drafts, future content, noindex records, admin routes, API routes, errors, and write endpoints. |
| GET | `/robots.txt` | Environment-aware robots response. Local/testing/staging block indexing; production allows public pages and references the production sitemap only when explicitly enabled. |

SEO environment variables:

- `PUBLIC_SITE_URL`: absolute public production origin used by sitemap and robots.
- `PUBLIC_INDEXING_ENABLED`: must be `true` alongside `APP_ENV=production` before robots allows indexing.

## Deferred Endpoints

The following endpoints remain planned for later tasks:

- None for SEO-001.
