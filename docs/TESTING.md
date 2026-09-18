# TESTING.md

## Strategy

Testing is required at each layer: Laravel backend tests, Angular unit tests, Playwright end-to-end tests, SEO verification, accessibility checks, and production build checks.

## Current Verification

- Required Markdown file existence check passed for `AGENTS.md` and all required `docs/*.md` files, including `docs/SEO.md`.
- Repository structure inspection confirmed `frontend/`, `backend/`, and `.git/` are not present.
- Runtime command inspection:
  - `php --version`: failed, `php` is not available on PATH.
  - `composer --version`: failed because PHP is not available on PATH.
  - `node --version`: passed, `v22.13.0`.
  - `cmd /c npm --version`: passed, `11.3.0`.
  - `cmd /c ng version`: passed, global Angular CLI `19.0.7`.
  - `docker --version`: failed, Docker is not available on PATH.
  - `docker compose version`: failed, Docker is not available on PATH.
  - Herd absolute binary `herd --version`: passed, `Herd 1.30.0`.
  - Herd absolute binary `herd php -v`: passed, PHP `8.5.10`.
  - Herd absolute binary `herd composer --version`: passed, Composer `2.10.2` through PHP `8.5.10`.
  - Herd absolute binary `herd php:list`: passed, PHP 8.5 installed; global Herd marker showed 8.6.
  - Herd absolute binary `herd which-php`: passed, `C:/Users/hamza/.config/herd/bin/php85/php.exe`.
  - Herd NVM absolute binary `nvm list`: passed, installed versions include `24.21.0`.
  - Herd-managed Node absolute binary: passed, `v24.21.0`.
  - Herd-managed npm absolute binary: passed, `11.19.0`.
  - `npx @angular/cli@22 version` with Herd Node 24 on PATH: passed, Angular CLI `22.1.8`.
  - FND-002 foundation file existence check: passed for `.editorconfig`, `.gitignore`, `.nvmrc`, `README.md`, `frontend/.gitkeep`, and `backend/.gitkeep`.
  - FND-002 manual file review: passed for README app inventory, ignore rules, and editor settings.
  - FND-003 Laravel creation: passed with `herd composer create-project laravel/laravel backend "^13.0"`.
  - FND-003 `herd php backend/artisan --version` from repository root: passed, Laravel Framework `13.32.0`.
  - FND-003 `herd composer --working-dir=backend validate`: passed, `./composer.json is valid`.
  - FND-003 `herd composer --working-dir=backend audit`: passed, no security vulnerability advisories found.
  - FND-003 `herd php backend/artisan about`: passed, app boots as `Portfolio Platform API`, Laravel `13.32.0`, PHP `8.5.10`, timezone `UTC`, locale `en`.
  - FND-003 `herd php backend/artisan test` from repository root: failed because PHPUnit vendor paths resolved relative to the repository root.
  - FND-003 `herd php artisan test` from `backend/`: passed, 2 tests and 2 assertions.
  - FND-003 `backend\vendor\bin\pint.bat --test`: passed.
  - FND-003 `.env` ignore check: passed through `backend/.gitignore`.
  - FND-003 `.env.example` secret check: passed; `APP_KEY` is empty and no generated key is present.
  - FND-004 Node environment check through Herd Node path: passed, Node.js `v24.21.0`, npm `11.19.0`.
  - FND-004 `npx @angular/cli@22 version`: passed, Angular CLI `22.1.8`, Angular `22.1.7`, Node `24.21.0`.
  - FND-004 Angular initialization: passed with `npx @angular/cli@22 new portfolio-frontend --directory frontend --routing --style css --ssr --standalone --strict --package-manager npm --skip-git --defaults`.
  - FND-004 `npm run build`: passed; generated browser and server SSR output under `frontend/dist/portfolio-frontend`.
  - FND-004 `npm test -- --watch=false`: passed; 1 test file, 2 tests.
  - FND-004 `npm audit`: passed, found 0 vulnerabilities.
  - FND-004 `npx playwright --version`: passed, Playwright `1.63.0`.
  - FND-004 SSR HTML inspection: passed; prerendered HTML includes `lang="en"`, title `Portfolio Platform`, semantic `<main>`, hydration markers/state, and no `noindex`.
  - FND-004 nested Git check: passed; no `frontend/.git` exists.
  - FND-005 obsolete Docker scope review: passed; active task scope was replaced with Laravel Herd local services and environment configuration.
  - FND-005 packaged Herd CLI discovery: wrapper unavailable; `C:/Program Files/Herd/resources/app.asar.unpacked/resources/bin/herd.bat` exists but reports `No usable PHP version found` from this shell.
  - FND-005 direct Herd PHP fallback: used `C:/Users/hamza/.config/herd/bin/php85/php.exe` for Artisan, tests, and Pint.
  - FND-005 Node check: `node --version` passed with `v24.19.0`; `cmd /c npx @angular/cli@22 version` passed with Angular CLI `22.1.8` and Angular `22.1.7`, but this Node path is not verified as Herd-managed NVM.
  - FND-005 MySQL discovery: `mysql --version` reports MySQL client `8.0.41`; TCP `127.0.0.1:3306` is reachable.
  - FND-005 Redis/Valkey discovery: `redis-cli` and `valkey-cli` are not on PATH; TCP `127.0.0.1:6379` is not reachable.
  - FND-005 mail discovery: TCP `127.0.0.1:2525` is not reachable; current mail testing strategy remains `MAIL_MAILER=log`.
  - FND-005 absolute Herd PHP discovery: passed; `C:/Users/hamza/.config/herd/bin/php85/php.exe` exists and reports PHP `8.5.10`.
  - FND-005 `php85\php.exe backend\artisan --version`: passed, Laravel Framework `13.32.0`.
  - FND-005 `php85\php.exe backend\artisan about`: passed; app boots with PHP `8.5.10`, database `mysql`, cache/session/queue `database`, and mail `log`.
  - FND-005 `php85\php.exe backend\artisan migrate:status`: passed; default users, cache, and jobs migrations are marked ran.
  - FND-005 Laravel database version query: passed, MySQL `8.0.41`.
  - FND-005 `php85\php.exe artisan test` from `backend/`: passed, 2 tests and 2 assertions.
  - FND-005 `php85\php.exe vendor\bin\pint --test`: passed.
  - FND-005 `npm run build`: passed; Angular SSR build generated browser and server output.
  - FND-005 `npm test -- --watch=false`: passed; 1 test file and 2 tests.
  - FND-005 `npm audit`: initial sandboxed run failed due registry/cache access; escalated run passed with 0 vulnerabilities.
  - BE-001 PHP syntax check for portfolio migration and schema test: passed.
  - BE-001 focused `DatabaseSchemaTest`: passed, 4 tests and 67 assertions.
  - BE-001 full backend test suite: passed, 6 tests and 69 assertions.
  - BE-001 in-memory testing database `migrate:fresh --seed`: passed; default Laravel migrations plus portfolio business schema migration ran cleanly.
  - BE-001 local MySQL `artisan migrate --force`: passed; portfolio business schema migration applied as batch 2 without destructive reset.
  - BE-001 local MySQL `artisan migrate:status`: passed; portfolio business schema migration shows `[2] Ran`.
  - BE-001 Pint check: passed.
  - BE-002 PHP syntax checks for new enums, policy, seeders, localization trait, and focused tests: passed.
  - BE-002 focused `ModelLayerTest`: passed, 6 tests and 33 assertions.
  - BE-002 focused `DevelopmentSeederTest`: passed, 1 test and 3 assertions.
  - BE-002 full backend test suite: passed, 13 tests and 105 assertions.
  - BE-002 Pint check: passed.
  - BE-002 local MySQL `artisan db:seed --force`: passed; ran the idempotent `DevelopmentPortfolioSeeder`.
  - BE-003 PHP syntax checks for public API controllers, requests, resources, and `PublicReadApiTest`: passed.
  - BE-003 `artisan route:list --path=api/v1`: passed; 13 public read routes are registered.
  - BE-003 focused `PublicReadApiTest`: passed, 10 tests and 72 assertions.
  - BE-003 service-layer architecture remediation `PublicApiArchitectureTest`: passed, 6 tests and 108 assertions.
  - BE-003 full backend test suite after service-layer remediation: passed, 29 tests and 285 assertions.
  - BE-003 in-memory SQLite `migrate:fresh --seed`: passed; migrations and the development seeder ran cleanly.
  - BE-003 local MySQL `artisan migrate:status`: passed; default and portfolio business migrations are marked ran.
  - BE-003 `vendor\bin\pint --test` through Herd PHP: passed after Pint formatted service-layer files.
  - BE-004 focused `PublicWriteApiTest`: passed, 11 tests and 88 assertions.
  - BE-004 focused `PublicApiArchitectureTest|PublicWriteApiTest`: passed, 19 tests and 323 assertions.
  - BE-004 tests cover project view idempotency, daily view uniqueness, like/unlike idempotency, published-only project writes, pending private testimonial submissions, private contact submissions, honeypot validation, consent validation, attachment rejection, endpoint-specific rate limiting, and direct service use without HTTP request state.
  - BE-004 PHP syntax checks for new/changed data objects, events, controllers, middleware, requests, resources, services, providers, config, routes, and tests: passed.
  - BE-004 `artisan route:list --path=api/v1`: passed; 18 API routes registered, including 5 public write routes.
  - BE-004 full backend `artisan test`: passed, 42 tests and 500 assertions.
  - BE-004 `vendor\bin\pint --test`: passed after Pint fixed import ordering and line endings in two files.
  - BE-004 local MySQL `artisan migrate:status`: passed; default and portfolio business migrations are marked ran.
  - BE-004 `git diff --check`: passed with line-ending normalization warnings only.
  - BE-004 `migrate:fresh --seed --env=testing` was not executed because the sandbox rejected it as a destructive database command; BE-004 verification used the full test suite's safe database refresh behavior instead.
  - ADM-001 `herd php -v`: passed, PHP `8.5.10`; Herd still prints the known OPcache startup warning.
  - ADM-001 `herd composer --version`: passed, Composer `2.10.2` through PHP `8.5.10`.
  - ADM-001 Filament installation: passed with `filament/filament` locked at `v5.8.2`; `herd php artisan filament:assets` published Filament assets.
  - ADM-001 PHP syntax checks for changed admin model, policy, middleware, provider, command, migration, and tests: passed.
  - ADM-001 `herd php artisan route:list --path=admin`: passed; registered `/admin`, `/admin/login`, and `POST /admin/logout` only.
  - ADM-001 `herd php artisan migrate:status`: passed; admin authorization migration is marked ran.
  - ADM-001 `herd composer validate --strict`: passed, `./composer.json is valid`.
  - ADM-001 `herd composer audit`: passed, no security vulnerability advisories found.
  - ADM-001 focused `AdminDashboardAuthTest`: passed, 11 tests and 57 assertions.
  - ADM-001 full backend `herd php artisan test`: passed, 53 tests and 560 assertions.
  - ADM-001 `herd php vendor\bin\pint --test`: passed.
  - ADM-001 `cmd /c npx @angular/cli@22 version`: passed with Angular CLI `22.1.8`, Angular `22.1.7`, Node `24.19.0`, and npm `11.19.0`; the global Angular CLI 19 was not used.
  - ADM-001 frontend `cmd /c npm run build`: passed.
  - ADM-001 frontend `cmd /c npm test -- --watch=false`: passed, 1 test file and 2 tests.
  - ADM-001 Playwright browser install: passed with `cmd /c npx playwright install chromium`.
  - ADM-001 `cmd /c npx playwright test`: the Chromium spec assertion passed; on Windows the command stayed attached to the Angular dev server and required manual interruption during cleanup.
  - ADM-001 temporary Laravel HTTP smoke: passed for `/admin/login?locale=ar` with status `200`, `X-Robots-Tag: noindex, nofollow, noarchive`, private no-store cache headers, and RTL markup.
  - Official/package compatibility verification passed for PHP 8.5.10, Laravel 13.32.0, Composer 2.10.3, Filament 5.8.2, Angular 22.1.7, Angular CLI 22.1.7, Node.js 24.21.0 LTS, MySQL 8.4 LTS, Redis 8.10.1, Sanctum 4.3.3, Spatie Permission 8.3.0, Pest 5.2.1, Playwright 1.63.0, Angular SSR/hydration, and Transloco 8.4.0.
  - Installed Node.js `v22.13.0` was found incompatible with Angular 22 because Angular 22 requires Node `^22.22.3 || ^24.15.0 || >=26.0.0`.

