You are a senior software architect and full-stack developer. Build a production-ready bilingual personal portfolio platform consisting of two connected applications:

1. A public portfolio website built with Angular.
2. A backend REST API and admin dashboard built with Laravel and Filament.

The project belongs to a Laravel developer and must be simple for clients to navigate, visually professional, fast, responsive, SEO-friendly, accessible, secure, and easy to manage without editing code.

Do not use green or any colored gradients. The visual identity must be monochrome: black, white, and neutral gray only.

Before writing implementation code, inspect the existing repository and follow all existing conventions. If the repository is empty, initialize the structure described below.

## Primary objectives

Build a portfolio platform that allows visitors to:

* Learn about the developer.
* View projects and complete case studies.
* View project screenshots and short animated walkthroughs.
* Like projects.
* Submit testimonials.
* Read blog articles.
* Contact the developer through a contact form.
* Contact the developer through WhatsApp.
* Switch between Arabic and English.
* Switch between Light and Dark mode.

Build an admin dashboard that allows the owner to:

* Manage projects and case studies.
* Manage project media.
* Manage blog articles.
* Manage services, skills, technologies, and experience.
* Review and approve testimonials.
* Manage contact messages.
* View project likes and views.
* View website analytics.
* Manage SEO fields.
* Manage general website settings.
* Manage Arabic and English content.

Do not add demo links, demo accounts, demo credentials, or public dashboard login information to project pages.

## Required technology stack

Use the latest stable mutually compatible versions available in the repository environment.

### Public frontend

* Angular.
* TypeScript with strict mode enabled.
* Angular standalone components.
* Angular Router.
* Angular SSR and hydration for SEO and performance.
* bootstarp CSS.
* Runtime Arabic and English translations using Transloco or an equivalent maintained runtime translation solution.
* Angular Signals for local UI state where appropriate.
* RxJS for asynchronous workflows where appropriate.
* Reactive Forms.
* Lazy-loaded routes.
* ESLint and Prettier.
* Vitest or the testing solution officially supported by the installed Angular version.
* Playwright for critical end-to-end tests.

### Backend and admin

* Laravel.
* PHP 8.3 or a later compatible stable version.
* MySQL 8.
* Laravel API Resources.
* Laravel Form Requests.
* Laravel Policies and Gates.
* Laravel Sanctum for dashboard/API authentication where required.
* Filament for the administration dashboard.
* Spatie Laravel Permission if multiple dashboard roles are implemented.
* Queue jobs for emails and slow background operations.
* Laravel notifications and mail.
* Laravel Scheduler for cleanup and aggregated analytics jobs.
* Pest or PHPUnit for backend tests.

### Media and storage

* Laravel filesystem abstraction.
* Local public storage during development.
* S3-compatible storage support in production.
* Images should support WebP or AVIF when possible.
* Animated explanations should use WebM or MP4 instead of large GIF files.
* Store video poster images.
* Use responsive images and lazy loading.
* Validate all uploaded file types and sizes.

### Development environment

Provide a Docker-based local development option containing at least:

* PHP application.
* MySQL.
* Redis.
* Mailpit.
* Node environment for Angular.

Do not require Docker if the repository already has a working native development environment, but document both available methods when practical.

## Repository structure

Prefer a monorepo structure similar to:

```text
portfolio-platform/
├── frontend/                 # Angular public website
├── backend/                  # Laravel API and Filament dashboard
├── docs/                     # Project documentation and tracking
├── docker/                   # Docker configuration when required
├── .editorconfig
├── .gitignore
├── README.md
└── AGENTS.md
```

Do not reorganize an existing repository destructively. Adapt this structure to the repository if necessary and document the decision.

## Mandatory Markdown workflow

The project must be managed through Markdown files. These files are required and must remain accurate throughout development:

