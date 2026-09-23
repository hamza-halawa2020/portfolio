# TASKS.md

## Summary

| Completed | Active | Pending | Blocked |
| ---: | ---: | ---: | ---: |
| 19 | 0 | 8 | 0 |

## Phase 0 - Discovery and Documentation

### P0-001 - Create initial project documentation

- Status: [x] Completed
- Dependencies: None
- Files: `AGENTS.md`, `docs/*.md`
- Acceptance criteria:
  - Required Markdown documentation files exist.
  - Current repository structure is recorded.
  - Installed runtime/tool versions are recorded where available.
  - Initial architecture proposal is documented.
  - Initial database schema proposal is documented.
  - Initial API contract proposal is documented.
  - Initial UI page inventory is documented.
  - SEO strategy is documented in `docs/SEO.md`.
  - Detailed implementation tasks are listed in `docs/TASKS.md`.
- Tests:
  - Verify all required Markdown files exist.
  - Verify no Angular or Laravel app is claimed to exist unless present.
  - Run available version commands and record results.
- Notes:
  - Repository currently contains only `project.md`; no `.git`, `frontend/`, or `backend/` directories were found.
  - PHP and Composer are not available on PATH in this environment.
  - Node `v22.13.0`, npm `11.3.0`, and global Angular CLI `19.0.7` were verified.
  - npm and Angular CLI version commands required `cmd /c` and elevated sandbox access to read the user npm installation path.
- Completed: 2026-09-18

## Phase 1 - Project Foundations

### FND-001 - Verify runtime and dependency compatibility

- Status: [x] Completed
- Dependencies: P0-001
- Files: `docs/ARCHITECTURE.md`, `docs/DECISIONS.md`, `docs/DEPLOYMENT.md`, `.env.example`, `backend/composer.json`, `frontend/package.json`, Docker and CI files
- Acceptance criteria:
  - PHP 8.5 stable availability is verified.
  - Laravel 13 stable availability is verified.
  - Angular 22 stable availability is verified.
  - Filament compatibility with Laravel 13 and PHP 8.5 is verified.
  - Node.js, TypeScript, RxJS, bootstarp, Composer, and npm compatibility is documented.
  - Unsupported, prerelease, or incompatible dependencies block implementation instead of being forced.
- Tests:
  - `php --version`
  - `composer --version`
  - `php artisan --version`
  - `node --version`
  - `cmd /c npm --version`
  - `ng version`
- Notes:
  - Verified stack is compatible when using PHP 8.5.10, Laravel 13.32.0, Angular 22.1.7, Angular CLI 22.1.7, Composer 2.10.3, Filament 5.8.2, MySQL 8.4 LTS, Redis 8.10.1, and Node.js 24.21.0 LTS.
  - Installed Node.js `v22.13.0` is not compatible with Angular 22 and must be replaced before frontend initialization.
  - Laravel Herd is the selected PHP/Composer environment. Do not use Docker.
  - Initial user shell verification showed Herd PHP `8.4.25` and Node `v22.13.0`; this was superseded by FND-001A.
  - Node.js 24 LTS must be used for Angular work.
  - Global Angular CLI `19.0.7` must not be used; use project-local Angular CLI 22 through `npx` or local npm scripts.
- Completed: 2026-09-18

### FND-001A - Verify Laravel Herd runtime before repository initialization

- Status: [x] Completed
- Dependencies: FND-001
- Files: `docs/ARCHITECTURE.md`, `docs/DECISIONS.md`, `docs/DEPLOYMENT.md`, `docs/TESTING.md`, `docs/TASKS.md`, `docs/SESSION_LOG.md`
- Acceptance criteria:
  - Herd CLI version is verified.
  - Herd PHP 8.5 is installed and selected for this project.
  - Composer works through `herd composer` using PHP 8.5.
  - Node.js 24 LTS is active for this project.
  - Global Angular CLI 19 is ignored in favor of `npx @angular/cli@22`.
- Tests:
  - `herd --version`
  - `herd php -v`
  - `herd composer --version`
  - `herd php:list`
  - `herd which-php`
  - `node --version`
  - `npm --version`
  - `nvm list`
