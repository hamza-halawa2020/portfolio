# PROJECT_REQUIREMENTS.md

## Goals

Build a production-ready bilingual personal portfolio platform for a Laravel developer. The platform consists of an Angular public website and a Laravel REST API with a Filament administration dashboard.

The public website must be simple for clients to navigate, visually professional, fast, responsive, SEO-friendly, accessible, secure, and manageable through the dashboard without editing code.

## Confirmed Features

- Public portfolio website built with Angular, SSR, hydration, bootstarp CSS, runtime translations, lazy routes, Reactive Forms, and Playwright coverage.
- Laravel API and Filament dashboard for content, messages, testimonials, analytics, media, SEO, and settings.
- Arabic and English content with runtime language switching.
- Light, dark, and system theme modes.
- Monochrome identity only: black, white, and neutral gray. No green and no colored gradients.
- Projects, case studies, media galleries, blog, services, about/resume, contact, privacy, testimonials, likes, views, and analytics.
- Privacy-conscious visitor identifiers for views and likes.
- SEO designed from the beginning, including SSR metadata, localized URLs, sitemap, robots, structured data, and redirect behavior.

## Functional Requirements

- Visitors can view public pages, filter projects and posts, like projects, submit testimonials, submit contact messages, and open WhatsApp with a configurable message.
- Administrators can manage bilingual content, media, project case studies, blog posts, services, skills, experience, testimonials, messages, site settings, SEO metadata, and analytics.
- Project details must be presented as complete case studies and must not include demo URLs, dashboard URLs, demo credentials, or client-private links.
- Testimonials are pending by default and only approved testimonials are public.
- Contact messages are private dashboard records and never public.
- Uploaded media must be validated for type and size.

## Non-Functional Requirements

- Fast SSR-rendered public pages with optimized Core Web Vitals.
- Accessible semantic UI with keyboard navigation, focus states, contrast, labels, validation errors, RTL support, and reduced-motion support.
- Secure server-side validation, authorization, CSRF where applicable, rate limiting, safe uploads, output escaping, HTML sanitization, secure CORS, production cookies, and no committed secrets.
- Maintainable code with thin controllers/components and business logic in services/actions.
- Documentation must stay synchronized with implementation.

## Arabic and English Requirements

- Arabic uses RTL layout; English uses LTR layout.
- Runtime language switching persists preference.
- `lang` and `dir` HTML attributes must be correct.
- Navigation, validation, SEO metadata, dates, numbers, slugs where practical, and dynamic content must be localized.
- User-facing interface text belongs in translation files.
- Dynamic content should use JSON translation columns unless later query requirements justify translation tables.

## Light and Dark Mode Requirements

- Light, dark, and system preference modes are required.
- Theme selection persists.
- SSR/hydration must avoid flash of incorrect theme.
- Public brand palette is monochrome only.
- Public primary actions are black in light mode and white in dark mode.
- Functional success, warning, and danger colors may appear only where accessibility requires dashboard feedback.

## Security Requirements

- No raw IP address storage.
- Public write endpoints require validation, rate limiting, and spam controls.
- Visitor identifiers must not be exposed by public APIs.
- Admin dashboard requires authentication and authorization policies.
- Secrets, access tokens, raw IPs, credentials, and demo accounts must not be committed.
- Uploads must validate MIME type, file extension, file size, and storage path safety.

## Performance Requirements

- Angular SSR and hydration for all public pages.
- Prerender static pages where appropriate.
- Lazy-loaded routes, images, and videos.
- Responsive images with explicit dimensions and WebP/AVIF support where possible.
- No autoplay for large videos.
- Server-side pagination or efficient pagination.
- Redis caching where beneficial.
- Queue slow tasks, emails, and media processing.

## Explicitly Excluded Features

- Demo dashboard links or demo credentials on public project pages.
- Invasive tracking or fingerprinting.
- Raw IP address storage.
- Colored brand accents, green UI, and colored gradients.
- Public exposure of testimonial verification email addresses or contact messages.
- Prerelease, abandoned, or unsupported framework dependencies.

## Assumptions Requiring Later Confirmation

- The target hosting environment can support PHP 8.5, MySQL 8, Redis, queue workers, scheduler, and Node build tooling.
- Laravel 13, Angular 22, PHP 8.5, and Filament compatibility must be verified before installation because this environment currently lacks PHP and Composer.
- JSON translation columns are the initial content strategy; translation tables may be used later if filtering/searching localized fields becomes too complex.
- The portfolio owner will later provide real biography, CV, social links, project content, media, WhatsApp number, analytics retention policy, and brand copy.