```text
docs/
├── PROJECT_REQUIREMENTS.md
├── ARCHITECTURE.md
├── DATABASE_SCHEMA.md
├── API_CONTRACT.md
├── UI_PAGES.md
├── TASKS.md
├── DECISIONS.md
├── CHANGELOG.md
├── TESTING.md
├── DEPLOYMENT.md
└── SESSION_LOG.md
```

Also create an `AGENTS.md` file at the repository root containing the working rules Codex must follow.

### PROJECT_REQUIREMENTS.md

Record:

* Project goals.
* Confirmed features.
* Functional requirements.
* Non-functional requirements.
* Arabic and English requirements.
* Light and Dark mode requirements.
* Security requirements.
* Performance requirements.
* Explicitly excluded features.
* Assumptions that require later confirmation.

### ARCHITECTURE.md

Record:

* Frontend architecture.
* Backend architecture.
* Authentication flow.
* API communication.
* Media storage strategy.
* Analytics flow.
* Translation strategy.
* Caching strategy.
* Queue strategy.
* Deployment topology.

Include Mermaid diagrams only when they materially improve understanding.

### DATABASE_SCHEMA.md

Document every table with:

* Purpose.
* Columns and types.
* Nullable fields.
* Indexes.
* Unique constraints.
* Foreign keys.
* Delete behavior.
* Privacy-sensitive fields.
* Expected record volume when relevant.

Keep this file synchronized with migrations.

### API_CONTRACT.md

For every API endpoint document:

* HTTP method.
* URL.
* Purpose.
* Authentication requirement.
* Request parameters.
* Validation.
* Example response.
* Possible errors.
* Pagination behavior.
* Rate limits when relevant.

Keep the document synchronized with routes, controllers, requests, and resources.

### UI_PAGES.md

Document:

* Every public page.
* Every dashboard section.
* Page sections.
* Loading, empty, error, and success states.
* Desktop, tablet, and mobile behavior.
* Arabic RTL behavior.
* Light and Dark behavior.
* Required API endpoints.
* SEO requirements.

### TASKS.md

This file is the source of truth for project progress.

Use this status format:

```md
- [ ] Not started
- [~] In progress
- [x] Completed
- [!] Blocked
```

Each task must contain:

* Unique task ID.
* Feature or milestone.
* Dependencies.
* Acceptance criteria.
* Files expected to change.
* Tests required.
* Current status.
* Short implementation notes.
* Completion date when completed.

Example:

```md
### FE-001 — Initialize Angular application

- Status: [~] In progress
- Dependencies: None
- Files: `frontend/`
- Acceptance criteria:
  - Angular app runs successfully.
  - Strict TypeScript is enabled.
  - SSR is configured.
  - ESLint and formatting pass.
- Tests:
  - Production build succeeds.
  - Default unit test succeeds.
- Notes:
- Completed:
```

Rules for `TASKS.md`:

1. Create the task before starting implementation.
2. Change it to `[~]` when work begins.
3. Never mark it `[x]` until the acceptance criteria and relevant tests pass.
4. If blocked, mark it `[!]` and record the exact blocker.
5. Split large tasks into smaller verifiable tasks.
6. Update the file after every meaningful implementation step.
7. Do not delete completed tasks.
8. Add newly discovered work instead of silently expanding existing tasks.
9. Maintain a summary table at the top showing completed, active, pending, and blocked task counts.

### DECISIONS.md

Record important architectural decisions using:

* Decision ID.
* Date.
* Context.
* Options considered.
* Selected option.
* Reason.
* Consequences.

Do not silently introduce a major dependency or architectural change.

### CHANGELOG.md

Record user-visible and architectural changes under:

* Added.
* Changed.
* Fixed.
* Security.
* Removed.

### TESTING.md

Record:

* Test strategy.
* Unit tests.
* Feature tests.
* API tests.
* E2E tests.
* Commands for running tests.
* Required manual verification.
* Known untested areas.

### DEPLOYMENT.md

Document:

