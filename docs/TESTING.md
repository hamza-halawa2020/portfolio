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
herd php artisan --version
herd php artisan test
npx @angular/cli@22 ng version
```

Use `cmd /c npm ...` when PowerShell script execution blocks `npm.ps1`.

## Known Untested Areas

- No application code exists yet.
- Herd PHP 8.5 and Herd Composer are verified.
- Docker must not be used for this project.
- The globally installed Angular CLI is `19.0.7`, so Angular 22 CLI must be invoked through a project-local install or `npx`.
- Direct `node` in the Codex sandbox still resolves to `v22.13.0`; use the Herd-managed Node `24.21.0` binary or prepend its directory for Angular commands.
- Herd PHP prints an OPcache API warning on startup; monitor during Laravel initialization.
