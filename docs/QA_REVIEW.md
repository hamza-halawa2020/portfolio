# QA_REVIEW.md

## QA-002 Review - 2026-09-25

Scope was limited to QA-002. `INF-003` and later tasks were not started.

## Accessibility

Automated Playwright checks cover representative public pages in English and Arabic:

- `/en`, `/ar`
- `/en/projects`, `/ar/projects`
- `/en/contact`, `/ar/contact`
- `/en/privacy`, `/ar/privacy`

The checks verify HTTP 200 responses, `html[lang]`, `html[dir]`, a single `#main-content` landmark target, primary navigation, visible level-one headings, footer contentinfo, no images without alt text, no horizontal overflow, skip-link focus, and accessible English contact/testimonial form controls.

Result: `cmd /c npm run test:e2e:qa002` passed with 8 tests across desktop and mobile Chromium.

## Performance and Core Web Vitals Inputs

The frontend production SSR build completed successfully. Browser initial bundle output was:

- Initial raw size: `419.35 kB`.
- Estimated transfer size: `99.41 kB`.
- Lazy public-page chunk raw size: `100.53 kB`.
- Lazy public-page estimated transfer size: `20.74 kB`.

The QA-002 Playwright suite records local navigation timing inputs for `/en` and asserts generous SSR smoke thresholds for DOMContentLoaded and full load completion on desktop and mobile Chromium. These are local timing checks, not a lab Lighthouse score.

Lighthouse status: attempted `npx lighthouse --version` and `npx --yes lighthouse --version`; both remained without output for about 60 seconds and were stopped. Lighthouse is not available as a local dependency, so no Lighthouse score is claimed for QA-002.

## Security Review

Backend security-focused tests verify:

- `.env.example` uses safe placeholders, no generated app key, no local admin credentials, and no sample database password.
- CORS uses explicit allowed origins and credential support without wildcard origins.
- Public write responses preserve private visitor state: no visitor/IP/user-agent hashes in JSON, `Cache-Control` includes `no-store`, and the encrypted visitor cookie is HTTP-only with SameSite=Lax.
- Public upload-style contact attachments are rejected.
- Interaction and analytics tables do not contain raw IP address columns.
- Unsafe image/video MIME types remain excluded from the media allowlists.

Additional review results:

- `cmd /c npm audit --audit-level=moderate`: passed, found 0 vulnerabilities.
- `cmd /c herd composer --working-dir=backend audit`: passed, no security vulnerability advisories found.
- `cmd /c herd composer --working-dir=backend run ci`: passed, including 81 backend tests, 760 assertions, and Pint.
- `cmd /c npm run ci`: passed, including Prettier, 20 frontend unit tests, and production SSR build.

Herd PHP still prints the known OPcache API warning, but Composer, Artisan, tests, and Pint exited successfully.

## Deployment and Backups

`docs/DEPLOYMENT.md` now includes production security checklist items for HTTPS, secure cookies, HSTS, dashboard noindex/private cache headers, public write no-store responses, explicit credentialed CORS, production secrets, and upload MIME limits.

Backup and restore documentation now covers MySQL backups, uploaded media backups, secret recovery handling, encrypted/off-host retention, restore drills, migration caution, and required post-restore smoke checks.

## Remaining Environmental Notes

Redis/Valkey and Mailpit/SMTP remain pending infrastructure tasks because the QA-002 bookkeeping audit found no concrete implementation or verification evidence for `INF-001` or `INF-002`.