* Development setup.
* Environment variables.
* Build commands.
* Queue workers.
* Scheduler configuration.
* Storage configuration.
* MySQL setup.
* Redis setup.
* Angular deployment.
* Laravel deployment.
* Reverse proxy configuration.
* HTTPS requirements.
* Backup and restore approach.
* Post-deployment verification.

### SESSION_LOG.md

At the end of each work session, append:

* Date and time.
* Tasks attempted.
* Tasks completed.
* Files changed.
* Tests executed and results.
* Current blockers.
* Exact recommended next task.

Never overwrite previous session entries.

## AGENTS.md working rules

Create `AGENTS.md` with these mandatory rules:

1. Read `PROJECT_REQUIREMENTS.md`, `ARCHITECTURE.md`, and `TASKS.md` before starting work.
2. Inspect the current code before editing.
3. Preserve existing user changes.
4. Update `TASKS.md` before and after implementation.
5. Work on one clear task or tightly related task group at a time.
6. Do not mark tasks complete without verification.
7. Add or update tests for every feature.
8. Run focused tests after each feature.
9. Run broader tests before completing a milestone.
10. Update documentation whenever behavior, architecture, database schema, or APIs change.
11. Do not expose secrets, raw IP addresses, access tokens, or credentials.
12. Do not place business logic in Angular components, Laravel controllers, Filament resources, or Blade templates.
13. Avoid unnecessary dependencies.
14. Follow existing conventions before creating new abstractions.
15. Report blockers clearly and do not claim uncompleted work is finished.
16. Do not perform destructive Git or database operations without explicit approval.
17. Do not create commits unless specifically requested.
18. At the end of every session, update `SESSION_LOG.md`.
19. Always state what was implemented, tested, and still pending.
20. Use English for code, database names, API keys, classes, methods, and technical documentation. User-facing content must support both Arabic and English.

## Public website pages

### 1. Home page

Include:

* Header and navigation.
* Developer introduction.
* Professional title.
* Short value proposition.
* Primary button to view projects.
* Secondary contact button.
* Selected projects.
* Services.
* Skills and technologies.
* Experience statistics.
* Approved client testimonials.
* Latest blog posts.
* Contact call-to-action.
* Footer with social links.
* Language switcher.
* Light/Dark mode switcher.

### 2. Projects page

Include:

* Projects grid.
* Search.
* Filters by project type.
* Filters by technology.
* Featured projects.
* View count.
* Like count.
* Clear loading, empty, and error states.
* Server-side pagination or an efficient pagination strategy.

### 3. Project details page

Treat each project as a professional case study.

Include:

* Project title.
* Short summary.
* Project category.
* Developer’s role.
* Project duration.
* Technologies.
* Client or industry when public.
* Project challenge.
* Proposed solution.
* Main features.
* Development challenges.
* How those challenges were solved.
* Results and measurable outcomes.
* Screenshots.
* Short WebM/MP4 walkthroughs.
* Captions explaining every media item.
* Project likes.
* Project views.
* Related projects.
* Share buttons.
* Contact call-to-action.

Do not include:

* Demo URLs.
* Dashboard URLs.
* Demo usernames.
* Demo passwords.
* Client-private links.

### 4. About and resume page

Include:

* Professional biography.
* Experience timeline.
* Skills.
* Technologies.
* Certifications.
* Education.
* Downloadable CV.
* Availability status.

### 5. Services page

Include manageable services such as:

* Laravel application development.
* API development.
* Dashboard development.
* Existing-system maintenance.
* Performance optimization.
* Third-party integrations.

Each service must support Arabic and English.

### 6. Blog page

Include:

* Article list.
* Categories.
* Tags.
* Search.
* Featured articles.
* Pagination.
* Reading time.
* SEO metadata.

### 7. Blog details page

Include:

* Title.
* Cover image.
* Article body.
* Table of contents where useful.
* Category and tags.
* Publishing date.
* Reading time.
* Related posts.
* Share buttons.
* Structured data.