## Backend Tests

- Use Pest or PHPUnit.
- Feature tests for every public read/write endpoint.
- Authorization tests for dashboard policies.
- Privacy tests to ensure raw IPs, visitor identifiers, contact messages, and testimonial emails are not exposed.
- Upload validation tests.

## Frontend Tests

- Use the testing solution officially supported by the installed Angular version.
- Unit tests for services, forms, guards, SEO utilities, theme, and localization.
- Component tests for critical states where practical.

## E2E Tests

- Use Playwright for critical public flows:
  - Navigation in English and Arabic.
  - Theme switching.
  - Project browsing and details.
  - Contact submission validation.
  - Testimonial submission validation.
  - Like optimistic update and rollback behavior.

## SEO Tests

- Inspect raw server-rendered HTML without client-side JavaScript.
- Confirm titles, descriptions, canonical links, `hreflang`, Open Graph, Twitter/X cards, and JSON-LD are present.
- Validate XML sitemap and robots.txt.
- Verify localized URLs, 200/301/404/410 status codes, Lighthouse SEO, Core Web Vitals, broken internal links, image alt text, duplicate metadata, accidental `noindex`, and dashboard non-indexability.

## Required Commands

Commands must be updated after applications are initialized.

```bash
php --version
composer --version
php artisan --version
php artisan test
node --version
npm --version
npx @angular/cli@22 ng version
npm run build
npm test
npx playwright test
```