- Notes:
  - Herd CLI `1.30.0` verified through installed Herd binary.
  - Herd PHP `8.5.10` verified through `herd php -v`.
  - Herd Composer `2.10.2` verified through `herd composer --version`, using PHP `8.5.10`.
  - Herd PHP 8.5 binary verified at `C:/Users/hamza/.config/herd/bin/php85/php.exe`.
  - Herd NVM has Node `24.21.0`; direct Herd-managed Node binary verified as `v24.21.0` with npm `11.19.0`.
  - Angular CLI `22.1.8` verified through `npx @angular/cli@22` with Node `24.21.0`.
  - PHP startup prints an OPcache API warning but commands exit successfully; monitor during Laravel initialization.
- Completed: 2026-09-18

### FND-002 - Initialize repository foundations

- Status: [x] Completed
- Dependencies: FND-001
- Files: `.editorconfig`, `.gitignore`, `README.md`, root workspace files
- Acceptance criteria:
  - Repository has non-destructive monorepo structure.
  - Root documentation explains available applications.
  - Ignore rules protect secrets, dependencies, builds, and local files.
  - Editor settings align frontend and backend formatting.
- Tests:
  - Manual file review.
- Notes:
  - Added root `.editorconfig`, `.gitignore`, `.nvmrc`, and `README.md`.
  - Added `frontend/.gitkeep` and `backend/.gitkeep` placeholders without initializing Angular or Laravel apps.
  - Docker remains intentionally unused.
  - README documents Herd PHP/Composer, Node.js 24, project-local Angular CLI 22, and the current uninitialized application state.
- Completed: 2026-09-18

### FND-003 - Initialize Laravel backend

- Status: [x] Completed
- Dependencies: FND-001, FND-002
- Files: `backend/`
- Acceptance criteria:
  - Laravel 13 app exists under `backend/`.
  - PHP requirement targets `^8.5`.
  - Base Laravel tests pass.
  - `.env.example` contains safe placeholders only.
- Tests:
  - `composer install`
  - `php artisan --version`
  - `php artisan test`
- Notes:
  - Initialized Laravel skeleton `v13.0.0`; installed framework version is Laravel `13.32.0`.
  - `backend/composer.json` requires PHP `^8.5` and Laravel framework `^13.0`.
  - App name is `Portfolio Platform API`.
  - Default locale is `en`; supported locales are documented as `en,ar`; timezone is UTC.
  - `.env` contains the generated app key and is ignored by Git; `.env.example` contains no generated key or production secrets.
  - No Filament, Sanctum, permissions, media packages, or business feature packages were installed.
  - Laravel installer detected local MySQL and ran default skeleton migrations for a local `portfolio` database; automated tests use in-memory SQLite through `phpunit.xml`.
  - Herd PHP emits an OPcache API warning from the CLI PHP configuration, but Composer, Artisan, Pint, and tests pass.
- Completed: 2026-09-18

### FND-004 - Initialize Angular frontend

- Status: [x] Completed
- Dependencies: FND-001, FND-002
- Files: `frontend/`
- Acceptance criteria:
  - Angular 22 app exists under `frontend/`.
  - SSR and hydration are configured.
  - Strict TypeScript is enabled.
  - bootstarp, linting, formatting, unit testing, and Playwright are configured.
- Tests:
  - `cmd /c npm install`
  - `cmd /c npm run build`
  - `cmd /c npm test`
- Notes:
  - Initialized Angular app `portfolio-frontend` in `frontend/` with Angular CLI `22.1.8` using Herd-managed Node.js `24.21.0` and npm `11.19.0`.
  - Angular framework packages are `22.1.7`; Angular CLI/build/SSR packages are `22.1.8`.
  - Standalone, routing, strict TypeScript, CSS, SSR, hydration, Vitest, and Playwright configuration are present.
  - Replaced the generated Angular demo page with a minimal semantic shell and title `Portfolio Platform`.
  - Production build generated browser/server output under `frontend/dist/portfolio-frontend`.
  - No nested `frontend/.git` repository was created.
  - Tailwind, Transloco, public pages, API integration, final design system, and E2E browser installation/runs are deferred to later tasks.
- Completed: 2026-09-18

### FND-005 - Configure local services and environment with Laravel Herd