### 8. Contact page

Include:

* Name.
* Email.
* Phone number.
* Company name.
* Project type.
* Expected budget range.
* Message.
* Optional attachment.
* Privacy consent.
* Spam protection.
* Success and error messages.
* WhatsApp contact button with a configurable prefilled message.

### 9. Privacy page

Explain:

* Analytics collection.
* Hashed IP usage.
* Cookies or visitor identifiers.
* Contact form data.
* Data retention.
* Testimonial data.
* User rights.

## Localization requirements

The website must fully support:

* Arabic with RTL layout.
* English with LTR layout.
* Runtime language switching.
* Persisted language preference.
* Correct `lang` and `dir` HTML attributes.
* Localized navigation.
* Localized validation messages.
* Localized date and number formatting.
* Localized SEO metadata.
* Localized slugs where practical.
* Arabic and English content from the backend.
* Layout testing in both directions.

Do not hardcode Arabic and English content in Angular components.

Use translation files for interface text and localized database fields or translation tables for dynamic content.

Recommended backend content format:

```json
{
  "title": {
    "ar": "عنوان المشروع",
    "en": "Project title"
  }
}
```

Choose JSON translation columns or translation tables based on query requirements and document the decision in `DECISIONS.md`.

## Light and Dark mode requirements

The website must provide:

* Light mode.
* Dark mode.
* System-preference mode.
* Persistent user selection.
* No flash of incorrect theme during SSR hydration.
* Accessible contrast in both themes.
* Monochrome palette only.
* No green.
* No colorful accent.
* No gradients.
* Black primary actions in Light mode.
* White primary actions in Dark mode.
* Neutral gray borders and secondary surfaces.

Create reusable design tokens using CSS custom properties and bootstarp integration.

Suggested semantic tokens:

```css
--background
--surface
--surface-secondary
--foreground
--foreground-muted
--border
--primary
--primary-foreground
--focus-ring
--success
--warning
--danger
```

Success, warning, and danger colors may only appear in functional dashboard feedback where accessibility requires them. The public-facing brand identity must remain monochrome.

## Backend content models

Plan and implement at least:

* User.
* Project.
* ProjectTranslation or translatable project fields.
* ProjectCategory.
* Technology.
* ProjectMedia.
* ProjectView.
* ProjectLike.
* Testimonial.
* BlogPost.
* BlogCategory.
* Tag.
* Service.
* Experience.
* Skill.
* ContactMessage.
* ContactAttachment.
* SiteSetting.
* SocialLink.
* SeoMetadata or polymorphic SEO fields.
* AnalyticsEvent when needed.

Avoid unnecessary models. Document every decision.

## Project management

The Filament project manager must support:

* Arabic and English title.
* Arabic and English summary.
* Arabic and English full case study.
* Category.
* Technologies.
* Role.
* Duration.
* Industry.
* Challenge.
* Solution.
* Features.
* Development challenges.
* Results.
* Metrics.
* Featured status.
* Publication status.
* Sort order.
* SEO fields.
* Cover image.
* Gallery.
* Video walkthroughs.
* Video poster images.
* Related projects.

Media items must support:

* Type: image or video.
* Arabic caption.
* English caption.
* Sort order.
* Alt text.
* Poster.
* File size.
* MIME type.

## Project views

A project view must not rely only on a raw IP address.

Use a privacy-conscious unique-view strategy based on:

* Project ID.
* First-party visitor identifier stored in a cookie or local storage.
* Hashed IP using a server-side secret.
* User-agent hash where appropriate.
* Time window.

Recommended behavior:

* Count one unique view per project, per visitor, per 24-hour period.
* Do not store raw IP addresses.
* Prevent bots from inflating statistics when practical.
* Add database indexes suitable for the view lookup.
* Run cleanup or aggregation jobs when necessary.
* Explain the method in the privacy page.
* Document limitations caused by VPNs, shared networks, deleted cookies, and changing IPs.

