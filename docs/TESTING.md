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
  - ADM-002 PHP syntax checks for admin content services and Filament resources: passed.
  - ADM-002 focused `AdminContentResourceTest`: passed, 5 tests and 20 assertions.
  - ADM-002 tests cover admin content route/policy protection, resource registration, translation preservation, rich-text sanitization, SEO persistence, localized duplicate slug rejection, blog reading-time calculation, and project media MIME/replacement cleanup with `Storage::fake('public')`.
  - ADM-002 full backend `herd php artisan test`: passed, 58 tests and 580 assertions.
  - ADM-002 `herd php vendor\bin\pint --test`: passed.
  - ADM-002 `herd composer validate --strict`: passed, `./composer.json is valid`.
  - ADM-002 `herd composer audit`: passed, no security vulnerability advisories found.
  - ADM-002 `herd php artisan route:list --path=admin`: passed; 15 admin routes registered, including content resources.
  - ADM-002 `herd php artisan migrate:status`: passed; localized slug unique index migration is marked ran.
  - ADM-002 frontend `cmd /c npm run build`: passed.
  - ADM-003 focused `AdminModerationAnalyticsTest`: passed, 5 tests and 40 assertions.
  - ADM-003 focused `DevelopmentSeederCoverageTest`: passed, 6 tests and 58 assertions.
  - ADM-003 dashboard auth regression `AdminDashboardAuthTest`: passed, 11 tests and 57 assertions.
  - ADM-003 public API architecture regression `PublicApiArchitectureTest`: passed, 8 tests and 235 assertions.
  - ADM-003 public read regression `PublicReadApiTest`: passed, 10 tests and 72 assertions.
  - ADM-003 public write regression `PublicWriteApiTest`: passed, 11 tests and 88 assertions.
  - ADM-003 full backend `herd php artisan test`: passed, 69 tests and 680 assertions.
  - ADM-003 `herd php vendor\bin\pint --test`: passed.
  - ADM-003 `herd composer validate --strict`: passed, `./composer.json is valid`.
  - ADM-003 `herd composer audit`: passed, no security vulnerability advisories found.
  - ADM-003 `herd php artisan route:list --path=admin`: passed; 17 admin routes registered, including testimonials and contact inbox.
  - ADM-003 `herd php artisan migrate --force`: passed, nothing to migrate.
  - ADM-003 `herd php artisan migrate:status`: passed; all migrations marked ran.
  - ADM-003 non-destructive local MySQL `herd php artisan db:seed --force`: passed after adjusting the seeder to reuse existing local technology names.
  - ADM-003 local seed verification: passed with 2 users, 7 projects, 7 posts, 7 project media records, 567 project views, 12 likes, 6 testimonials, 4 contacts, 370 analytics events, 140 daily summaries, 0 contact attachments, and local admin email `admin@example.com` with a hashed password.
  - ADM-003 frontend `cmd /c npm run build`: passed.
  - ADM-003 frontend `cmd /c npm test -- --watch=false`: passed, 1 test file and 2 tests.
  - ADM-003 temporary Laravel HTTP smoke was attempted, but the server did not become reachable from the automation shell. Route registration, dashboard authorization, Arabic RTL login behavior, and seeded public API behavior are covered by automated tests.
  - Official/package compatibility verification passed for PHP 8.5.10, Laravel 13.32.0, Composer 2.10.3, Filament 5.8.2, Angular 22.1.7, Angular CLI 22.1.7, Node.js 24.21.0 LTS, MySQL 8.4 LTS, Redis 8.10.1, Sanctum 4.3.3, Spatie Permission 8.3.0, Pest 5.2.1, Playwright 1.63.0, Angular SSR/hydration, and Transloco 8.4.0.
  - Installed Node.js `v22.13.0` was found incompatible with Angular 22 because Angular 22 requires Node `^22.22.3 || ^24.15.0 || >=26.0.0`.
  - FE-001 `node --version` through the restricted sandbox failed because no Node binary was visible; unsandboxed machine PATH reported Node `v26.4.0`, which satisfies Angular 22's `>=26.0.0` range.
  - FE-001 `cmd /c npm test -- --watch=false`: passed, 5 test files and 7 tests covering shell rendering, localization, theme persistence, navigation URL switching, and SEO metadata.
  - FE-001 `cmd /c npm run build`: passed; Angular production SSR build generated browser/server output and prerendered 0 static routes by design.
  - FE-001 direct SSR server checks against `http://127.0.0.1:4100`: passed for localized `lang`/`dir`, title, meta description, canonical URL, static-page `hreflang`, one H1, dynamic project canonical handling, and unknown-route HTTP 404.
  - FE-001 first `cmd /c npx playwright test` attempt failed because the Playwright Chromium binary was not installed for `C:\Users\abdelaziz`; `cmd /c npx playwright install chromium` then passed.
  - FE-001 `PLAYWRIGHT_BASE_URL=http://127.0.0.1:4100 cmd /c npx playwright test`: passed, 6 tests across desktop and mobile Chromium covering localized navigation, theme switching, language switching, skip-link keyboard focus, mobile menu behavior, and 404 behavior.
  - FE-001 `cmd /c npm audit`: passed, found 0 vulnerabilities.
  - FE-002 `cmd /c npm test -- --watch=false`: passed, 5 test files and 13 tests covering locale resolution, deterministic translation fallback, locale persistence, route language switching, dynamic slug mapping strategy, theme persistence, system preference changes, theme metadata, and SSR safety.
  - FE-002 `cmd /c npm run build`: passed; Angular production SSR build generated browser/server output and prerendered 0 static routes by design.
  - FE-002 temporary SSR server on `http://127.0.0.1:4100`: direct raw HTML checks passed for English and Arabic `lang`/`dir`, localized title text, meta description, canonical URL, static-page `hreflang`, theme attributes, H1, and unknown-route HTTP 404.
  - FE-002 `PLAYWRIGHT_BASE_URL=http://127.0.0.1:4100 cmd /c npx playwright test`: passed, 10 tests across desktop/mobile Chromium covering English/Arabic, LTR/RTL, light/dark/system modes, theme persistence after reload, language switching, dynamic slug fallback, desktop/mobile navigation, keyboard skip-link access, and no unexpected browser console/page errors.
  - FE-002 `cmd /c npm audit`: passed, found 0 vulnerabilities.
  - PAGE-001 `npm.cmd test -- --watch=false`: passed, 7 test files and 18 tests covering public API query construction, encoded detail slugs, page facade loading/success/empty/error behavior, locale/theme/navigation/SEO regressions, and SSR-safe services.
  - PAGE-001 `npm.cmd run build`: passed; Angular production SSR build generated browser/server output and prerendered 0 static routes by design.
  - PAGE-001 temporary backend API server on `http://127.0.0.1:8000`: API smoke passed for `/api/v1/projects?locale=en&per_page=1`.
  - PAGE-001 temporary built Angular SSR server on `http://127.0.0.1:4000`: raw HTML checks passed for `/en/projects` and `/ar/projects`, including HTTP 200, rendered API-backed content cards, `index, follow` robots metadata, and exact SSR `<html lang="en" dir="ltr">` / `<html lang="ar" dir="rtl">` attributes.
  - PAGE-001 `npx.cmd playwright test --reporter=line`: passed, 10 tests across desktop/mobile Chromium covering English/Arabic, LTR/RTL, light/dark/system themes, preference persistence after reload, language switching, desktop/mobile navigation, keyboard access, dynamic detail slug fallback, localized 404 behavior, and no unexpected browser console/page errors.
  - PAGE-001 `npm.cmd audit`: passed, found 0 vulnerabilities.
  - SEO-001 focused `php artisan test --filter=PublicSeoTest`: passed, 3 tests and 28 assertions covering localized slug mappings, sitemap inclusion/exclusion, XML alternates, and environment-specific robots behavior.
  - SEO-001 final full backend `php artisan test`: passed, 72 tests and 708 assertions.
  - SEO-001 `C:\Users\abdelaziz\.config\herd\bin\php85\php.exe vendor\bin\pint --test`: passed.
  - SEO-001 Composer validation and audit: passed; `composer.json` is valid and no security advisories found.
  - SEO-001 full frontend `npm.cmd test -- --watch=false`: passed, 7 test files and 18 tests.
  - SEO-001 `npm.cmd run build`: passed; production SSR browser/server output generated.
  - SEO-001 `npm.cmd audit`: passed, found 0 vulnerabilities.
  - SEO-001 temporary Laravel server on `http://127.0.0.1:8000`: direct checks passed for `/sitemap.xml` and `/robots.txt`; local robots blocks indexing.
  - SEO-001 temporary built Angular SSR server on `http://127.0.0.1:4000`: direct raw HTML checks passed for canonical, Open Graph, Twitter Card, JSON-LD, `hreflang`, `x-default`, root HTTP 301 redirect to `/en`, and one H1 on representative pages.
  - SEO-001 dynamic project raw HTML check passed for localized `hreflang` alternates using API-provided English and Arabic slugs, `og:image`, `CreativeWork` JSON-LD, and one H1.
  - SEO-001 Playwright `PLAYWRIGHT_BASE_URL=http://127.0.0.1:4000 npx.cmd playwright test --reporter=line`: passed, 12 tests across desktop/mobile Chromium.
  - SEO-001 Lighthouse could not be run: `npx.cmd lighthouse --version` timed out and `npm.cmd ls lighthouse --depth=0` showed Lighthouse is not installed locally. No dependency was added for this task.
  - INT-001 frontend `cmd /c npm test -- --watch=false`: passed, 8 test files and 20 tests, including the public interaction API client.
  - INT-001 frontend `cmd /c npm run build`: passed; production SSR browser/server output generated without warnings.
  - INT-001 focused backend `PublicReadApiTest`: passed, 10 tests and 73 assertions.
  - INT-001 focused backend `PublicWriteApiTest`: passed, 12 tests and 98 assertions.
  - INT-001 full backend `artisan test`: passed, 73 tests and 719 assertions.
  - INT-001 Pint check: passed.
  - INT-001 isolated focused Playwright harness `cmd /c npm run test:e2e:int001`: passed twice consecutively, 16 tests per run across desktop and mobile Chromium.
  - INT-001 complete Playwright suite through the same isolated harness, `INT001_PLAYWRIGHT_CONFIG=playwright.config.ts cmd /c npm run test:e2e:int001`: passed, 28 tests across desktop and mobile Chromium.
  - INT-001 E2E harness uses a temp SQLite database, fictional E2E seed data, built Angular SSR, Laravel `artisan serve --no-reload`, explicit unused 127.0.0.1 frontend/API ports, process-scoped CORS/cookie/mail/env settings, exact owned PID cleanup, and no MySQL destructive operations.
  - INT-001 browser CORS/cookie evidence: readiness and browser responses emitted `Access-Control-Allow-Origin` for the generated Angular origin and `Access-Control-Allow-Credentials: true`; the visitor cookie was `HttpOnly`, unavailable to `document.cookie`, and reload preserved server-backed like state without localStorage.
  - INT-001 `cmd /c npm audit`: initial sandboxed attempt failed on registry/cache access; approved rerun passed with 0 vulnerabilities.
  - INT-002 focused `AnalyticsAggregationCleanupTest`: passed, 4 tests and 9 assertions covering daily summary aggregation, privacy-safe summary columns, dashboard summary usage, raw event cleanup, and scheduler registration.
  - INT-002 focused `AdminModerationAnalyticsTest`: passed, 5 tests and 40 assertions after dashboard query changes.
  - INT-002 full backend `artisan test`: passed, 77 tests and 728 assertions.
  - INT-002 PHP syntax checks passed for both analytics jobs, both analytics services, `bootstrap/app.php`, `config/portfolio.php`, `AnalyticsDashboardQuery`, and the new feature test. Herd PHP still prints the known OPcache API warning.
  - INT-002 Pint check passed for touched backend files.
  - INT-002 Composer validation and audit passed after adding Herd PHP to the command PATH; Composer emitted PHP 8.5 deprecation notices from its bundled dependencies, and no security advisories were found.
  - INT-002 browser/E2E checks were not run because this task changes backend scheduler/jobs/query behavior only and does not alter browser workflows.
  - QA-001 backend `composer run ci`: passed; it cleared config, ran the full backend test suite, and ran Pint. Full backend suite passed, 77 tests and 728 assertions.
  - QA-001 Composer validation and audit: passed; Composer emitted PHP 8.5 deprecation notices from its bundled dependencies, and no security advisories were found.
  - QA-001 frontend `npm run ci`: passed; Prettier check passed, Angular unit tests passed with 8 files and 20 tests, and Angular production SSR build completed.
  - QA-001 `npm audit`: initial sandboxed attempt failed on registry/cache access; approved rerun passed with 0 vulnerabilities.
  - QA-001 added `.github/workflows/quality-gates.yml` for secret-free backend/frontend CI using SQLite tests, generated CI app key, Node.js 24, npm ci, Composer validate/audit, npm audit, Pint, Prettier, tests, and build.
  - QA-002 task bookkeeping audit found `docs/TASKS.md` summary inconsistent with individual statuses and found no implementation/configuration/test/history evidence that `INF-001` Redis/Valkey or `INF-002` Mailpit/SMTP acceptance criteria were met. `INF-001` and `INF-002` were returned to not-started status, and the summary was repaired before QA-002 work continued.
  - QA-002 focused `SecurityDeploymentReviewTest`: passed, 4 tests and 32 assertions covering safe `.env.example` placeholders, explicit credentialed CORS without wildcards, public write no-store/private visitor-cookie behavior, private hash exclusions, contact attachment rejection, no raw IP columns, and media MIME allowlists.
  - QA-002 `cmd /c npm run build`: passed; production SSR build generated browser/server output. Browser initial raw size was `419.35 kB`, estimated transfer size was `99.41 kB`, and the lazy public-page chunk estimated transfer size was `20.74 kB`.
  - QA-002 isolated Playwright review `cmd /c npm run test:e2e:qa002`: passed, 8 tests across desktop and mobile Chromium covering English/Arabic landmarks, headings, directions, image alt text, horizontal overflow, skip-link focus, contact/testimonial form accessibility, private-data leakage checks, HTTP-only visitor-cookie privacy, browser storage privacy, and local navigation timing thresholds.
  - QA-002 Lighthouse check was attempted with `npx lighthouse --version` and `npx --yes lighthouse --version`; both remained without output for about 60 seconds and were stopped. Lighthouse is still unavailable locally, so no Lighthouse score is claimed.
  - QA-002 dependency audits: `cmd /c npm audit --audit-level=moderate` passed with 0 vulnerabilities after approved registry access; `cmd /c herd composer --working-dir=backend audit` passed with no security vulnerability advisories.
  - QA-002 backend CI `cmd /c herd composer --working-dir=backend run ci`: passed, including 81 tests, 760 assertions, and Pint.
  - QA-002 frontend CI `cmd /c npm run ci`: passed, including Prettier, 8 test files, 20 tests, and production SSR build.
  - INF-001 fresh Redis/Valkey verification attempt: `redis-cli` and `valkey-cli` were not found on PATH; `Test-NetConnection 127.0.0.1 -Port 6379` returned `TcpTestSucceeded=False`; `cmd /c herd services:list` reported Herd Pro is required to use services.
  - INF-001 Herd PHP extension check: `C:\Users\hamza\.config\herd\bin\php85\php.exe -m` shows the `redis` extension is loaded.
  - INF-001 Laravel runtime check: default `artisan about` reports cache, queue, and session drivers as `database`; with `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, and `SESSION_DRIVER=redis`, Laravel reports all three as `redis`, proving environment selection works.
  - INF-001 Redis smoke checks failed because no service is listening: direct PHP Redis connection to `127.0.0.1:6379` failed, and `artisan tinker --execute "cache()->store('redis')->put(...)"` failed with `RedisException No connection could be made because the target machine actively refused it.`
  - INF-002 fresh mail verification attempt: `mailpit` and `smtp4dev` were not found on PATH; `Test-NetConnection 127.0.0.1 -Port 2525` returned `TcpTestSucceeded=False`.
  - INF-002 Laravel runtime check: default `artisan about` reports mail as `log`; with `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, and `MAIL_PORT=2525`, Laravel reports mail as `smtp`, proving environment selection works.
  - INF-002 event-list check: public contact/testimonial submission events exist, but no application mail notification listeners are registered for them.
  - INF-002 SMTP smoke check failed because no local SMTP service is listening: direct `Mail::raw(...)` send to `127.0.0.1:2525` failed with `TransportException Connection could not be established`.
  - INF-003 fresh MySQL 8.4 verification attempt: `cmd /c mysql --version` reports MySQL Community Server `8.0.41`, `Test-NetConnection 127.0.0.1 -Port 3306` passed, Laravel `select version()` returned `8.0.41`, and `artisan migrate:status` showed all current migrations ran against the local database.
  - INF-003 environment discovery: `where mysqld` resolves to the MySQL 8.0 server path, Docker is not available, and no MySQL 8.4 LTS staging/production-like environment was available from this workspace. Migration SQL was reviewed for the known MySQL-specific generated-column slug-index migration, and no uncommitted migration changes were present, but MySQL 8.4 execution could not be verified.

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
npm run ci
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
herd composer run ci
npx @angular/cli@22 ng version
npm run build
npm test -- --watch=false
npm run ci
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