- Status: [x] Completed
- Dependencies: FND-003, FND-004
- Files: `README.md`, `backend/.env.example`, Angular environment files, backend CORS/environment config, `docs/*.md`
- Acceptance criteria:
  - Laravel Herd is documented as the selected local development environment and Docker is not required by this task.
  - PHP 8.5 is used through Herd; when the `herd` wrapper is unavailable in automation, the verified absolute Herd PHP executable may be used.
  - Node.js 24 is used for frontend work and Angular CLI 22 is invoked through the project-local CLI or `npx @angular/cli@22`; global Angular CLI 19 is ignored.
  - Local MySQL `8.0.41` is accepted for initial development, staging/production target MySQL `8.4 LTS` is documented, and Laravel database connection is verified non-destructively.
  - Redis or Valkey is deferred for production/future optimization; local cache, session, and queue use database drivers.
  - Mail testing uses the Laravel `log` mailer locally; Mailpit/local SMTP and production SMTP are deferred.
  - Laravel environment settings document `APP_ENV`, local-only `APP_DEBUG`, `APP_URL`, database variables, Redis variables, queue/cache/session drivers, and safe `.env.example` values.
  - CORS requirements allow only Angular development and SSR origins; production wildcard CORS is not allowed.
  - Angular environment strategy documents development API URL, production placeholder URL, SSR compatibility, and no hardcoded API URLs inside components.
  - Verification commands pass and current deferred infrastructure tasks are documented.