## Project likes

Allow visitors to like a project without an account.

Requirements:

* One active like per visitor per project.
* Allow removing a like.
* Use the same privacy-conscious visitor identifier strategy.
* Never expose visitor identifiers through the API.
* Protect endpoints with rate limiting.
* Make UI updates optimistic but roll back if the API fails.
* Return authoritative like counts from the backend.

## Testimonials

Visitors or clients may submit testimonials.

Fields:

* Name.
* Company.
* Position.
* Profile image or company logo.
* Testimonial content.
* Rating.
* Related project when applicable.
* Contact email for private verification.
* Consent checkbox.

Workflow:

* New testimonials are pending.
* Pending testimonials are never public.
* Admin can approve, reject, edit, feature, or archive them.
* Only approved testimonials appear publicly.
* The contact email must never be included in public API responses.
* Protect submission with rate limiting and spam prevention.

## Contact messages

Contact submissions must:

* Be validated server-side.
* Be protected with rate limiting.
* Be stored in the database.
* Send an email or notification to the owner.
* Support statuses:

  * New.
  * Read.
  * Replied.
  * Qualified lead.
  * Archived.
* Support private admin notes.
* Never expose messages publicly.
* Validate and securely store attachments.
* Include protection against spam and malicious uploads.

## Analytics dashboard

The Filament dashboard should show:

* Total visits.
* Unique visitors.
* Project views.
* Project likes.
* Most viewed projects.
* Most liked projects.
* Contact conversion count.
* Recent messages.
* Pending testimonials.
* Visits by day.
* Visits by month.
* Referrer sources.
* Device category.
* Browser.
* Country only when obtained through a privacy-respecting method.
* Date range filters.
* Export where useful.

Do not build invasive tracking or fingerprinting.

## SEO requirements

Implement:

* SSR-rendered metadata.
* Localized titles and descriptions.
* Canonical URLs.
* Alternate `hreflang` links for Arabic and English.
* Open Graph metadata.
* Twitter/X cards.
* XML sitemap.
* Robots.txt.
* Structured data for:

  * Person.
  * WebSite.
  * Article.
  * BreadcrumbList.
* Clean slugs.
* Correct 404 behavior.
* Redirect strategy when slugs change.

## Performance requirements

Target:

* Lighthouse performance score of 90 or higher where practical.
* Lazy-loaded Angular routes.
* Lazy-loaded images and videos.
* Responsive image sizes.
* Minimal initial JavaScript.
* Server-side pagination.
* API caching for public data.
* Redis caching where beneficial.
* Database eager loading.
* Proper database indexes.
* Queue processing for emails and media tasks.
* No autoplay for large project videos.
* Video poster displayed before playback.
* Respect `prefers-reduced-motion`.

## Accessibility requirements

Implement:

* Semantic HTML.
* Keyboard navigation.
* Visible focus states.
* Accessible form labels.
* Accessible validation errors.
* Sufficient contrast.
* Alt text for project images.
* Captions for project videos.
* ARIA only where semantic HTML is insufficient.
* RTL accessibility testing.
* Reduced-motion support.
* Accessible Light and Dark modes.

## Security requirements

Implement:

* Server-side validation.
* Authorization policies.
* CSRF protection where applicable.
* Secure authentication.
* Rate limiting.
* Safe file validation.
* Protection from mass assignment.
* Output escaping and HTML sanitization for rich content.
* No raw IP storage.
* No secrets committed to Git.
* `.env.example` with safe placeholder values.
* Secure CORS configuration.
* Secure production cookies.
* Security headers.
* Admin activity logging for sensitive changes where practical.

## API design

Use versioned public endpoints:

```text
/api/v1/
```

Possible endpoint groups:

