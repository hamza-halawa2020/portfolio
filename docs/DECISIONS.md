# DECISIONS.md

## DEC-001 - Monorepo structure

- Date: 2026-09-18
- Context: The repository currently contains only `project.md`.
- Options considered: initialize separate repositories; initialize requested monorepo.
- Selected option: use the requested monorepo with `frontend/`, `backend/`, and `docs/`.
- Reason: The brief requires two connected applications with shared documentation. Docker was later superseded by DEC-007 after the user selected Laravel Herd.
- Consequences: Future tasks must create the apps non-destructively and keep docs synchronized. Do not add Docker configuration unless a later explicit task reverses DEC-007.

## DEC-002 - Initial translation storage

- Date: 2026-09-18
- Context: Dynamic content must support Arabic and English.
- Options considered: JSON translation columns; translation tables.
- Selected option: JSON translation columns for the initial implementation.
- Reason: The expected portfolio content volume is moderate, and JSON columns keep the editorial model simpler. The project can migrate to translation tables later if localized search/indexing becomes a bottleneck.
- Consequences: Queries on localized content require careful indexing/search strategy and documentation.

## DEC-003 - Runtime compatibility gate before installation

- Date: 2026-09-18
- Context: The brief mandates PHP 8.5, Laravel 13, Angular 22, and stable compatible dependencies, but PHP and Composer are unavailable in the current environment.
- Options considered: install latest available packages immediately; add a compatibility verification task first.
- Selected option: block framework installation behind a compatibility verification task.
- Reason: The brief forbids prerelease or unsupported dependencies and requires official compatibility confirmation.
- Consequences: The next implementation task should verify availability and compatibility before generating Laravel or Angular apps.

## DEC-004 - Local development runtime strategy

- Date: 2026-09-18
- Context: The user uses Laravel Herd on Windows. Herd works in the user's regular command prompt but is not visible to the Codex sandbox PATH. Docker must not be used. Angular 22 does not support the installed Node.js `v22.13.0`.
- Options considered: use Docker; install PHP/Composer separately; use Laravel Herd for PHP/Composer; force installs with unsupported local runtimes.
- Selected option: use Laravel Herd as the local PHP and Composer environment, with PHP 8.5 selected through Herd and Composer invoked via `herd composer`.
- Reason: This matches the user's environment and avoids Docker, separate PHP/Composer installation, and PATH modifications.
- Consequences: `FND-002` may proceed only after Herd reports PHP 8.5 and Node.js 24 LTS is active. Direct `php` and `composer` commands should not be required.

Update on 2026-09-18: Herd PHP `8.5.10`, Herd Composer `2.10.2`, and Herd-managed Node.js `24.21.0` were verified. `FND-002` may proceed using Herd commands and project-local Angular CLI 22.

## DEC-007 - Docker excluded from local development

- Date: 2026-09-18
- Context: The user explicitly selected Laravel Herd on Windows and instructed not to use Docker.
- Options considered: keep Docker as the local development target; switch local development documentation to Herd.
- Selected option: use Herd for local PHP/Composer and do not create Docker configuration unless a future task explicitly changes this.
- Reason: Herd is installed, supports PHP 8.5, and matches the user's active development setup.
- Consequences: Deployment documentation may still describe production services conceptually, but local setup and foundation tasks must not rely on Docker.

Update on 2026-09-18: FND-005 replaced the obsolete Docker development-services scope with a Herd-only local services scope.

## DEC-005 - Angular CLI execution strategy

- Date: 2026-09-18
- Context: The global Angular CLI is `19.0.7`, while the project requires Angular 22.
- Options considered: upgrade the global CLI; use the global CLI anyway; use a project-local Angular CLI via `npx` or local npm binary.
- Selected option: use a project-local Angular CLI 22 command, preferably `npx -p @angular/cli@22.1.7 ng ...`, during frontend initialization.
- Reason: This avoids dependence on the global CLI and keeps Angular CLI aligned with the framework major version.
- Consequences: Frontend commands must be run with Node.js 24.21.0 LTS or another Angular-supported Node version.

## DEC-006 - MySQL 8 line selection

- Date: 2026-09-18
- Context: The requirement says MySQL 8, but official MySQL documentation says MySQL 8.0 reached EOL in April 2026 and recommends MySQL 8.4 LTS or a current Innovation release.
- Options considered: MySQL 8.0; MySQL 8.4 LTS; MySQL 9 Innovation.
- Selected option: MySQL 8.4 LTS.
- Reason: It remains within the requested MySQL 8 family while using the supported production LTS line.
- Consequences: Herd/local database setup should target MySQL 8.4 unless later hosting constraints require another supported MySQL 8 line.

Update on 2026-09-18: FND-005 accepts the currently running local MySQL `8.0.41` instance for initial development. Staging and production still target MySQL `8.4 LTS`; migrations and SQL must remain compatible with both, and MySQL 8.4-only features must not be used without documentation.