QA-001 local quality commands:

```powershell
cd backend
composer run ci

cd ..\frontend
npm run ci
```

In the Codex sandbox, prepend `C:\Users\hamza\.config\herd\bin\php85` to `PATH` before direct Composer commands so Composer can find PHP 8.5.

Use `cmd /c npm ...` when PowerShell script execution blocks `npm.ps1`.

## Known Untested Areas

- The Laravel backend and Angular frontend foundations exist.
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
  - Redis-backed cache/queues, notification listener delivery, S3 media storage, contact attachments, and Lighthouse scoring are still untested because their implementation/tooling tasks have not started or Lighthouse is not installed locally. QA-002 added local Playwright navigation timing smoke checks and documented build-size inputs, but no Lighthouse score is claimed.
- ADM-001 covers Filament dashboard authentication, explicit administrator authorization, noindex/private headers, English/Arabic direction, login throttling, logout, and interactive owner provisioning.
- ADM-002 covers implemented admin content services/resources, but does not test image transcoding, video conversion, S3 storage, or public browser upload flows because those capabilities are not implemented yet.
- ADM-003 covers testimonial moderation, contact inbox workflows, analytics aggregation boundaries, thin widget/query architecture, comprehensive seeder coverage, seeder repeatability, production refusal, local admin provisioning safety, and public privacy checks.