```text
GET    /api/v1/site
GET    /api/v1/projects
GET    /api/v1/projects/{slug}
POST   /api/v1/projects/{project}/views
POST   /api/v1/projects/{project}/likes
DELETE /api/v1/projects/{project}/likes
GET    /api/v1/testimonials
POST   /api/v1/testimonials
GET    /api/v1/posts
GET    /api/v1/posts/{slug}
GET    /api/v1/services
POST   /api/v1/contact
```

This list is not final. Design the API according to the requirements and document the final contract before implementing consumers.

Public API responses must:

* Use API Resources.
* Have consistent success and error shapes.
* Support pagination metadata.
* Never expose private fields.
* Respect the selected locale.
* Return appropriate cache headers where safe.

## Code quality rules

### Angular

* Use standalone components.
* Keep components focused.
* Separate container and presentational responsibilities when beneficial.
* Keep API access in services or repositories.
* Keep route definitions modular.
* Use typed forms.
* Avoid `any`.
* Use signals deliberately, not everywhere.
* Use RxJS operators safely.
* Clean up subscriptions.
* Provide loading, empty, error, and success states.
* Use reusable UI components.
* Do not hardcode API URLs.
* Use environment configuration.
* Test critical components, services, guards, and forms.

### Laravel

* Keep controllers thin.
* Use Form Requests.
* Use API Resources.
* Use actions or services for business workflows.
* Use policies for authorization.
* Use enums for stable statuses.
* Use database transactions for multi-step writes.
* Use events/listeners where decoupling is valuable.
* Avoid observers for hidden complex business logic.
* Prevent N+1 queries.
* Add indexes based on actual query patterns.
* Use factories and seeders.
* Use DTOs only where they reduce complexity.
* Write feature tests for every public write endpoint.
* Write tests for authorization and privacy-sensitive behavior.

### Filament

* Keep business logic outside resources and pages.
* Use clear forms and tables.
* Provide filters.
* Provide bulk actions only when safe.
* Add confirmation to destructive actions.
* Provide media previews.
* Display Arabic and English fields clearly.
* Display status badges.
* Provide analytics widgets.
* Enforce policies.

## Git and safety rules

* Inspect `git status` before editing.
* Do not discard existing changes.
* Do not use destructive Git commands.
* Do not delete user files without approval.
* Do not rewrite unrelated code.
* Do not create commits unless explicitly asked.
* If the working tree contains overlapping user changes, stop and explain the conflict.
* Use migrations for database changes.
* Never edit production data directly.
* Never run destructive migrations without explicit confirmation.

## Required implementation phases

Create these milestones in `TASKS.md`.

### Phase 0 — Discovery and documentation

* Inspect repository.
* Record current structure.
* Confirm versions.
* Write requirements.
* Write architecture.
* Write database design.
* Write API contract.
* Create page inventory.
* Create implementation tasks.

### Phase 1 — Project foundations

* Initialize or validate Laravel backend.
* Initialize or validate Angular frontend.
* Configure code quality tools.
* Configure environment examples.
* Configure Docker when required.
* Configure CI.
* Add base tests.

### Phase 2 — Backend core

* Authentication.
* Permissions.
* Database migrations.
* Models.
* Factories.
* Seeders.
* Policies.
* Service/action layer.
* API resources.
* API endpoints.
* Tests.

### Phase 3 — Filament dashboard

* Dashboard authentication.
* Projects management.
* Media management.
* Blog management.
* Testimonials moderation.
* Contact message management.
* Settings.
* Analytics widgets.
* Authorization tests.

### Phase 4 — Angular foundations

* SSR.
* Routing.
* API client.
* Translation.
* RTL/LTR handling.
* Light/Dark/System theme.
* Global layout.
* Reusable UI components.
* Error handling.
* SEO service.

### Phase 5 — Public pages

* Home.
* Projects.
* Project details.
* About.
* Services.
* Blog.
* Blog details.
* Contact.
* Privacy.
* 404.

### Phase 6 — Interactions and analytics