On this Windows/Herd setup, prefer:

```powershell
herd php -v
herd composer --version
herd php backend/artisan --version
herd composer --working-dir=backend validate
herd composer --working-dir=backend audit
cd backend
herd php artisan test
npx @angular/cli@22 ng version
npm run build
npm test -- --watch=false
npm audit
npm run test:e2e
```

When the `herd` wrapper is unavailable in automation, use the verified absolute Herd PHP executable:

```powershell
C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan --version
C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan about
C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan migrate:status
cd backend
C:\Users\hamza\.config\herd\bin\php85\php.exe artisan test
C:\Users\hamza\.config\herd\bin\php85\php.exe vendor\bin\pint --test
```

Use `cmd /c npm ...` when PowerShell script execution blocks `npm.ps1`.

## Known Untested Areas

- No application code exists yet.
- Herd PHP 8.5 and Herd Composer are verified.
- Docker must not be used for this project.
- The globally installed Angular CLI is `19.0.7`, so Angular 22 CLI must be invoked through a project-local install or `npx`.
- Direct `node` in the Codex sandbox still resolves to `v22.13.0`; use the Herd-managed Node `24.21.0` binary or prepend its directory for Angular commands.
- Herd PHP prints an OPcache API warning on startup. It comes from the CLI PHP configuration loaded at `C:\Users\hamza\.config\herd\bin\php85\php.ini`. Composer, Artisan, Pint, and tests still pass; keep monitoring it as an environment issue.
- PowerShell blocks the Herd Node `npm.ps1` and `npx.ps1` shims; use `npm.cmd` and `npx.cmd` or prepend the Herd Node path in a command-scoped environment.
- `npm install --save-dev @playwright/test` emitted npm's install-scripts review warning for packages with install scripts. The install and audit completed successfully.
- Playwright is configured, but browser binaries were not installed or tested during FND-004 because the task did not require running E2E tests.
- FND-005 is complete using approved local fallbacks. Redis/Valkey, Mailpit/SMTP, and MySQL 8.4 staging/production verification remain pending infrastructure tasks.
- BE-001 schema tests verify portfolio tables, JSON-capable translation columns, hash-based visitor privacy columns, and key unique constraints. SQLite reports Laravel JSON columns as `text`, so tests accept both `json` and SQLite `text` storage types.
- BE-002 model tests verify relationships, casts, enum values, localized access, hidden sensitive fields, and authenticated dashboard policy behavior.
- BE-003 public API tests verify published-only read behavior, localized responses, filter validation, localized slug lookup, pagination metadata, public cache headers, privacy exclusions, and a basic project-list query-count guard.
- BE-003 architecture tests verify public API controllers do not contain Eloquent query calls, controllers delegate to services, services apply visibility rules independently from HTTP, query services apply filters/eager loading, invalid transport input is rejected before query filters run, and Resources do not trigger unexpected lazy-loading queries in the covered project-detail path.
- BE-004 public write tests verify anonymous project views, likes/unlikes, testimonial submissions, contact submissions, validation/spam controls, privacy exclusions, event dispatch, direct service usage, endpoint-specific rate limiting, transaction usage, query-free Resources, and thin controller boundaries.
- Sitemap/robots, dashboard content resources, Redis-backed cache/queues, frontend API integration, notification listener delivery, and contact attachments are still untested because their implementation tasks have not started.
- ADM-001 covers Filament dashboard authentication, explicit administrator authorization, noindex/private headers, English/Arabic direction, login throttling, logout, and interactive owner provisioning.
