# API_CONTRACT.md

The public read API is implemented in Laravel under `/api/v1`. Public write endpoints, authentication, sitemap, and robots responses are deferred to later tasks.

## Global Rules

- Base path: `/api/v1`.
- Authentication: all BE-003 read endpoints are public and unauthenticated.
- Locale: pass `locale=en|ar` or `Accept-Language: en|ar`; invalid locales return HTTP 422. English is the fallback locale.
- Response shape: successful responses use top-level `data`; list responses include Laravel pagination `links` and `meta`, plus `meta.locale`.
- Cache headers: safe public read responses include `Cache-Control` directives for `public` and `max-age=60`.
- Errors: validation returns HTTP 422 with field errors; missing or unpublished resources return HTTP 404.
- Privacy: public resources do not expose contact messages, verification emails, admin notes, visitor identifiers, raw IPs, hash fields, media filesystem paths, MIME types, or file sizes.

## Implemented Read Endpoints

| Method | Path | Purpose | Parameters |
| --- | --- | --- | --- |
| GET | `/api/v1/site` | Public settings allowlist, navigation, services, skills, social links | `locale`, `page`, `per_page` |
| GET | `/api/v1/about` | Public about payload with experience, skills, technologies, social links | `locale`, `page`, `per_page` |
| GET | `/api/v1/projects` | Published project cards | `locale`, `page`, `per_page`, `search`, `category`, `technology`, `featured`, `sort` |
| GET | `/api/v1/projects/{slug}` | Published project case study detail | `locale`; `{slug}` may be localized with English fallback |
| GET | `/api/v1/project-categories` | Visible project categories used by published projects | `locale`, `page`, `per_page` |
| GET | `/api/v1/technologies` | Visible technologies used by published projects | `locale`, `page`, `per_page` |
| GET | `/api/v1/testimonials` | Approved testimonials only | `locale`, `page`, `per_page`, `featured`, `project` |
| GET | `/api/v1/posts` | Published blog post cards | `locale`, `page`, `per_page`, `search`, `category`, `tag`, `featured`, `sort` |
| GET | `/api/v1/posts/{slug}` | Published blog post detail | `locale`; `{slug}` may be localized with English fallback |
| GET | `/api/v1/blog-categories` | Visible blog categories used by published posts | `locale`, `page`, `per_page` |
| GET | `/api/v1/tags` | Tags used by published posts | `locale`, `page`, `per_page` |
| GET | `/api/v1/services` | Visible services | `locale`, `page`, `per_page` |
| GET | `/api/v1/social-links` | Visible social links | `locale`, `page`, `per_page` |

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

Project details additionally include localized `body`, ordered public media URLs with localized alt/caption, related published projects, and SEO metadata.

Published-only rules:

- `status` must be `published`.
- `published_at` must be null or in the past.
- Draft, archived, future, or unknown slugs return HTTP 404.

## Blog Responses

Blog list cards include localized `title`, `slug`, `excerpt`, category, tags, featured flag, reading time, cover image URL, and publication timestamp.

Blog details additionally include localized `body`, related published posts, and SEO metadata.

Published-only rules:

- `status` must be `published`.
- `published_at` must be null or in the past.
- Draft, archived, future, or unknown slugs return HTTP 404.

## Site And About Responses

`GET /api/v1/site` exposes only these site setting keys:

- `site.profile_headline`
- `site.public_email`
- `site.whatsapp_url`
- `site.default_seo`

`GET /api/v1/about` returns public profile data composed from visible experience, skills, technologies, and social links. Private contact messages and dashboard-only settings are not exposed.

## Deferred Endpoints

The following endpoints remain planned for later tasks and are not implemented in BE-003:

- `POST /api/v1/projects/{project}/views`
- `POST /api/v1/projects/{project}/likes`
- `DELETE /api/v1/projects/{project}/likes`
- `POST /api/v1/testimonials`
- `POST /api/v1/contact`
- `GET /api/v1/sitemap.xml`
- `GET /robots.txt`