* Views.
* Likes.
* Testimonials.
* Contact form.
* WhatsApp.
* Analytics aggregation.
* Dashboard charts.
* Rate limiting.
* Privacy verification.

### Phase 7 — Quality and deployment

* Unit tests.
* Backend feature tests.
* E2E tests.
* Accessibility checks.
* Performance checks.
* Security review.
* Production build.
* Deployment documentation.
* Backup documentation.
* Final acceptance checklist.

## Working behavior

Follow this cycle for every task:

1. Read the relevant Markdown documentation.
2. Inspect related code.
3. Add or update the task in `TASKS.md`.
4. Mark the task `[~] In progress`.
5. Implement the smallest complete and testable unit.
6. Run formatting and focused tests.
7. Fix failures caused by the change.
8. Update API, database, architecture, or UI documentation when affected.
9. Mark the task `[x] Completed` only when acceptance criteria pass.
10. Add the result to `CHANGELOG.md`.
11. Append a summary to `SESSION_LOG.md`.
12. Report:

* What was implemented.
* Files changed.
* Tests executed.
* Test results.
* Remaining tasks.
* Blockers.
* Recommended next task.

Do not claim that a feature is finished if its tests fail, documentation is stale, or required behavior remains missing.

## First action

Start with Phase 0 only.

Do not begin full feature implementation immediately.

Perform these steps:

1. Inspect the repository and current Git status.
2. Identify whether Angular or Laravel already exists.
3. Identify installed versions and conventions.
4. Create the required Markdown documentation.
5. Produce an initial architecture proposal.
6. Produce the first database schema proposal.
7. Produce the API contract proposal.
8. Break the work into detailed tasks inside `TASKS.md`.
9. Mark only the discovery/documentation tasks as completed.
10. Report your findings, assumptions, blockers, and the recommended first implementation task.

Ask questions only when an unanswered decision would materially change the architecture or cause destructive rework. Otherwise, document a reasonable assumption and continue.


## SEO is a critical project requirement

Search engine compatibility is one of the highest priorities of this project. SEO must be designed and implemented from the beginning, not added as a final enhancement.

The Angular public website must use Angular SSR, hydration, and prerendering where appropriate. Important content must be present in the initial server-rendered HTML and must not depend only on client-side JavaScript.

SEO requirements include:

* Server-side rendering for all public pages.
* Prerendering for suitable static pages.
* Indexable project and blog detail pages.
* Unique Arabic and English URLs.
* Correct `lang` and `dir` attributes.
* Localized page titles and meta descriptions.
* Canonical URLs.
* Arabic and English `hreflang` links.
* Open Graph metadata.
* Twitter/X Card metadata.
* Indexable project descriptions and article content.
* Clean, readable, localized slugs.
* Automatic XML sitemap generation.
* Correct `robots.txt`.
* Breadcrumb navigation.
* Structured data using JSON-LD.
* `Person` structured data for the portfolio owner.
* `WebSite` structured data.
* `Article` structured data for blog posts.
* `BreadcrumbList` structured data.
* Proper HTTP status codes.
* A real server-rendered 404 page returning HTTP 404.
* Redirects when project or article slugs change.
* Optimized Core Web Vitals.
* Responsive images with explicit dimensions.
* Lazy loading for below-the-fold media.
* Optimized WebP or AVIF images.
* Video poster images.
* No autoplay for large project videos.
* Minimal render-blocking resources.
* No duplicated Arabic and English content under the same URL.
* No important text embedded only inside images or videos.

The Laravel dashboard must allow administrators to manage SEO information for every relevant page, project, blog post, category, and service.

Editable SEO fields should include:

* SEO title in Arabic and English.
* Meta description in Arabic and English.
* Canonical URL when an override is required.
* Open Graph title.
* Open Graph description.
* Open Graph image.
* Index/noindex setting.
* Follow/nofollow setting.
* Structured-data fields when required.
* Redirect URL for changed slugs.