## DEC-010 - Local infrastructure fallbacks for FND-005

- Date: 2026-09-18
- Context: Redis/Valkey, Mailpit/local SMTP, and local MySQL 8.4 are not required by the currently implemented foundation features.
- Options considered: block FND-005 until all services are installed; complete FND-005 with safe local fallbacks and track future infrastructure tasks.
- Selected option: complete FND-005 with database-backed Laravel cache/session/queue drivers, Laravel `log` mailer, and local MySQL `8.0.41`.
- Reason: This keeps initial development moving without installing system services, changing PATH, resetting databases, or pretending production infrastructure is complete.
- Consequences: Redis-dependent features, Horizon if selected, distributed locks, production queue configuration, Mailpit/local SMTP, real SMTP delivery, and MySQL 8.4 staging/production verification remain separate pending tasks.

## DEC-011 - Portfolio schema translation and slug indexing

- Date: 2026-09-18
- Context: Dynamic content must support Arabic and English, and local development currently verifies migrations against SQLite and MySQL 8.0.41 while staging/production targets MySQL 8.4 LTS.
- Options considered: JSON translation columns only; separate translation tables; database-specific generated columns for localized slug uniqueness.
- Selected option: implement JSON translation columns for BE-001 and enforce localized JSON slug uniqueness later through application validation unless a future task approves database-specific generated columns.
- Reason: JSON columns match the documented initial translation strategy and remain portable across the current test database and MySQL target.
- Consequences: Business migrations avoid MySQL 8.4-only features. Future API/admin tasks must validate localized slugs before writes, and any generated-column index strategy must document MySQL compatibility.

Update on 2026-09-18: BE-002 added a lightweight `HasLocalizedAttributes` model trait. Models do not resolve locale from the HTTP request or global app state; callers pass the requested locale explicitly and may fall back to English.

Update on 2026-09-18: ADM-002 approved the smallest MySQL-specific slug index strategy needed for concurrent writes. English and Arabic generated stored columns now index JSON slug paths on routed content tables. SQLite test databases skip the generated-column SQL while admin services still validate duplicates before persistence.

## DEC-012 - Initial dashboard model authorization

- Date: 2026-09-18
- Context: BE-002 needs policies for dashboard-managed models before Filament, roles, and permissions are installed.
- Options considered: no policies until Filament; one reusable authenticated-user dashboard policy; per-model role policies before roles exist.
- Selected option: register one reusable dashboard policy for current business models that allows authenticated users and denies guests.
- Reason: This protects dashboard-managed resources at the model-policy layer without inventing roles before the authentication and permissions milestone.
- Consequences: ADM-001 or a later permissions task must replace or refine this broad policy when Filament authentication and role/permission rules are implemented.

Update on 2026-09-18: ADM-001 replaced this broad policy with explicit `users.is_admin` authorization.

## DEC-013 - Development seed data

- Date: 2026-09-18
- Context: BE-002 requires useful seeders without real client data or destructive behavior.
- Options considered: no business seeders; random-only factories; a small idempotent fictional development seeder.
- Selected option: add a production-guarded `DevelopmentPortfolioSeeder` with fictional bilingual records and predictable lookup keys.
- Reason: This supports local dashboard/API development while keeping data safe and repeatable.
- Consequences: Production data import remains out of scope. The development seeder must not truncate tables, create credentials, or seed real client information.

## DEC-014 - Public read API response and localization strategy

- Date: 2026-09-18
- Context: BE-003 required public `/api/v1` read endpoints for bilingual Angular consumption without exposing private dashboard or analytics fields.
- Options considered: expose Eloquent models directly from controllers; keep query/filter logic in controllers; use dedicated public API Resources, Form Requests, application services, query services, and typed filter DTOs.
- Selected option: use thin controllers backed by `App\Services\PublicApi`, `App\Queries\PublicApi`, and `App\Data\PublicApi`, with locale from `locale=en|ar` or `Accept-Language` and English fallback for missing localized content.
- Reason: This keeps public responses stable, localized, privacy-filtered, suitable for SSR frontend consumption, and aligned with the permanent thin-controller rule.
- Consequences: Public read endpoints use a short 60-second public cache header. Detail endpoints currently resolve localized JSON slugs in query services at the application layer for portability; revisit database-specific generated-column indexes if content volume requires faster slug lookup.

## DEC-015 - Anonymous public write workflow strategy

- Date: 2026-09-18
- Context: BE-004 requires project views, likes, testimonial submissions, and contact submissions without exposing raw IP addresses, visitor identifiers, private emails, moderation fields, or dashboard-only data.
- Options considered: accept visitor IDs from request bodies; require Sanctum sessions for all writes; issue a first-party visitor cookie and store only HMAC hashes.
- Selected option: use a server-issued first-party encrypted HTTP-only visitor cookie, derive HMAC hashes with the configured visitor hash secret, and keep public writes anonymous and stateless.
- Reason: This supports public interactions from the Angular website without forcing accounts or dashboard authentication, while keeping identifiers out of public payloads and avoiding raw IP storage.
- Consequences: CORS must allow credentials only for explicit trusted origins. Public write rate limiters use privacy-safe visitor/IP hash keys. Project view uniqueness currently follows the schema's UTC date window, not a rolling 24-hour interval. Contact attachments are rejected until private storage and upload security are implemented.

