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
- Database target: MySQL 8.4 LTS for local/prod parity.
- Cache/queue target: Redis or Valkey once a local service is installed and verified.
- Mail testing: Laravel `log` mailer until a local mail service is explicitly configured.

FND-005 is currently blocked in the Codex shell because Herd PHP cannot be resolved by the packaged Herd CLI, Redis/Valkey is not reachable on `127.0.0.1:6379`, and the reachable MySQL client is `8.0.41` rather than the selected MySQL `8.4 LTS` target.

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

See `docs/TASKS.md` for the current task status and `docs/SESSION_LOG.md` for verification history.