- Tests:
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe -v`
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan --version`
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan about`
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan migrate:status`
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe artisan test`
  - `C:\Users\hamza\.config\herd\bin\php85\php.exe vendor\bin\pint --test`
  - `npm run build`
  - `npm test -- --watch=false`
  - `npm audit`
- Notes:
  - Docker scope was removed from the active task because the confirmed local strategy is Laravel Herd on Windows.
  - Absolute Herd PHP `C:/Users/hamza/.config/herd/bin/php85/php.exe` exists and reports PHP `8.5.10`.
  - Laravel boots as `Portfolio Platform API` on Laravel `13.32.0`, PHP `8.5.10`, database driver `mysql`, cache/session/queue drivers `database`, and mail driver `log`.
  - Laravel database connection reports MySQL `8.0.41`; this is accepted for initial local development only.
  - Staging/production target remains MySQL `8.4 LTS`; SQL and migrations must stay compatible with both and must not use MySQL 8.4-only features without documentation.
  - Default Laravel `cache` and `jobs` table migrations already exist and are marked ran; no duplicate migrations were generated.
  - Redis/Valkey and Mailpit/local SMTP remain deferred infrastructure tasks, not current blockers.
  - OPcache startup warning still appears but is non-blocking.
  - No Docker files were added, no service was installed or started, no PATH changes were made, and no destructive database command was run.
- Completed: 2026-09-18

### INF-001 - Configure and verify Redis or Valkey

- Status: [ ] Not started
- Dependencies: FND-005
- Files: deployment docs, backend environment docs, queue/cache configuration
- Acceptance criteria:
  - Redis or Valkey service is installed or provisioned in an approved environment.
  - Laravel cache, queue, rate limiting, and any distributed-lock usage are configured and verified.
  - Redis-dependent features, Horizon if selected, workers, and production queue configuration are documented.
- Tests:
  - Redis/Valkey connectivity check.
  - Laravel cache and queue smoke tests.
- Notes:
  - Deferred from FND-005 because current implemented features do not require Redis locally.
- Completed:

### INF-002 - Configure local mail inbox or production SMTP

- Status: [ ] Not started
- Dependencies: FND-005
- Files: deployment docs, backend mail configuration, notification tests
- Acceptance criteria:
  - Mailpit/local SMTP or production SMTP is configured in an approved environment.
  - Contact-form and notification delivery are verified before any email workflow is marked production-ready.
  - Secrets remain outside committed files.
- Tests:
  - Laravel mail notification tests.
  - Local inbox or SMTP delivery smoke test.
- Notes:
  - Deferred from FND-005 because local development uses `MAIL_MAILER=log`.
- Completed:

### INF-003 - Verify MySQL 8.4 staging and production compatibility

- Status: [ ] Not started
- Dependencies: FND-005, BE-001
- Files: deployment docs, database schema docs, migrations
- Acceptance criteria:
  - Staging/production MySQL `8.4 LTS` is available and verified.
  - Migrations and SQL remain compatible with both local MySQL `8.0.41` and target MySQL `8.4 LTS`.
  - Any MySQL 8.4-only feature use is explicitly documented and justified.
- Tests:
  - Non-destructive version check in staging/production-like environment.
  - Migration verification against MySQL 8.4.
- Notes:
  - Local MySQL `8.0.41` is accepted only for initial development.
- Completed:

## Phase 2 - Backend Core

### BE-001 - Design and implement database migrations

- Status: [x] Completed
- Dependencies: FND-003
- Files: `backend/database/migrations/`, `docs/DATABASE_SCHEMA.md`
- Acceptance criteria:
  - Required tables and indexes are implemented.
  - Translatable content strategy matches documented decision.
  - No raw IP address storage is introduced.
  - Migrations run cleanly from empty database.
- Tests:
  - `php artisan migrate:fresh --seed`
  - Backend schema tests where useful.
- Notes:
  - Added `2026_09_18_130000_create_portfolio_business_schema.php` for the documented portfolio business tables.
  - Implemented JSON translation columns for bilingual content and slug fields.
  - Implemented hash-based visitor/analytics fields (`visitor_id_hash`, `ip_hash`, `user_agent_hash`) without adding raw IP columns to business tables.
  - Added schema tests for table existence, JSON-capable translation fields, privacy hash columns, and key unique constraints.
  - Localized JSON slug uniqueness is deferred to application validation or a future documented generated-column/index strategy because portable JSON uniqueness differs between SQLite and MySQL.
  - ADM-002 later implemented the documented MySQL generated-column/index strategy for routed content slugs while preserving SQLite test portability.
  - Ran `migrate:fresh --seed` only against an in-memory SQLite testing database; no destructive local MySQL reset was performed.
  - Applied the new migration to local MySQL with non-destructive `artisan migrate --force`; migration status shows batch `[2] Ran`.
- Completed: 2026-09-18

### BE-002 - Implement models, factories, seeders, enums, and policies

- Status: [x] Completed
- Dependencies: BE-001
- Files: `backend/app/Models/`, `backend/database/factories/`, `backend/database/seeders/`, `backend/app/Policies/`
- Acceptance criteria:
  - Models represent documented schema.
  - Factories support tests without private demo credentials.
  - Policies protect dashboard-managed resources.
- Tests:
  - `php artisan test`
- Notes:
  - Added Eloquent models for all BE-001 business tables.
  - Added relationship methods, casts, composable scopes, sensitive attribute hiding, and polymorphic SEO metadata relationships.
  - Added string-backed enums for publication status, project media type, testimonial status, contact message status, and analytics event type.
  - Added `HasLocalizedAttributes` for explicit localized reads with English fallback and no request/global-locale coupling.
  - Added factories with bilingual fake data and useful states.
  - Added production-guarded `DevelopmentPortfolioSeeder` with fictional bilingual content and idempotent `updateOrCreate` usage.
  - Registered a broad authenticated-user dashboard policy for current business models; fine-grained role/permission rules are deferred.
  - Added focused tests for model relationships, casts, enums, localization, hidden sensitive attributes, policies, and seeder idempotency.
- Completed: 2026-09-18

### BE-003 - Implement public API resources and read endpoints

- Status: [x] Completed
- Dependencies: BE-002
- Files: `backend/routes/api.php`, `backend/app/Http/Controllers/Api/V1/`, `backend/app/Http/Resources/`, `docs/API_CONTRACT.md`
- Acceptance criteria:
  - Versioned `/api/v1` read endpoints exist.
  - Responses are localized and never expose private fields.
  - Pagination and cache headers are handled where appropriate.
- Tests:
  - Backend feature tests for public read endpoints.
- Notes:
  - Added 13 versioned public read routes under `/api/v1`.
  - Added public API controllers, Form Requests, Resources, localized response handling, pagination, cache headers, published-only filtering, and privacy-safe payloads.
  - Refactored public API controllers to the required service-layer architecture: controllers are thin, application services live under `App\Services\PublicApi`, query services live under `App\Queries\PublicApi`, and typed data objects live under `App\Data\PublicApi`.
  - Implemented read endpoints for site, about, projects, project categories, technologies, testimonials, blog posts, blog categories, tags, services, and social links.
  - Detail endpoints resolve localized slugs with English fallback and return 404 for unpublished, future, draft, archived, or missing content.
  - At BE-003 completion, public write endpoints, sitemap, robots, Filament/admin flows, and Angular API consumption remained deferred.
  - Architecture tests passed: `artisan test --filter=PublicApiArchitectureTest` (6 tests, 108 assertions).
  - Behavior tests passed: `artisan test --filter=PublicReadApiTest` (10 tests, 72 assertions).
  - Full verification passed: `artisan test` (29 tests, 285 assertions), `vendor\bin\pint --test`, syntax checks, route list, in-memory `migrate:fresh --seed`, and local MySQL `migrate:status`.
- Completed: 2026-09-18

### BE-004 - Implement public write workflows

- Status: [x] Completed
- Dependencies: BE-003
- Files: backend requests, services, controllers, resources, events, tests, configuration, documentation
- Acceptance criteria:
  - Contact, testimonial, project view, and project like workflows are implemented.
  - Form Requests validate payloads.
  - Rate limiting and spam controls exist.
  - Notifications use queues where appropriate.
- Tests:
  - Backend feature tests for write endpoints.
  - Privacy-sensitive tests for hidden fields and visitor identifiers.
- Notes:
  - Added anonymous public write routes for project views, project likes, project unlikes, testimonial submissions, and contact submissions.
  - Implemented thin controllers, Form Requests, DTOs, application services, API Resources, after-commit submission events, named rate limiters, CORS credentials support, and `no-store` write responses.
  - Visitor identity is server-issued through a first-party encrypted HTTP-only cookie and stored only as HMAC hashes for visitor ID, IP, and user agent. Public request bodies cannot provide visitor identity.
  - Project view uniqueness follows the implemented schema: one view per project, visitor hash, and UTC calendar date. A true rolling 24-hour window would require a future schema/service change.
  - Project likes/unlikes are idempotent and return authoritative counts.
  - Testimonials are stored as pending, consented, private records and public requests cannot set moderation fields.
  - Contact messages are stored as private `new` records; public APIs never return contact message records.
  - Contact attachments are explicitly rejected until safe private storage and upload security are configured.
  - Verification passed: syntax checks, route list showing 18 API routes, focused `PublicWriteApiTest` (11 tests, 88 assertions), focused architecture/write tests (19 tests, 323 assertions), full backend suite (42 tests, 500 assertions), `vendor\bin\pint --test`, local MySQL `migrate:status`, and `git diff --check` with line-ending warnings only.
  - `migrate:fresh --seed --env=testing` was not executed during BE-004 because the sandbox rejected it as a destructive database command; the full test suite exercised test database refreshes safely.
- Completed: 2026-09-18

## Phase 3 - Filament Dashboard

### ADM-001 - Configure Filament dashboard and authentication

- Status: [x] Completed
- Dependencies: BE-002
- Files: `backend/app/Providers/Filament/`, `backend/app/Filament/`
- Acceptance criteria:
  - Dashboard authentication works.
  - Policies are enforced.
  - Dashboard pages are not indexable.
- Tests:
  - Backend auth and authorization tests.
- Notes:
  - Installed Filament `5.8.2` and registered the admin panel at `/admin`.
  - Dashboard routes are limited to `/admin`, `/admin/login`, and `POST /admin/logout`; public registration is disabled.
  - Dashboard access requires explicit `users.is_admin=true` authorization through `User::canAccessPanel()` and dashboard model policies.
  - Added `is_admin`, `admin_granted_at`, and `admin_granted_by` to users for the initial owner authorization strategy.
  - Added `portfolio:provision-owner-admin` interactive owner provisioning command with hidden password input.
  - Added admin noindex/private-cache middleware and admin locale middleware for English LTR and Arabic RTL rendering.
  - Published Filament assets and verified the dashboard login route with a temporary local server.
  - Verification passed: PHP syntax checks, focused `AdminDashboardAuthTest` (11 tests, 57 assertions), full backend suite (53 tests, 560 assertions), `vendor\bin\pint --test`, `herd composer validate --strict`, `herd composer audit`, admin route list, migration status, frontend build/unit checks, Playwright browser install, Playwright Chromium assertion, and `/admin/login?locale=ar` HTTP smoke.
  - Playwright's Windows process remained attached to the Angular dev server after the passing assertion and required manual interruption during cleanup.
  - Herd PHP continues to print the known OPcache startup warning, but all ADM-001 backend checks pass.
- Completed: 2026-09-18

### ADM-002 - Implement content management resources

- Status: [x] Completed
- Dependencies: ADM-001
- Files: Filament resources for projects, media, blog, services, skills, experience, testimonials, messages, settings, SEO
- Acceptance criteria:
  - Owner can manage bilingual content without code edits.
  - Media previews and validation are present.
  - Moderation/status workflows are represented.
- Tests:
  - Filament/resource tests where practical.
- Notes:
  - Implemented Filament resources for projects, project categories, technologies, project media, blog posts, blog categories, tags, services, skills, experience, public site settings, social links, and parent-owned SEO metadata.
  - Testimonial moderation, contact inbox, and analytics widgets were intentionally not implemented because the active ADM-002 scope excluded later ADM-003/later workflows.
  - Added focused admin content services for localized slug normalization, bilingual translation preservation, project/blog/category/service/profile/site workflows, SEO upserts, rich-text sanitization, media lifecycle handling, and project media persistence.
  - Added server-side rich-text sanitization with an explicit HTML allowlist. Administrator-submitted rich content is sanitized before persistence.
  - Added MySQL-only generated columns and unique indexes for English and Arabic localized slugs on project categories, projects, blog categories, tags, blog posts, and services. SQLite test runs skip this MySQL-specific migration logic.
  - Added configurable media disk, MIME allowlists, and upload limits through `config/portfolio.php` and safe `.env.example` placeholders.
  - Media writes use Laravel filesystem abstraction, generated stored filenames, MIME verification, replacement-safe cleanup, and reference checks before deleting old files. Image transcoding and video conversion are not implemented.
  - Verification passed: `herd php artisan test` (58 tests, 580 assertions), `herd php vendor\bin\pint --test`, `herd composer validate --strict`, `herd composer audit`, `herd php artisan route:list --path=admin`, `herd php artisan migrate:status`, and frontend `cmd /c npm run build`.
  - Herd PHP continues to print the known OPcache startup warning, but all verification commands exited successfully.
- Completed: 2026-09-18

### ADM-003 - Implement analytics dashboard

- Status: [x] Completed
- Dependencies: BE-004, ADM-001
- Files: Filament widgets, analytics services, aggregation jobs
- Acceptance criteria:
  - Privacy-conscious analytics metrics are visible.
  - Date filters and useful summaries are available.
  - No invasive fingerprinting is introduced.
- Tests:
  - Backend tests for aggregation logic.
- Notes:
  - Added testimonial moderation and contact inbox resources because the ADM-003 request clarified this scope alongside analytics.
  - Added testimonial approve, reject, archive, and feature actions through `TestimonialModerationService`.
  - Added private contact message status and admin note workflows through `ContactInboxService`.
  - Added dashboard analytics widgets for total visits, unique visitors, project views, likes, contact submissions, pending testimonials, daily trends, top projects, and device breakdowns.
  - Added `AnalyticsDashboardQuery` so aggregation queries stay out of Filament widgets.
  - Public testimonial API remains approved-only; pending, rejected, and archived testimonials stay private.
  - Verification passed: focused ADM-003 tests, dashboard auth tests, public read/write/architecture tests, full backend suite, Pint, Composer validate/audit, admin route list, migration status, Angular build, and Angular tests.
  - Browser smoke was attempted with a temporary local server, but the server did not become reachable from the automation shell. Equivalent route, RTL, authorization, and API behavior are covered by feature tests.
- Completed: 2026-09-19

### ADM-003A - Expand comprehensive local development seed data

- Status: [x] Completed
- Dependencies: BE-002, ADM-003
- Files: `backend/database/seeders/DevelopmentPortfolioSeeder.php`, development fixture files, seeder tests, documentation
- Acceptance criteria:
  - Development seed data covers application-owned business tables where safe.
  - Fictional bilingual content includes enough records to exercise public pages and dashboard filters.
  - Seeders are idempotent, preserve user-created data, avoid destructive database operations, and refuse production.
  - Analytics fixtures are clearly development-only and contain no raw IP addresses.
- Tests:
  - Seeder coverage, repeatability, preservation, production denial, and privacy tests.
- Notes:
  - Expanded `DevelopmentPortfolioSeeder` to cover safe application-owned business tables with fictional bilingual records and development analytics fixtures.
  - Seeded users, project categories, technologies, projects, project-technology pivots, project media, project views, project likes, testimonials, blog categories, tags, blog posts, blog-post-tag pivots, services, experience, skills, contact messages, site settings, social links, SEO metadata, analytics events, and daily analytics summaries.
  - Contact attachments are intentionally excluded because secure private attachment storage is not implemented and public uploads remain rejected.
  - Framework infrastructure tables are not artificially seeded.
  - Seeder is local/testing-only, idempotent, non-destructive, preserves user-created records, uses stable fixture identifiers, and avoids raw IP storage.
  - Non-destructive local MySQL seeding passed with `herd php artisan db:seed --force`.
- Completed: 2026-09-19

### ADM-003B - Provision local development administrator

- Status: [x] Completed
- Dependencies: ADM-001, ADM-003A
- Files: `backend/.env.example`, provisioning service or seeder integration, tests, documentation
- Acceptance criteria:
  - Local administrator credentials are read from ignored environment variables.
  - Password is hashed and never committed or printed.
  - Provisioning is idempotent and does not reset existing administrator passwords.
  - Existing non-fixture account conflicts are not overwritten or elevated automatically.
  - Provisioning is denied outside local/testing environments.
- Tests:
  - Local administrator provisioning tests.
- Notes:
  - Added local-only administrator provisioning from ignored `.env` variables through `LocalDevelopmentAdministratorProvisioner`.
  - `.env.example` contains empty placeholders only; the password is not committed, documented, logged, or passed in command arguments.
  - Local administrator provisioning is idempotent, stores a hashed password, does not reset an existing administrator password on repeated seed runs, and refuses to elevate an existing non-admin account with the configured email.
  - Local database provisioning completed for `admin@example.com`.
- Completed: 2026-09-19

## Phase 4 - Angular Foundations

### FE-001 - Implement frontend app shell, SSR, routing, and layout

- Status: [x] Completed
- Dependencies: FND-004, BE-003
- Files: `frontend/src/`
- Acceptance criteria:
  - Standalone app shell is SSR-rendered.
  - Lazy routes exist for public pages.
  - Header, footer, language, and theme controls work.
  - Monochrome design tokens are integrated with bootstarp.
- Tests:
  - Angular production SSR build.
  - Unit tests for shell services/components.
- Notes:
  - 2026-09-23: FE-001 implementation resumed. ADM-002 and ADM-003 are both recorded as completed above; no inconsistency found.
  - Implemented a reusable standalone Angular public shell with skip link, responsive header, accessible mobile navigation, footer, language controls, and light/dark/system theme controls.
  - Added localized `/en/...` and `/ar/...` routes for home, projects, project details, about, services, blog, blog details, contact, privacy, and localized 404 handling.
  - Dynamic project and blog detail routes use SSR server rendering rather than prerender-only output.
  - Added bilingual shell/page placeholder copy for FE-001 only; full page content, API integration, forms, interactions, and content states remain deferred.
  - Added SSR-compatible locale, theme, navigation, public-site config, and SEO services. Browser storage and media APIs are guarded for SSR/constrained runtimes.
  - Frontend public configuration can be overridden with `PORTFOLIO_API_BASE_URL`, `PORTFOLIO_PUBLIC_ORIGIN`, or `globalThis.PORTFOLIO_PUBLIC_CONFIG`.
  - Raw SSR HTML verifies localized `lang`, `dir`, title, meta description, canonical, real static-page `hreflang` alternates, one H1, and HTTP 404 for unknown routes.
  - All FE-001 placeholder routes intentionally set `noindex` until real page content and production SEO rules are implemented in later tasks.
  - Verification used Node.js `v26.4.0` from the unsandboxed machine PATH because this sandbox did not expose the previously documented Herd Node path. Angular 22 supports `>=26.0.0`.
  - Playwright Chromium was installed for the current Windows user after the first browser-smoke attempt found the browser binary missing.
  - Verification passed: `cmd /c npm test -- --watch=false` (5 files, 7 tests), `cmd /c npm run build`, direct SSR HTTP checks against `http://127.0.0.1:4100`, `cmd /c npx playwright test` (6 tests across desktop/mobile Chromium), and `cmd /c npm audit` (0 vulnerabilities).
