# Portfolio Platform

Production-ready bilingual portfolio platform for a Laravel developer.

## Applications

- `frontend/` - Angular 22 public website. Not initialized yet.
- `backend/` - Laravel 13 API foundation. Filament dashboard is not installed yet.
- `docs/` - Project requirements, architecture, task tracking, testing, deployment, and session logs.

## Local Environment

This project uses Laravel Herd on Windows for PHP and Composer. Docker is not used.

Required runtime commands:

```powershell
herd php -v
herd composer --version
node --version
npm --version
npx @angular/cli@22 version
```

Laravel work must use:

```powershell
herd php
herd composer
```

Angular work must use Node.js 24 LTS and a project-local Angular CLI 22 invocation:

```powershell
npx @angular/cli@22
```

Do not use the globally installed Angular CLI 19.

## Current State

The repository foundations and Laravel backend foundation are initialized. The Angular application has not been created yet.

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

See `docs/TASKS.md` for the current task status and `docs/SESSION_LOG.md` for verification history.
