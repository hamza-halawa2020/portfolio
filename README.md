# Portfolio Platform

Production-ready bilingual portfolio platform for a Laravel developer.

## Applications

- `frontend/` - Angular 22 public website foundation with SSR and hydration.
- `backend/` - Laravel 13 API with implemented public read/write endpoints and a Filament owner dashboard at `/admin`.
- `docs/` - Project requirements, architecture, task tracking, testing, deployment, and session logs.

## Local Environment

This project uses Laravel Herd on Windows for PHP and Composer. Docker is not used.

Required runtime commands:

```powershell
herd php -v
herd composer --version
herd services:list
herd services:available
herd services:versions
node --version
npm --version
npx @angular/cli@22 version
```

Laravel work must use:

```powershell
herd php
herd composer
```

Angular work must use Node.js 24 LTS and the project-local Angular CLI 22:

```powershell
cd frontend
npm run build
npm test -- --watch=false
npm run test:e2e
```

Do not use the globally installed Angular CLI 19.

Current local service target:

- Backend Herd URL: `https://backend.test`
- Angular dev URL: `http://localhost:4200`
- Angular SSR dev URL: `http://localhost:4000`
- Local database: MySQL `8.0.41` is accepted for initial development.
- Staging/production database target: MySQL `8.4 LTS`.
- Cache, session, and queue drivers: Laravel database drivers for local development.
- Future cache/queue target: Redis or Valkey before Redis-dependent features, Horizon, distributed locks, or production queue configuration are marked complete.
- Mail testing: Laravel `log` mailer locally; Mailpit/local SMTP and production SMTP are deferred.

When the `herd` wrapper is unavailable in automation, use the existing Herd PHP executable directly:

```powershell
C:\Users\hamza\.config\herd\bin\php85\php.exe backend\artisan --version
```

## Current State

The repository foundations, Laravel backend foundation, Angular frontend foundation, portfolio schema/model layer, public read/write API, and Filament dashboard authentication are initialized.

## Backend

Use Herd commands from the repository root or from `backend/`:

```powershell
herd php backend/artisan --version
herd composer --working-dir=backend validate
herd composer --working-dir=backend audit
```

Run backend tests from the backend directory so PHPUnit vendor paths resolve correctly:

```powershell
cd backend
herd php artisan test
```

List the implemented public API routes:

```powershell
cd backend
herd php artisan route:list --path=api/v1
```

Implemented public read endpoints include `/api/v1/site`, `/api/v1/about`, `/api/v1/projects`, `/api/v1/projects/{slug}`, `/api/v1/posts`, `/api/v1/posts/{slug}`, `/api/v1/testimonials`, `/api/v1/services`, taxonomies, and social links.

Implemented public write endpoints include `POST /api/v1/projects/{slug}/views`, `POST /api/v1/projects/{slug}/likes`, `DELETE /api/v1/projects/{slug}/likes`, `POST /api/v1/testimonials`, and `POST /api/v1/contact`.

Filament dashboard:

```powershell
cd backend
herd php artisan route:list --path=admin
herd php artisan portfolio:provision-owner-admin
```

The dashboard is available at `/admin`. Registration is disabled, access requires a user with `is_admin=true`, and dashboard responses send `noindex` plus private no-store cache headers. The provisioning command prompts interactively for owner details and hides the password input.

Seed small fictional local development content only after migrations are applied:

```powershell
cd backend
herd php artisan db:seed
```

The development seeder is guarded from production and uses `updateOrCreate`; it does not truncate tables or create real client data.

See `docs/TASKS.md` for the current task status and `docs/SESSION_LOG.md` for verification history.
