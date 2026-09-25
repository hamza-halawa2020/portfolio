# UI_PAGES.md

## Global UI Requirements

- Monochrome public design using black, white, and neutral gray only.
- Responsive layouts for desktop, tablet, and mobile.
- Arabic pages use RTL; English pages use LTR.
- Light, dark, and system theme controls are available globally.
- Language switcher is available globally.
- Loading, empty, error, and success states are required for data-driven pages.
- SEO metadata, localized URLs, accessibility, and performance verification are part of page completion.

## Public Pages

FE-001 shell status:

- A reusable SSR-rendered Angular app shell exists with skip-to-content, responsive header, accessible mobile navigation, language controls, light/dark/system theme controls, main outlet, and footer.
- English and Arabic public routes exist for all public page types listed below. Arabic routes render RTL; English routes render LTR.
- Current page bodies are internal FE-001 placeholders only and are intentionally noindexed. Full page content, API data, loading/empty/error/success states, forms, interactions, and media remain in later tasks.
- The language switch preserves static page locations. Dynamic project/blog detail switches intentionally return to the target language listing page until localized slug correspondence is available from API data.
- No demo URLs, dashboard demo links, demo credentials, or private client links are present in the frontend shell.

FE-002 localization and theme status:

- Shared shell labels, navigation labels, accessibility labels, theme labels, placeholder page text, 404 text, and common UI state messages are translated in English and Arabic.
- Missing localized dynamic values use deterministic English fallback. The frontend does not use browser-only locale detection that would alter server-rendered content unexpectedly after hydration.
- The language switch preserves static public routes and uses a typed localized slug mapping strategy for future API-backed project/blog detail routes. Until corresponding localized slugs are available, detail pages switch to the target language listing page.
- Light, dark, and system theme modes persist explicit visitor preference safely, update `data-bs-theme`, update `theme-color`, and respond to system preference changes.
- Public colors remain centralized monochrome tokens only. Focus, hover, active, and disabled states use black, white, and neutral gray values.

PAGE-001 public page status:

- Home, projects, project detail, about, services, blog, blog detail, contact, privacy, and 404 routes now render read-only API-backed page content where API data exists.
- Each data-driven page has loading, empty, error, and success states. Error states do not invent fallback content.
- Project and blog detail pages are SSR-rendered from the localized route slug. Language switching does not assume English and Arabic slugs match; it falls back to the target listing until localized slug mappings are available from the API.
- Media returned by the public API renders with explicit dimensions, lazy loading for list media, and localized alt text from the content title.
- Contact was read-only in PAGE-001. INT-001 implements contact submission, testimonial submission, project views/likes, and WhatsApp contact action, pending stable browser E2E verification. Advanced filters/search and rich sharing controls remain deferred.

SEO-001 status:

- Sitemap, robots, Open Graph, Twitter Card, static/dynamic `hreflang`, `x-default`, and JSON-LD are implemented.
- Dynamic project and blog detail pages use API-provided localized slug mappings for alternates and language switching.
- Contact submission, testimonials submission, project views/likes, and WhatsApp contact action are implemented in INT-001 pending stable browser E2E verification. Advanced filters/search and rich sharing controls remain deferred.

### Home

- Sections: header, developer intro, professional title, value proposition, project CTA, contact CTA, selected projects, services, skills/technologies, statistics, testimonials, latest blog posts, contact CTA, footer.
- APIs: `GET /api/v1/site`, `GET /api/v1/about`, `GET /api/v1/projects`, `GET /api/v1/posts`, `GET /api/v1/services`, `GET /api/v1/testimonials`.
- SEO: `WebSite` and `Person` structured data.

### Projects

- Sections: project grid, featured/public metadata, category/technology display, pagination payload support.
- Deferred: search UI and category/technology filter controls.
- States: loading, empty search, API error, pagination loading.
- APIs: `GET /api/v1/projects`.

### Project Details

- Sections: case study header, category, role, duration, technologies, challenge, solution, features, development challenges, results, metrics, screenshots/media, captions, related projects, contact CTA.
- INT-001 status: likes, unlikes, visitor like-state loading, and browser-only view registration are implemented and pending stable browser E2E verification.
- Deferred: sharing controls and richer media carousel/player behavior.
- Exclusions: no demo URLs, dashboard URLs, demo credentials, or private client links.
- APIs: `GET /api/v1/projects/{slug}`, view and like endpoints.
- SEO: localized title/description, canonical, `hreflang`, Open Graph, BreadcrumbList.

### About and Resume

- Sections: biography, timeline, skills, technologies, certifications, education, CV download, availability.
- APIs: `GET /api/v1/about`.

### Services

- Sections: manageable service list with bilingual titles and descriptions.
- APIs: `GET /api/v1/services`.

### Blog

- Sections: article list, categories/tags display, featured metadata, pagination payload support, reading time.
- Deferred: search UI and category/tag filter controls.
- APIs: `GET /api/v1/posts`.

### Blog Details

- Sections: title, cover image, article body, category, tags, date, reading time, related posts.
- Deferred: table of contents and share buttons.
- APIs: `GET /api/v1/posts/{slug}`.
- SEO: Article structured data.

### Contact

- INT-001 status: public channels from `GET /api/v1/site`, WhatsApp action, contact form submission, testimonial submission, validation, submitting, success, rate-limit/failure states, and honeypot fields are implemented and pending stable browser E2E verification.
- Attachments remain deferred until private storage and upload security are configured.

### Privacy

- Content: analytics collection, hashed IP usage, cookies/visitor identifiers, contact form data, retention, testimonial data, user rights, project view/like limitations.
- APIs: may use `GET /api/v1/site` for settings.

### 404

- Requirements: real SSR-rendered 404 page with HTTP 404 status.

## Dashboard Sections

- Projects and case studies.
- Project media.
- Blog posts, categories, and tags.
- Services, skills, technologies, and experience.
- Testimonials moderation.
- Contact messages.
- Project likes and views.
- Analytics.
- SEO metadata.
- Site settings and social links.