- Completed: 2026-09-23

### FE-002 - Implement localization and theme infrastructure

- Status: [x] Completed
- Dependencies: FE-001
- Files: frontend translation files, locale/theme services, SSR providers
- Acceptance criteria:
  - English and Arabic switching works at runtime.
  - `lang` and `dir` attributes update correctly.
  - Light, dark, and system modes persist without hydration flash.
- Tests:
  - Unit tests for locale and theme services.
  - SSR HTML inspection.
- Notes:
  - 2026-09-23: FE-002 started after FE-001 completion. Existing FE-001 locale/theme shell code will be audited and extended without duplicating working behavior.
  - FE-001 already provided localized routes, basic Arabic/English shell copy, basic theme controls, SSR metadata, and 404 behavior.
  - Added typed locale/navigation/page copy structures for shared UI labels, accessibility labels, theme labels, state messages, placeholders, and 404 content.
  - Added deterministic English fallback helpers for missing localized dynamic values without browser-only locale detection.
  - Fixed Arabic placeholder/page copy and kept `/en/...` and `/ar/...` explicit routes.
  - Added a typed dynamic project/blog localized slug switching strategy so API-provided slug mappings can be used later; missing mappings safely fall back to the target language listing page.
  - Hardened theme handling with applied theme state, SSR-safe storage and system preference resolution, system-theme change handling, persistent explicit preferences, and `theme-color` metadata updates.
  - Added centralized monochrome tokens for disabled, hover, and focus states; no colored accents or gradients were introduced.
  - Verification passed: `cmd /c npm test -- --watch=false` (5 files, 13 tests), `cmd /c npm run build`, direct SSR HTML checks for English/Arabic metadata and 404 behavior, `PLAYWRIGHT_BASE_URL=http://127.0.0.1:4100 cmd /c npx playwright test` (10 tests across desktop/mobile Chromium), and `cmd /c npm audit` (0 vulnerabilities).
