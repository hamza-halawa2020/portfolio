# DEPLOYMENT.md

## Development Setup

The repository uses Laravel Herd on Windows for local PHP and Composer development. Docker is intentionally not part of the local setup.

Current state: the Laravel backend has been initialized in `backend/`; the Angular frontend foundation has been initialized in `frontend/`.

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

The current Codex shell can find the installed Herd application under `C:/Program Files/Herd`, but the packaged `herd.bat` cannot resolve a usable PHP runtime and reports `No usable PHP version found`. Automation may use the existing absolute Herd PHP executable `C:/Users/hamza/.config/herd/bin/php85/php.exe` for Artisan and test commands without modifying PATH. Angular commands must use Node.js 24 and the project-local Angular CLI 22; the global Angular CLI must be ignored.

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

Current safe local placeholders:

```text
APP_ENV=local
APP_DEBUG=true
APP_URL=https://backend.test
FRONTEND_URL=http://localhost:4200
CORS_ALLOWED_ORIGINS=http://localhost:4200,http://127.0.0.1:4200,http://localhost:4000,http://127.0.0.1:4000
PUBLIC_VISITOR_COOKIE=portfolio_visitor
PUBLIC_VISITOR_COOKIE_LIFETIME=525600
PUBLIC_VISITOR_COOKIE_DOMAIN=null
VISITOR_HASH_SECRET=
RATE_LIMIT_PROJECT_VIEWS_PER_MINUTE=60
RATE_LIMIT_PROJECT_LIKES_PER_MINUTE=30
RATE_LIMIT_TESTIMONIALS_PER_HOUR=5
RATE_LIMIT_CONTACT_PER_HOUR=5
DB_CONNECTION=sqlite
# Local .env may use MySQL 8.0.41 for initial development.
# Staging/production target: MySQL 8.4 LTS.
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=portfolio
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
```

Secrets such as database usernames, database passwords, mail passwords, app keys, visitor hash secrets, and production service credentials must exist only in ignored `.env` files or deployment secret stores.

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

Frontend commands:

```powershell
$nodeDir="$env:USERPROFILE\.config\herd\bin\nvm\v24.21.0"
$env:PATH="$nodeDir;$env:PATH"
cd frontend
npm install
npm run build
npm test -- --watch=false
npm audit
```

Framework initialization command used for the backend:

```powershell
herd composer create-project laravel/laravel backend "^13.0"
```

Frontend initialization command used:

```powershell
npx @angular/cli@22 new portfolio-frontend --directory frontend --routing --style css --ssr --standalone --strict --package-manager npm --skip-git --defaults
```

Angular SSR output:

- Browser output: `frontend/dist/portfolio-frontend/browser`
- Server output: `frontend/dist/portfolio-frontend/server`
- SSR server entry: `frontend/dist/portfolio-frontend/server/server.mjs`
- Local SSR serve script: `npm run serve:ssr:portfolio-frontend`

Filament `5.8.2` was installed during ADM-001. Sanctum, Spatie Permission, and media packages are still deferred.

Dashboard setup commands:

```powershell
cd backend
herd php artisan migrate --force
herd php artisan filament:assets
herd php artisan portfolio:provision-owner-admin
herd php artisan route:list --path=admin
```

The provisioning command is interactive and hides password input. Do not create or commit real owner credentials in documentation, seeders, or tests.

Bootstrap 5.3.8 was verified but is not selected by default because the original project requirement specifies Tailwind CSS. Add Bootstrap only if a later decision explicitly changes the frontend styling stack.

## Queue Workers

Production must run Laravel queue workers for notifications, media processing, analytics, and cleanup jobs.

BE-004 currently dispatches public testimonial/contact submission events after database commit. Notification listeners, mail delivery, and database queue worker verification remain future work until SMTP/local inbox configuration is completed.

## Scheduler

Production must run Laravel scheduler every minute for aggregation, cleanup, sitemap generation where applicable, and maintenance jobs.

## Storage

- Development: local public storage.
- Production: S3-compatible storage where available.
- Uploaded files must be validated and stored through Laravel filesystem abstraction.
- Contact form attachments are rejected by the public API until private storage, validation limits, malware-scanning expectations, and dashboard-only access controls are configured.

## MySQL

Use MySQL `8.0.41` for initial local development because it is already running and verified. Use MySQL `8.4 LTS` as the staging and production target. SQL and migrations must remain compatible with both; do not use MySQL 8.4-only features without documenting the requirement.

During backend initialization, the installer detected a local MySQL connection and created/migrated a local `portfolio` database using Laravel's default skeleton migrations. Automated tests use the default Laravel in-memory SQLite configuration from `backend/phpunit.xml`.

`.env.example` keeps safe SQLite defaults plus commented MySQL placeholders. Production database credentials must be supplied only through `.env` or deployment secrets.

FND-005 verified Laravel's existing database connection non-destructively with MySQL `8.0.41`; Laravel default users, cache, and jobs migrations are marked as run. Do not run destructive commands such as `migrate:fresh`, `db:wipe`, schema drops, or database recreation without explicit approval.

## Redis

Use database-backed cache, session, and queue drivers for local development.

Redis or Valkey remains the staging/production target and future optimization for cache, queues, rate limiting, Horizon if selected, and distributed locks. Redis-dependent features and production queue configuration must not be marked complete until Redis or Valkey is configured and tested.

## Mail Testing

Local mail uses `MAIL_MAILER=log`, which requires no background mail service and avoids leaking credentials. Mailpit or another local SMTP inbox is deferred, and real SMTP configuration must be completed before contact-form email delivery is considered production-ready.

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

Angular SSR output must be deployed with a Node-compatible SSR runtime or adapter selected during implementation. The current foundation build uses Angular's SSR server output under `frontend/dist/portfolio-frontend/server` and prerenders the root route.

## Laravel Deployment

Laravel must run behind HTTPS with secure environment values, production cookies, queues, scheduler, cache configuration, and storage links configured.

Filament dashboard deployment requirements:

- Serve the dashboard at `/admin` over HTTPS only.
- Disable public registration; owner/admin creation must use `portfolio:provision-owner-admin`.
- Verify `users.is_admin=true` for the intended owner account.
- Keep dashboard routes private and protected by Laravel session/CSRF middleware.
- Confirm dashboard responses include `X-Robots-Tag: noindex, nofollow, noarchive` and private no-store cache headers.
- Confirm English LTR and Arabic RTL dashboard rendering with `/admin/login?locale=en` and `/admin/login?locale=ar`.

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
- API read smoke tests for `/api/v1/site`, `/api/v1/projects`, `/api/v1/projects/{slug}`, `/api/v1/posts`, `/api/v1/posts/{slug}`, `/api/v1/testimonials`, `/api/v1/services`, and taxonomy endpoints.
- API write smoke tests for `POST /api/v1/projects/{slug}/views`, `POST /api/v1/projects/{slug}/likes`, `DELETE /api/v1/projects/{slug}/likes`, `POST /api/v1/testimonials`, and `POST /api/v1/contact`.
- Credentialed CORS verification for the Angular origin and the encrypted `PUBLIC_VISITOR_COOKIE`.
- Sitemap and robots responses.
- Dashboard authentication.
- Dashboard authorization denies ordinary authenticated users.
- Storage upload and public media access.

Current BE-004 API deployment note: public read/write endpoints are registered under `/api/v1`. Reads return short public cache headers; writes return `no-store`, use endpoint-specific rate limits, and require the visitor hash secret/cookie configuration. Redis is not required locally because the current rate limiter uses the configured Laravel cache store.
