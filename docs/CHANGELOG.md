# CHANGELOG.md

## Unreleased

### Added

- Initial project documentation set.
- Initial architecture, database, API, UI, SEO, testing, and deployment proposals.
- Initial task backlog and working rules.
- Laravel Herd runtime verification for PHP 8.5, Composer, Node.js 24, npm, and Angular CLI 22.
- Repository foundation files: `.editorconfig`, `.gitignore`, `.nvmrc`, `README.md`, `frontend/.gitkeep`, and `backend/.gitkeep`.
- Laravel 13 backend foundation in `backend/` with PHP `^8.5`, safe `.env.example`, UTC timezone, English default locale, documented English/Arabic supported locales, and passing default tests.
- Angular 22 frontend foundation in `frontend/` with routing, strict TypeScript, SSR, hydration, Vitest unit tests, Playwright configuration, npm lockfile, and a minimal semantic shell.
- Safe local backend environment placeholders for Herd backend URL, Angular origins, and CORS allowed origins.
- Deferred infrastructure tasks for Redis/Valkey, local mail inbox or SMTP, and MySQL 8.4 staging/production compatibility verification.
- Portfolio business database schema migration covering projects, media, testimonials, blog, services, experience, skills, contact messages, site settings, social links, SEO metadata, and privacy-conscious analytics.
- Backend schema tests for table creation, translation columns, hash-based visitor privacy fields, and key unique constraints.
- Eloquent models, relationships, casts, scopes, enums, factories, policies, and a production-guarded fictional development seeder for the portfolio business schema.
- Backend model-layer tests covering localization, relationships, enum casts, sensitive serialization, dashboard policy behavior, and seeder idempotency.
- Versioned public read API under `/api/v1` for site, about, projects, project categories, technologies, testimonials, blog posts, blog categories, tags, services, and social links.
- Public API Form Requests, Resources, localized response handling, pagination, cache headers, published-only filtering, and privacy-safe payloads.
- Backend public API feature tests covering localization, validation, filters, slug lookup, 404 behavior, privacy exclusions, pagination metadata, cache headers, and a basic query-count guard.
- Public API service-layer architecture with thin controllers, typed filter/data objects, application services, query services, typed not-found exceptions, and architecture tests.
- Public write workflows for project views, project likes/unlikes, testimonial submissions, and contact submissions.
- Privacy-conscious visitor identity middleware and service using an encrypted first-party HTTP-only cookie with HMAC-only interaction storage.
- Named public write rate limiters, honeypot fields, consent validation, after-commit testimonial/contact submission events, and no-store write responses.
- Backend write workflow and architecture tests covering idempotency, privacy exclusions, validation, rate limiting, transactions, direct service use without HTTP, and query-free Resources.
- Filament `5.8.2` owner dashboard at `/admin` with login, logout, disabled registration, light/dark mode, neutral monochrome styling, and English/Arabic LTR/RTL rendering.
- Explicit dashboard administrator authorization through `users.is_admin`, admin grant metadata, strict policies, and an interactive `portfolio:provision-owner-admin` command with hidden password input.
- Dashboard noindex/private-cache middleware and focused admin authentication/authorization tests.
- Filament content resources for projects, project categories, technologies, project media, blog posts, blog categories, tags, services, skills, experience, site settings, social links, and parent-owned SEO metadata.
- Admin content service layer for bilingual translation preservation, localized slug normalization, SEO persistence, rich-text sanitization, media lifecycle handling, reading-time calculation, and focused create/update/delete workflows.
- MySQL generated-column unique indexes for English and Arabic localized slugs on routed content tables.
- Configurable portfolio media disk, upload MIME allowlists, and image/video size limits.
- Admin content feature tests covering policy-protected resources, translation preservation, sanitization, slug validation, SEO persistence, reading-time calculation, and media file replacement cleanup.
- Testimonial moderation dashboard resource with approve, reject, archive, and feature actions.
- Private contact inbox dashboard resource with status and admin-note workflows.
- Dashboard analytics widgets for visits, unique visitors, project views, project likes, contact submissions, pending testimonials, daily trends, top projects, and device breakdowns.
- Admin analytics query service and moderation/inbox services to keep Filament widgets/resources thin.
- Comprehensive local/testing development seeder with bilingual fictional content, analytics fixtures, safe media fixtures, and optional local administrator provisioning from ignored environment variables.
- Angular public frontend shell with SSR-rendered localized routes, responsive header/navigation, footer, skip link, language controls, light/dark/system theme controls, and internal noindexed placeholders for documented public pages.
- Frontend locale, theme, shell navigation, public-site config, and SEO services with SSR-safe browser API access and raw-HTML title, description, canonical, and static-page `hreflang` metadata.
- Playwright desktop/mobile smoke coverage for public shell navigation, theme switching, language switching, keyboard skip-link access, mobile menu behavior, and 404 behavior.
- Production-ready frontend localization/theme foundation with typed Arabic/English copy, deterministic English fallback for missing dynamic translations, translated shared UI states, typed localized slug switching strategy for future API-backed detail routes, system theme change handling, and browser `theme-color` metadata updates.
- API-backed Angular public pages for home, projects, project detail, about, services, blog, blog detail, contact, privacy, and localized 404 routes.
- Typed frontend public API models/service and a public page facade for read-only `/api/v1` content loading, localized state mapping, and loading/empty/error/success behavior.
- Frontend unit coverage for public API query serialization and public page facade states.
- Laravel-generated `/sitemap.xml` and environment-aware `/robots.txt` for localized public SEO.
- API-provided `localized_slugs` mappings on project and blog detail responses.
- SSR Open Graph, Twitter Card, `x-default` alternates, dynamic localized `hreflang`, and safely escaped JSON-LD metadata.
- Playwright coverage for SEO metadata, JSON-LD duplication prevention, and API-provided dynamic language switching.
- Isolated INT-001 Playwright acceptance harness covering project views, likes/unlikes, contact submissions, testimonial submissions, WhatsApp action behavior, credentialed CORS, and encrypted visitor-cookie behavior against a temp SQLite Laravel backend and built Angular SSR frontend.
- Scheduled analytics aggregation and cleanup jobs with count-only daily summaries, configurable raw event retention, and scheduler tests.
- Secret-free GitHub Actions quality gates for backend and frontend verification.
- Backend Composer quality scripts and frontend npm quality scripts for local CI dry runs.
- Frontend Prettier ignore rules and formatted frontend source files.
- QA-002 accessibility, performance, security, and deployment review documentation.
- Isolated QA-002 Playwright review coverage for bilingual accessibility landmarks, form controls, private-data leakage checks, visitor-cookie privacy, and local navigation timing inputs.
- Backend security/deployment review tests for safe environment placeholders, credentialed CORS, public write response privacy, upload rejection, raw-IP avoidance, and MIME allowlists.

