# CHANGELOG.md

## Unreleased

### Added

- Initial project documentation set.
- Initial architecture, database, API, UI, SEO, testing, and deployment proposals.
- Initial task backlog and working rules.
- Laravel Herd runtime verification for PHP 8.5, Composer, Node.js 24, npm, and Angular CLI 22.
- Repository foundation files: `.editorconfig`, `.gitignore`, `.nvmrc`, `README.md`, `frontend/.gitkeep`, and `backend/.gitkeep`.
- Laravel 13 backend foundation in `backend/` with PHP `^8.5`, safe `.env.example`, UTC timezone, English default locale, documented English/Arabic supported locales, and passing default tests.
- Angular 22 frontend foundation in `frontend/` with routing, strict TypeScript, SSR, hydration, Vitest unit tests, Playwright configuration, npm lockfile, and a minimal semantic shell.
- Safe local backend environment placeholders for Herd backend URL, Angular origins, and CORS allowed origins.
- Deferred infrastructure tasks for Redis/Valkey, local mail inbox or SMTP, and MySQL 8.4 staging/production compatibility verification.

### Changed

- Local development strategy now uses Laravel Herd on Windows and explicitly excludes Docker.
- Backend documentation now uses Herd PHP and Herd Composer commands.
- Frontend documentation now uses Herd-managed Node.js 24, npm 11.19.0, and project-local Angular CLI 22 commands.
- Replaced the obsolete FND-005 Docker scope with Laravel Herd local service and environment verification.
- FND-005 now uses approved local development fallbacks: MySQL `8.0.41`, database cache/session/queue drivers, and Laravel `log` mailer.

### Fixed

- None.

### Security

- Documented no raw IP storage, no committed secrets, safe upload validation, and private dashboard requirements.

### Removed

- None.
