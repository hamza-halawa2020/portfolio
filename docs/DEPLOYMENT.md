# DEPLOYMENT.md

## Development Setup

The repository will support native and Docker-based local development after foundation tasks are complete.

Current state: the Laravel backend has been initialized in `backend/`; the Angular frontend has not been initialized.

### Selected Local Strategy After Herd Verification

Laravel Herd on Windows is the selected local PHP and Composer environment. Do not use Docker for this project and do not ask for separate PHP or Composer installation.

Current user-shell verification:

- `herd php -v`: PHP `8.5.10`.
- `herd composer --version`: Composer `2.10.2`, running through Herd PHP `8.5.10`.
- Herd PHP binary: `C:/Users/hamza/.config/herd/bin/php85/php.exe`.
- Herd-managed Node.js: `v24.21.0`.
- Herd-managed npm: `11.19.0`.
- Angular CLI through `npx @angular/cli@22`: `22.1.8`.

Local runtime rules:

- Use Herd PHP 8.5 for Laravel.
- Use `herd composer` for Composer.
- Use Node.js 24 LTS for Angular.
- The global Angular CLI `19.0.7` must be ignored.

The Codex sandbox cannot resolve `herd` or `nvm` from PATH, so automation should invoke Herd binaries by absolute path or run through the user's normal shell. Direct `node` still resolves to `v22.13.0` in the sandbox; Angular commands must run with the Herd Node 24 directory first on PATH.

## Environment Variables

Environment files must use safe placeholders only. Required categories will include:

- Laravel app key, URL, environment, debug flag.
- MySQL connection.
- Redis connection.
- Queue and cache drivers.
- Mail provider or Mailpit.
- Filesystem disk and S3-compatible storage credentials.
- Visitor hash secret.
- Angular API base URL.
- Public site URL.

## Build Commands

Backend commands:

```powershell
herd composer --working-dir=backend install
herd composer --working-dir=backend validate
herd composer --working-dir=backend audit
cd backend
herd php artisan --version
herd php artisan about
herd php artisan test
vendor\bin\pint.bat --test
```

Frontend commands will be finalized when Angular is initialized.

Framework initialization command used for the backend:

```powershell
herd composer create-project laravel/laravel backend "^13.0"
```

Planned frontend initialization command for a later task:

```powershell
npx @angular/cli@22 ng new frontend --standalone --routing --style=scss --ssr --strict
```

Filament, Sanctum, permissions, and project feature packages were intentionally not installed during FND-003.

Bootstrap 5.3.8 was verified but is not selected by default because the original project requirement specifies Tailwind CSS. Add Bootstrap only if a later decision explicitly changes the frontend styling stack.

## Queue Workers

Production must run Laravel queue workers for notifications, media processing, analytics, and cleanup jobs.

## Scheduler

Production must run Laravel scheduler every minute for aggregation, cleanup, sitemap generation where applicable, and maintenance jobs.

## Storage

- Development: local public storage.
- Production: S3-compatible storage where available.
- Uploaded files must be validated and stored through Laravel filesystem abstraction.

## MySQL

Use MySQL 8.4 LTS for local/prod parity where available. MySQL 8.0 reached EOL in April 2026, so it should not be used for this production-oriented project.

During backend initialization, the installer detected a local MySQL connection and created/migrated a local `portfolio` database using Laravel's default skeleton migrations. Automated tests use the default Laravel in-memory SQLite configuration from `backend/phpunit.xml`.

`.env.example` keeps safe SQLite defaults plus commented MySQL placeholders. Production database credentials must be supplied only through `.env` or deployment secrets.

## Redis

Use Redis for cache, queues, and rate limiting where beneficial. Redis 8.10.1 is the latest verified GA release; Redis 8.2 is the current extended support line if a longer support window is preferred.

## Required PHP Extensions

Enable at least:

```text
ctype
curl
dom
fileinfo
filter
hash
intl
json
mbstring
openssl
pdo
pdo_mysql
session
tokenizer
xml
zip
```

Recommended for project media and Redis-backed queues/cache:

```text
gd or imagick
redis
```

With Herd, verify extensions through:

```powershell
herd php -m
```

Select PHP 8.5 for this project, if installed, without removing other versions:

```powershell
herd isolate 8.5
```

## Angular Deployment

Angular SSR output must be deployed with a Node-compatible SSR runtime or adapter selected during implementation. Static prerendered pages may be served through the same deployment.

## Laravel Deployment

Laravel must run behind HTTPS with secure environment values, production cookies, queues, scheduler, cache configuration, and storage links configured.

## Reverse Proxy

Reverse proxy must route public website requests to Angular SSR and API/dashboard requests to Laravel, or use a documented alternative topology.

## HTTPS

HTTPS is required in production. Secure cookies, HSTS, and security headers must be configured.

## Backup and Restore

Production backup strategy must include MySQL backups, uploaded media backups, and restore verification.

## Post-Deployment Verification

- Laravel health and migrations.
- Queue worker and scheduler status.
- Angular SSR page responses.
- API read/write smoke tests.
- Sitemap and robots responses.
- Dashboard authentication.
- Storage upload and public media access.