### Changed

- Local development strategy now uses Laravel Herd on Windows and explicitly excludes Docker.
- Backend documentation now uses Herd PHP and Herd Composer commands.
- Frontend documentation now uses Herd-managed Node.js 24, npm 11.19.0, and project-local Angular CLI 22 commands.
- Replaced the obsolete FND-005 Docker scope with Laravel Herd local service and environment verification.
- FND-005 now uses approved local development fallbacks: MySQL `8.0.41`, database cache/session/queue drivers, and Laravel `log` mailer.
- Database schema documentation now reflects the implemented BE-001 migration and ADM-002 localized slug generated-column indexes for routed content tables.
- Architecture and decisions now document the explicit model localization helper, administrator-only dashboard policy, and fictional development seed strategy.
- API contract now reflects implemented BE-003 read endpoints; SEO-001 later added sitemap and robots endpoints.
- BE-003 public API controllers were refactored to remove Eloquent query construction, filtering, visibility rules, relationship loading, and business orchestration.
- API contract now reflects implemented BE-004 write endpoints; sitemap/robots are implemented by SEO-001.
- Contact attachments are explicitly rejected by public writes until private storage and upload security are configured.
- Dashboard model policies now require explicit administrator access instead of any authenticated user.
- Localized JSON slug uniqueness is now protected by admin validation plus MySQL generated-column unique indexes for routed content tables.
- Development seeding now reuses existing local technology records by name to preserve earlier local data and avoid duplicate unique names.
- Playwright configuration can target an externally started SSR server through `PLAYWRIGHT_BASE_URL`, while still supporting the local Angular dev server by default.
- Frontend Playwright smoke coverage now checks both directions, light/dark/system themes, theme persistence after reload, dynamic detail slug fallback, mobile navigation, keyboard access, and unexpected browser console/page errors.
- Public page robots metadata now uses `index, follow` for completed API-backed pages while preserving non-indexable behavior for 404/error-style routes.
- Frontend Playwright coverage now exercises API-backed public content and uses a real published project slug for dynamic detail language-switch fallback checks.
- Angular SSR root now redirects `/` to `/en` with HTTP 301.
- Dynamic project/blog language switching now uses API-provided localized slug mappings when available.
- Angular SSR now serves `/portfolio-public-config.js` so browser hydration can use the same runtime API base URL as SSR in isolated and deployed environments.
- Dashboard daily trend metrics now prefer complete daily analytics summaries when available and fall back to live tables when summaries are missing.
- `.env.example` no longer carries a generated Laravel `APP_KEY`; CI generates a temporary key during verification.
- `.env.example` now uses secret-free local administrator placeholders and SQLite as the safe default database connection.
- Deployment documentation now includes production HTTP security, backup, restore, and post-restore verification checklists.
- Task bookkeeping was repaired so deferred Redis/Valkey and Mailpit/SMTP tasks are not marked completed without evidence.

