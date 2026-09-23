# UI_PAGES.md

## Global UI Requirements

- Monochrome public design using black, white, and neutral gray only.
- Responsive layouts for desktop, tablet, and mobile.
- Arabic pages use RTL; English pages use LTR.
- Light, dark, and system theme controls are available globally.
- Language switcher is available globally.
- Loading, empty, error, and success states are required for data-driven pages.
- SEO metadata, localized URLs, accessibility, and performance verification are part of page completion.

## Public Pages

FE-001 shell status:

- A reusable SSR-rendered Angular app shell exists with skip-to-content, responsive header, accessible mobile navigation, language controls, light/dark/system theme controls, main outlet, and footer.
- English and Arabic public routes exist for all public page types listed below. Arabic routes render RTL; English routes render LTR.
- Current page bodies are internal FE-001 placeholders only and are intentionally noindexed. Full page content, API data, loading/empty/error/success states, forms, interactions, and media remain in later tasks.
- The language switch preserves static page locations. Dynamic project/blog detail switches intentionally return to the target language listing page until localized slug correspondence is available from API data.
- No demo URLs, dashboard demo links, demo credentials, or private client links are present in the frontend shell.

### Home

- Sections: header, developer intro, professional title, value proposition, project CTA, contact CTA, selected projects, services, skills/technologies, statistics, testimonials, latest blog posts, contact CTA, footer.
- APIs: `GET /api/v1/site`, project/post/testimonial/service data as needed.
- SEO: `WebSite` and `Person` structured data.

### Projects

- Sections: project grid, search, category filters, technology filters, featured projects, pagination, counts.
- States: loading, empty search, API error, pagination loading.
- APIs: `GET /api/v1/projects`.

### Project Details

- Sections: case study header, category, role, duration, technologies, challenge, solution, features, development challenges, results, metrics, screenshots, video walkthroughs, captions, likes, views, related projects, sharing, contact CTA.
- Exclusions: no demo URLs, dashboard URLs, demo credentials, or private client links.
- APIs: `GET /api/v1/projects/{slug}`, view and like endpoints.
- SEO: localized title/description, canonical, `hreflang`, Open Graph, BreadcrumbList.

### About and Resume

- Sections: biography, timeline, skills, technologies, certifications, education, CV download, availability.
- APIs: `GET /api/v1/about`.

### Services

- Sections: manageable service list with bilingual titles and descriptions.
- APIs: `GET /api/v1/services`.

### Blog

- Sections: article list, categories, tags, search, featured articles, pagination, reading time.
- APIs: `GET /api/v1/posts`.

### Blog Details

- Sections: title, cover image, article body, table of contents when useful, category, tags, date, reading time, related posts, share buttons.
- APIs: `GET /api/v1/posts/{slug}`.
- SEO: Article structured data.

### Contact

- Fields: name, email, phone, company, project type, budget range, message, optional attachment, privacy consent, spam protection.
- States: validation, submitting, success, error.
- APIs: `POST /api/v1/contact`.
- Extra: WhatsApp button with configurable prefilled message.

### Privacy

- Content: analytics collection, hashed IP usage, cookies/visitor identifiers, contact form data, retention, testimonial data, user rights, project view/like limitations.
- APIs: may use `GET /api/v1/site` for settings.

### 404

- Requirements: real SSR-rendered 404 page with HTTP 404 status.

## Dashboard Sections

- Projects and case studies.
- Project media.
- Blog posts, categories, and tags.
- Services, skills, technologies, and experience.
- Testimonials moderation.
- Contact messages.
- Project likes and views.
- Analytics.
- SEO metadata.
- Site settings and social links.

