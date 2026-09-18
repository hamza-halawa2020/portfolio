# Portfolio Platform

Production-ready bilingual portfolio platform for a Laravel developer.

## Applications

- `frontend/` - Angular 22 public website foundation with SSR and hydration.
- `backend/` - Laravel 13 API foundation. Filament dashboard is not installed yet.
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

The repository foundations, Laravel backend foundation, and Angular frontend foundation are initialized.

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

Seed small fictional local development content only after migrations are applied:

```powershell
cd backend
herd php artisan db:seed
```

The development seeder is guarded from production and uses `updateOrCreate`; it does not truncate tables or create real client data.

See `docs/TASKS.md` for the current task status and `docs/SESSION_LOG.md` for verification history.