### Fixed

- Hardened frontend locale and theme services so missing or unavailable browser storage does not crash SSR, tests, or constrained browser-like runtimes.
- Fixed Arabic frontend placeholder copy and replaced mojibake text with valid UTF-8 Arabic strings.
- Normalized frontend boolean API query parameters to `1`/`0` for Laravel validation compatibility across SSR and browser fetch.
- Fixed robots indexing flag parsing so `PUBLIC_INDEXING_ENABLED=false` is treated as false.
- Removed static Laravel `public/robots.txt` so environment-aware robots routing is not shadowed.
- Fixed Laravel middleware bootstrap so the default `api` middleware group is registered and CORS middleware is explicitly present for credentialed public API requests.
- Fixed Windows E2E instability by running Laravel `artisan serve --no-reload`, ensuring process-scoped temp DB, CORS, visitor-cookie, and site-setting env values reach the served PHP child.

### Security

- Documented no raw IP storage, no committed secrets, safe upload validation, and private dashboard requirements.
- Public interaction tables store visitor, IP, and user-agent HMAC hashes only; raw IP addresses are not stored.
- Public testimonial/contact endpoints reject dashboard-only fields and avoid returning private records or submitter contact data.
- Filament dashboard access requires an explicitly authorized admin user; public visitor cookies cannot authenticate dashboard access.
- Dashboard responses send `X-Robots-Tag: noindex, nofollow, noarchive` and private no-store cache headers.
- Admin rich text is sanitized server-side before persistence with an explicit HTML allowlist.
- Project and cover media uploads reject unsafe MIME types, use server-generated filenames, and clean up replaced files only when they are no longer referenced.
- Local administrator provisioning reads credentials from ignored `.env`, hashes the password, refuses production, avoids password resets on repeat seed runs, and refuses to elevate existing non-admin accounts automatically.
- Public APIs continue to hide testimonial verification emails, contact messages, admin notes, raw identifiers, and raw IP data.
- INT-001 browser verification confirms the visitor cookie is HTTP-only, unavailable to JavaScript, accepted cross-origin with explicit credentialed CORS, and not used as localStorage-derived like state.
- Analytics daily summaries store aggregate counts only, and scheduled cleanup prunes raw analytics events after the configured retention period while preserving summaries.
- Baseline CI runs without production secrets, external databases, Redis, SMTP, S3 credentials, or public demo credentials.
- QA-002 confirms public write responses avoid exposing visitor/IP/user-agent hashes, public contact attachments are rejected, media MIME allowlists exclude unsafe types, dependency audits report no advisories, and committed example environment values use safe placeholders only.

### Removed

- None.