Update on 2026-09-18: BE-004 implemented this strategy with thin controllers, Form Requests, DTOs, application services, API Resources, named rate limiters, encrypted visitor cookies, HMAC-only interaction storage, pending testimonials, private contact messages, and after-commit submission events.

## DEC-016 - Initial Filament owner dashboard authorization

- Date: 2026-09-18
- Context: ADM-001 requires Filament authentication, dashboard authorization, no public registration, and policy enforcement before content resources are created.
- Options considered: allow any authenticated user; create full roles/permissions immediately; use a minimal explicit administrator flag now.
- Selected option: use `users.is_admin` as the initial explicit administrator gate for Filament panel access and dashboard policies.
- Reason: This satisfies strict authorization without installing a broader permissions model before resource workflows exist.
- Consequences: Owner access is provisioned with `portfolio:provision-owner-admin`. Registration is disabled. Future Spatie Permission work may replace or augment this flag, but cannot loosen dashboard authorization accidentally.

## DEC-017 - Filament admin localization and indexing posture

- Date: 2026-09-18
- Context: The dashboard must support English/Arabic, LTR/RTL, light/dark mode, and must not be indexable.
- Options considered: rely only on app default locale; add explicit admin locale resolution; leave indexing to external robots configuration.
- Selected option: resolve admin locale from `?locale=`, session, or `Accept-Language`, and apply noindex/private-cache headers in the Filament middleware stack.
- Reason: This verifies locale direction inside the dashboard itself and protects private dashboard screens regardless of public robots configuration.
- Consequences: Dashboard routes send `X-Robots-Tag` and private no-store headers. Public SEO robots/sitemap work remains separate.

## DEC-018 - Admin content service layer

- Date: 2026-09-18
- Context: ADM-002 requires Filament content management without putting business workflows in resources or pages.
- Options considered: place save logic directly in Filament actions; create one generic admin content service; use focused services per workflow area.
- Selected option: keep Filament resources focused on forms, tables, filters, uploads, and action wiring, and delegate persistence to focused services under `App\Services\Admin\Content`.
- Reason: This follows the repository rule against business logic in dashboard resources and avoids a single broad service with mixed responsibilities.
- Consequences: New dashboard resources should use the same pattern. Cache invalidation must be added inside these services when stored public content caches are introduced.

## DEC-019 - Admin rich text and media safety

- Date: 2026-09-18
- Context: Administrators can submit rich bilingual content and upload project/cover media, but admin input must still be treated as untrusted.
- Options considered: trust Filament rich editor output; store raw HTML and sanitize in the frontend; sanitize server-side before persistence.
- Selected option: sanitize rich text server-side before persistence with an explicit allowlist, and validate media using configured MIME and size allowlists through Laravel filesystem storage.
- Reason: Server-side sanitization protects both current API consumers and future rendering paths. Filesystem abstraction supports local storage now and S3-compatible storage later.
- Consequences: SVG/HTML-style unsafe uploads remain rejected unless a future task adds explicit sanitization. Image transcoding and video conversion are not claimed or implemented.

## DEC-008 - Laravel backend foundation scope

- Date: 2026-09-18
- Context: FND-003 required a fresh Laravel 13 backend without dashboard, authentication packages, permissions packages, media packages, or business feature models.
- Options considered: install only Laravel skeleton; install Laravel plus Filament/Sanctum/permissions immediately.
- Selected option: initialize only the Laravel 13 skeleton and default development tooling.
- Reason: The task acceptance criteria only required the backend foundation, PHP `^8.5`, safe environment files, and passing default tests.
- Consequences: Filament, Sanctum, Spatie Permission, API resources, project models, and business migrations remain for later backend/dashboard tasks.

## DEC-009 - Angular frontend foundation scope

- Date: 2026-09-18
- Context: FND-004 required Angular 22, strict TypeScript, routing, SSR, hydration, unit testing, and Playwright configuration, while explicitly deferring public pages, API integration, Tailwind, Transloco, and final design.
- Options considered: initialize Angular only; initialize Angular plus full app shell/localization/theme; initialize Angular plus final homepage.
- Selected option: initialize only the Angular foundation with a minimal semantic shell, Vitest unit test, and Playwright configuration.
- Reason: This satisfies foundation verification without creating throwaway public-page content or final design decisions.
- Consequences: Tailwind, Transloco, theme handling, public routes, API integration, and E2E browser installation/runs remain for later frontend tasks.