Create a dedicated `docs/SEO.md` file containing:

* SEO architecture.
* SSR and prerendering strategy.
* URL and localization strategy.
* Metadata strategy.
* Structured-data strategy.
* Sitemap behavior.
* Robots.txt behavior.
* Redirect behavior.
* Image and video optimization rules.
* SEO acceptance criteria.
* SEO testing procedure.

Add a dedicated SEO milestone to `TASKS.md`. SEO tasks must not be considered complete until they are verified.

Required SEO verification:

* Inspect the raw server-rendered HTML without relying on client-side JavaScript.
* Confirm that titles, descriptions, canonical links, and `hreflang` links exist in the initial HTML.
* Validate structured data.
* Validate the XML sitemap.
* Validate robots.txt.
* Confirm that Arabic and English pages have separate valid URLs.
* Confirm correct 200, 301, 404, and 410 status codes where applicable.
* Run Lighthouse SEO checks.
* Check Core Web Vitals.
* Check for broken internal links.
* Check for missing image alt text.
* Check for duplicate titles and descriptions.
* Check for accidental `noindex`.
* Verify that authenticated dashboard pages are not indexable.
* Document all results in `docs/SEO.md` and `docs/TESTING.md`.

A public page must not be marked completed in `TASKS.md` until its SEO metadata, SSR output, localized URLs, accessibility, and performance have been verified.


## Mandatory framework and runtime versions

The project must use the latest stable production-ready releases available at the time implementation begins.

The required baseline is:

* PHP 8.5, using the latest stable PHP 8.5 patch release.
* Laravel 13, using the latest stable Laravel 13 release.
* Angular 22, using the latest stable Angular 22 release.
* Angular CLI and `@angular/core` must use the same major version.
* TypeScript, RxJS, Node.js, Composer, npm, Filament, bootstarp CSS, and all other dependencies must use stable versions that are officially compatible with PHP 8.5, Laravel 13, and Angular 22.

Do not use:

* Alpha releases.
* Beta releases.
* Release candidates.
* Nightly builds.
* Development branches.
* Abandoned packages.
* Packages without confirmed compatibility with the required PHP, Laravel, or Angular versions.

Before installing dependencies:

1. Verify the versions using official documentation and package metadata.
2. Record the selected versions in `docs/ARCHITECTURE.md`.
3. Record the compatibility decision in `docs/DECISIONS.md`.
4. Add the version requirements to `composer.json`, `package.json`, Docker configuration, CI configuration, and deployment documentation.
5. Create a task in `docs/TASKS.md` for verifying runtime and dependency compatibility.
6. Do not continue if a critical dependency, including Filament, does not officially support the selected Laravel or PHP version. Mark the task as blocked and report the exact incompatibility instead of forcing installation with ignored platform requirements.

The Laravel Composer requirement must target PHP 8.5:

```json
{
  "require": {
    "php": "^8.5",
    "laravel/framework": "^13.0"
  }
}
```

The Angular application must be initialized using the latest stable Angular 22 CLI release. The Angular CLI and framework packages must remain on compatible versions.

Use an actively supported stable Node.js LTS version that is officially compatible with Angular 22. Verify the exact Node.js and TypeScript compatibility before initialization instead of guessing version numbers.

Docker, local development, CI, and production must all use PHP 8.5. The project must not silently run on PHP 8.4 or an older version.

Required version verification commands must be documented and executed:

```bash
php --version
composer --version
php artisan --version
node --version
npm --version
ng version
```

Record the verified output in `docs/SESSION_LOG.md`.

The task must not be marked complete until:

* Laravel runs successfully on PHP 8.5.
* Composer dependency resolution succeeds without ignoring platform requirements.
* The Laravel test suite passes.
* The Angular production SSR build succeeds.
* Angular unit tests pass.
* No unsupported or prerelease dependency is installed.
