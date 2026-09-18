# API_CONTRACT.md

This is the initial API proposal. It must be synchronized with Laravel routes, Form Requests, Resources, and tests.

## Global Rules

- Base path: `/api/v1`
- Authentication: public endpoints are unauthenticated unless explicitly stated.
- Locale: use localized URLs and/or `Accept-Language: ar|en`.
- Responses: public responses must not expose private fields, visitor identifiers, raw hashes, admin notes, or testimonial verification emails.
- Errors: validation uses HTTP 422 with field errors; rate limits use HTTP 429; missing resources use HTTP 404.
- Pagination: list endpoints use Laravel-style pagination metadata.
- Rate limits: public write endpoints must be rate-limited.

## GET /api/v1/site

- Purpose: fetch site settings, navigation, social links, featured homepage content, and global SEO defaults.
- Auth: none.
- Parameters: optional `locale`.
- Response: settings and localized content blocks.
- Errors: 400 for invalid locale.

## GET /api/v1/projects

- Purpose: list published projects.
- Auth: none.
- Parameters: `page`, `per_page`, `search`, `category`, `technology`, `featured`, `locale`.
- Validation: pagination limits and known filters.
- Response: paginated project cards with title, slug, summary, cover image, category, technologies, view count, like count, and featured flag.
- Errors: 422 for invalid filters.

## GET /api/v1/projects/{slug}

- Purpose: project case study details.
- Auth: none.
- Parameters: localized `slug`.
- Response: full localized case study, media, related projects, SEO metadata, like count, and view count.
- Errors: 404 when unpublished or missing.

## POST /api/v1/projects/{project}/views

- Purpose: record a unique view for a project.
- Auth: none.
- Validation: valid project identifier; visitor cookie/header handled privately.
- Response: authoritative view count.
- Errors: 404, 429.
- Privacy: no raw IP storage and no visitor identifiers returned.

## POST /api/v1/projects/{project}/likes

- Purpose: like a project.
- Auth: none.
- Validation: valid project identifier and visitor strategy.
- Response: authoritative like count and `liked: true`.
- Errors: 404, 429.

## DELETE /api/v1/projects/{project}/likes

- Purpose: remove a project like.
- Auth: none.
- Response: authoritative like count and `liked: false`.
- Errors: 404, 429.

## GET /api/v1/testimonials

- Purpose: list approved testimonials.
- Auth: none.
- Parameters: `page`, `per_page`, `featured`, `project`.
- Response: approved testimonial cards only.
- Privacy: contact email is never included.

## POST /api/v1/testimonials

- Purpose: submit a testimonial for moderation.
- Auth: none.
- Validation: name, content, rating, optional company/position/project, verification email, consent, optional safe image/logo.
- Response: success message and pending status.
- Errors: 422, 429.

## GET /api/v1/posts

- Purpose: list published blog posts.
- Auth: none.
- Parameters: `page`, `per_page`, `search`, `category`, `tag`, `featured`, `locale`.
- Response: paginated post cards with localized metadata and reading time.

## GET /api/v1/posts/{slug}

- Purpose: fetch a blog article.
- Auth: none.
- Parameters: localized `slug`.
- Response: localized article, category, tags, related posts, SEO metadata, and structured-data fields.
- Errors: 404.

## GET /api/v1/services

- Purpose: list active services.
- Auth: none.
- Parameters: optional `locale`.
- Response: ordered localized services.

## GET /api/v1/about

- Purpose: fetch biography, experience, skills, technologies, certifications, education, CV link, and availability.
- Auth: none.
- Parameters: optional `locale`.
- Response: localized profile data.

## POST /api/v1/contact

- Purpose: submit a private contact message.
- Auth: none.
- Validation: name, email, phone, company, project type, budget range, message, optional attachment, privacy consent, spam protection token.
- Response: success message and reference ID safe for user display.
- Errors: 422, 429.
- Side effects: store message, queue owner notification, store validated attachment.

## GET /api/v1/sitemap.xml

- Purpose: generated XML sitemap for indexable localized public URLs.
- Auth: none.
- Response: XML.

## GET /robots.txt

- Purpose: robots policy for public and private paths.
- Auth: none.
- Response: text disallowing dashboard/private paths and referencing sitemap.

