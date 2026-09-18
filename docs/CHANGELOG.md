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
- Portfolio business database schema migration covering projects, media, testimonials, blog, services, experience, skills, contact messages, site settings, social links, SEO metadata, and privacy-conscious analytics.
- Backend schema tests for table creation, translation columns, hash-based visitor privacy fields, and key unique constraints.
- Eloquent models, relationships, casts, scopes, enums, factories, policies, and a production-guarded fictional development seeder for the portfolio business schema.
- Backend model-layer tests covering localization, relationships, enum casts, sensitive serialization, dashboard policy behavior, and seeder idempotency.

### Changed

- Local development strategy now uses Laravel Herd on Windows and explicitly excludes Docker.
- Backend documentation now uses Herd PHP and Herd Composer commands.
- Frontend documentation now uses Herd-managed Node.js 24, npm 11.19.0, and project-local Angular CLI 22 commands.
- Replaced the obsolete FND-005 Docker scope with Laravel Herd local service and environment verification.
- FND-005 now uses approved local development fallbacks: MySQL `8.0.41`, database cache/session/queue drivers, and Laravel `log` mailer.
- Database schema documentation now reflects the implemented BE-001 migration and notes that localized JSON slug uniqueness is enforced at the application layer until a database-specific indexing strategy is approved.
- Architecture and decisions now document the explicit model localization helper, broad initial dashboard policy, and fictional development seed strategy.

### Fixed

- None.

### Security

- Documented no raw IP storage, no committed secrets, safe upload validation, and private dashboard requirements.

### Removed

- None.