- Completed: 2026-09-23

## Phase 5 - Public Pages

### PAGE-001 - Implement core public pages

- Status: [ ] Not started
- Dependencies: FE-002, BE-003
- Files: home, projects, project detail, about, services, blog, post detail, contact, privacy, not-found routes
- Acceptance criteria:
  - Required public pages are implemented with loading, empty, error, and success states.
  - Content is sourced from translations or API data, not hardcoded in components.
  - Responsive LTR and RTL layouts are verified.
- Tests:
  - Component tests.
  - Playwright critical navigation tests.
- Notes:
- Completed:

## Dedicated SEO Milestone

### SEO-001 - Implement and verify SEO foundation

- Status: [ ] Not started
- Dependencies: FE-001, BE-003
- Files: Angular SEO services/routes, Laravel sitemap/robots/redirect endpoints, `docs/SEO.md`, `docs/TESTING.md`
- Acceptance criteria:
  - SSR metadata exists in raw HTML.
  - Canonical, `hreflang`, Open Graph, Twitter/X Card, and JSON-LD data are present.
  - XML sitemap and robots.txt are generated.
  - Public pages have localized URLs and correct status codes.
  - Dashboard pages are not indexable.
- Tests:
  - Raw server-rendered HTML inspection.
  - Structured data validation.
  - Sitemap and robots checks.
  - Lighthouse SEO checks.
  - Broken link, duplicate metadata, missing alt, and accidental noindex checks.
