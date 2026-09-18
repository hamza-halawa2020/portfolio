# TASKS.md

## Summary

| Completed | Active | Pending | Blocked |
| ---: | ---: | ---: | ---: |
| 6 | 0 | 16 | 0 |

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

### FND-005 - Configure Docker and development services

- Status: [ ] Not started
- Dependencies: FND-003, FND-004
- Files: `docker/`, `docker-compose.yml`, backend/frontend environment docs
- Acceptance criteria:
  - Docker services exist for PHP, MySQL, Redis, Mailpit, and Node tooling.
  - Native and Docker setup paths are documented.
  - PHP 8.5 is used consistently.
- Tests:
  - Docker config validation.
  - Documented startup smoke test.
- Notes:
- Completed:

## Phase 2 - Backend Core

### BE-001 - Design and implement database migrations

- Status: [ ] Not started
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
- Completed:

### BE-002 - Implement models, factories, seeders, enums, and policies

- Status: [ ] Not started
- Dependencies: BE-001
- Files: `backend/app/Models/`, `backend/database/factories/`, `backend/database/seeders/`, `backend/app/Policies/`
- Acceptance criteria:
  - Models represent documented schema.
  - Factories support tests without private demo credentials.
  - Policies protect dashboard-managed resources.
- Tests:
  - `php artisan test`
- Notes:
- Completed:

### BE-003 - Implement public API resources and read endpoints

- Status: [ ] Not started
- Dependencies: BE-002
- Files: `backend/routes/api.php`, `backend/app/Http/Controllers/Api/V1/`, `backend/app/Http/Resources/`, `docs/API_CONTRACT.md`
- Acceptance criteria:
  - Versioned `/api/v1` read endpoints exist.
  - Responses are localized and never expose private fields.
  - Pagination and cache headers are handled where appropriate.
- Tests:
  - Backend feature tests for public read endpoints.
- Notes:
- Completed:

### BE-004 - Implement public write workflows

- Status: [ ] Not started
- Dependencies: BE-003
- Files: backend requests, actions, controllers, resources, notifications, tests
- Acceptance criteria:
  - Contact, testimonial, project view, and project like workflows are implemented.
  - Form Requests validate payloads.
  - Rate limiting and spam controls exist.
  - Notifications use queues where appropriate.
- Tests:
  - Backend feature tests for write endpoints.
  - Privacy-sensitive tests for hidden fields and visitor identifiers.
- Notes:
- Completed:

## Phase 3 - Filament Dashboard

### ADM-001 - Configure Filament dashboard and authentication

- Status: [ ] Not started
- Dependencies: BE-002
- Files: `backend/app/Providers/Filament/`, `backend/app/Filament/`
- Acceptance criteria:
  - Dashboard authentication works.
  - Policies are enforced.
  - Dashboard pages are not indexable.
- Tests:
  - Backend auth and authorization tests.
- Notes:
- Completed:

### ADM-002 - Implement content management resources

- Status: [ ] Not started
- Dependencies: ADM-001
- Files: Filament resources for projects, media, blog, services, skills, experience, testimonials, messages, settings, SEO
- Acceptance criteria:
  - Owner can manage bilingual content without code edits.
  - Media previews and validation are present.
  - Moderation/status workflows are represented.
- Tests:
  - Filament/resource tests where practical.
- Notes:
- Completed:

### ADM-003 - Implement analytics dashboard

- Status: [ ] Not started
- Dependencies: BE-004, ADM-001
- Files: Filament widgets, analytics services, aggregation jobs
- Acceptance criteria:
  - Privacy-conscious analytics metrics are visible.
  - Date filters and useful summaries are available.
  - No invasive fingerprinting is introduced.
- Tests:
  - Backend tests for aggregation logic.
- Notes:
- Completed:

## Phase 4 - Angular Foundations

### FE-001 - Implement frontend app shell, SSR, routing, and layout

- Status: [ ] Not started
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
- Completed:

### FE-002 - Implement localization and theme infrastructure

- Status: [ ] Not started
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
- Completed:

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