- Notes:
- Completed:

## Phase 6 - Interactions and Analytics

### INT-001 - Implement visitor interactions

- Status: [ ] Not started
- Dependencies: BE-004, PAGE-001
- Files: frontend interaction services/components, backend interaction endpoints
- Acceptance criteria:
  - Project views and likes work with privacy-conscious visitor strategy.
  - Testimonials and contact submissions work with validation and rollback states.
  - WhatsApp button uses configurable message.
- Tests:
  - Backend feature tests.
  - Frontend unit tests.
  - Playwright form and interaction tests.
- Notes:
- Completed:

### INT-002 - Implement analytics aggregation and cleanup

- Status: [ ] Not started
- Dependencies: INT-001, ADM-003
- Files: Laravel jobs, scheduler, analytics docs/tests
- Acceptance criteria:
  - Aggregation jobs and cleanup are scheduled.
  - Privacy limitations are documented.
  - Dashboard metrics use aggregated data where appropriate.
- Tests:
  - Backend job and scheduler tests.
- Notes:
- Completed:

## Phase 7 - Quality and Deployment

### QA-001 - Add CI and quality gates

- Status: [ ] Not started
- Dependencies: FND-003, FND-004
- Files: CI configuration, frontend/backend scripts, `docs/TESTING.md`
- Acceptance criteria:
  - Backend tests, frontend tests, builds, linting, and formatting are available through documented commands.
  - CI does not require secrets for basic verification.
- Tests:
  - Local script dry run.
- Notes:
- Completed:

### QA-002 - Run accessibility, performance, security, and deployment review

- Status: [ ] Not started
- Dependencies: PAGE-001, SEO-001, INT-002
- Files: docs, frontend/backend fixes
- Acceptance criteria:
  - Accessibility checks pass for key pages in English and Arabic.
  - Performance and Core Web Vitals checks are documented.
  - Security review verifies validation, auth, headers, CORS, secrets, uploads, and no raw IP storage.
  - Deployment and backup docs are complete.
- Tests:
  - Lighthouse.
  - Playwright accessibility/navigation checks.
  - Backend security-focused tests.
- Notes:
- Completed:
