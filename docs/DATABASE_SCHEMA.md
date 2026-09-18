# DATABASE_SCHEMA.md

This is the initial schema proposal. It must be synchronized with Laravel migrations once implementation begins.

## Current Implemented Migrations

FND-003 initialized the Laravel 13 skeleton. BE-001 added the first portfolio business schema migration.

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2026_09_18_130000_create_portfolio_business_schema.php`

Implemented portfolio business tables:

- `project_categories`
- `technologies`
- `projects`
- `project_technology`
- `project_media`
- `project_views`
- `project_likes`
- `testimonials`
- `blog_categories`
- `tags`
- `blog_posts`
- `blog_post_tag`
- `services`
- `experiences`
- `skills`
- `contact_messages`
- `contact_attachments`
- `site_settings`
- `social_links`
- `seo_metadata`
- `analytics_events`
- `analytics_daily_summaries`

The existing Laravel infrastructure migrations already provide:

- `users`, `password_reset_tokens`, and `sessions` support from the default users migration.
- `cache` and `cache_locks` for the local database cache driver.
- `jobs`, `job_batches`, and `failed_jobs` for the local database queue driver.

FND-005 verified these migrations as already run against the local MySQL `8.0.41` database. No duplicate cache, session, or jobs migrations were generated.

BE-001 verified the full migration set with an in-memory SQLite `migrate:fresh --seed` run and applied the portfolio business schema to the local MySQL database with `artisan migrate`. No destructive local MySQL reset was performed.

BE-002 added Eloquent models, factories, policies, and a development seeder for the implemented tables. The seeder writes a small fictional bilingual dataset with `updateOrCreate` and is guarded from production.

## Conventions

- Primary keys use unsigned big integers unless Laravel conventions provide UUIDs where needed.
- Translatable user-facing fields use JSON columns with `ar` and `en` keys by default.
- Timestamps use Laravel `created_at` and `updated_at`.
- Soft deletes are used for admin-managed content where accidental deletion risk is meaningful.
- Raw IP addresses are not stored.
- Visitor and analytics records use hashes such as `visitor_id_hash`, `ip_hash`, and `user_agent_hash`.
- Laravel's default `sessions.ip_address` column predates the business schema and is framework session infrastructure. Public visitor analytics and interaction tables do not store raw IP addresses.
- Localized slug uniqueness for JSON slug columns is enforced later at the validation/application layer unless a future database-specific generated-column strategy is approved for MySQL 8 compatibility.
- Models expose explicit localized reads through `localized($attribute, $locale, $fallback)` while preserving the full English/Arabic arrays for API resources and dashboard forms.

## users

- Purpose: dashboard users and authenticated administrators.
- Columns: `id`, `name`, `email`, `email_verified_at` nullable, `password`, `remember_token`, timestamps.
- Indexes: unique `email`.
- Privacy-sensitive fields: `email`, `password`, remember token.

## project_categories

- Purpose: classify projects.
- Columns: `id`, `name` JSON, `slug` JSON, `description` JSON nullable, `sort_order`, `is_active`, timestamps, soft deletes.
- Indexes: `is_active`, `sort_order`.
- Unique constraints: localized slug uniqueness should be enforced where practical.

## technologies

- Purpose: reusable technology tags for projects and skills.
- Columns: `id`, `name`, `slug`, `icon` nullable, `sort_order`, `is_active`, timestamps.
- Unique constraints: `slug`.

## projects

- Purpose: portfolio projects and case studies.
- Columns: `id`, `project_category_id` nullable, `title` JSON, `slug` JSON, `summary` JSON, `body` JSON, `role` JSON nullable, `duration` JSON nullable, `industry` JSON nullable, `challenge` JSON nullable, `solution` JSON nullable, `features` JSON nullable, `development_challenges` JSON nullable, `results` JSON nullable, `metrics` JSON nullable, `cover_image_path` nullable, `status`, `is_featured`, `sort_order`, `published_at` nullable, timestamps, soft deletes.
- Indexes: `project_category_id`, `status`, `is_featured`, `sort_order`, `published_at`.
- Foreign keys: `project_category_id` references `project_categories.id` with null-on-delete.

## project_technology

- Purpose: many-to-many relation between projects and technologies.
- Columns: `project_id`, `technology_id`.
- Primary/unique: composite `project_id`, `technology_id`.
- Foreign keys: cascade deletes for pivot rows.

## project_media

- Purpose: screenshots, images, videos, and posters.
- Columns: `id`, `project_id`, `type`, `path`, `poster_path` nullable, `caption` JSON nullable, `alt_text` JSON nullable, `mime_type`, `file_size`, `sort_order`, timestamps.
- Indexes: `project_id`, `type`, `sort_order`.
- Foreign keys: `project_id` references `projects.id` cascade delete.

## project_views

- Purpose: privacy-conscious unique project view tracking.
- Columns: `id`, `project_id`, `visitor_id_hash`, `ip_hash` nullable, `user_agent_hash` nullable, `viewed_on`, `viewed_at`, timestamps.
- Indexes: `project_id`, `viewed_on`, `visitor_id_hash`.
- Unique constraints: `project_id`, `visitor_id_hash`, `viewed_on`.
- Privacy-sensitive fields: hashes; no raw IP.
- Delete behavior: cascade when project is deleted.

## project_likes

- Purpose: one active like per visitor per project.
- Columns: `id`, `project_id`, `visitor_id_hash`, `ip_hash` nullable, `user_agent_hash` nullable, `liked_at`, timestamps.
- Indexes: `project_id`, `visitor_id_hash`.
- Unique constraints: `project_id`, `visitor_id_hash`.
- Privacy-sensitive fields: hashes; no raw IP.

## testimonials

- Purpose: client testimonials with moderation.
- Columns: `id`, `project_id` nullable, `name`, `company` nullable, `position` nullable, `profile_image_path` nullable, `content` JSON, `rating`, `contact_email`, `status`, `is_featured`, `consented_at`, `reviewed_at` nullable, timestamps, soft deletes.
- Indexes: `project_id`, `status`, `is_featured`.
- Privacy-sensitive fields: `contact_email`, review workflow fields.
- Public API exclusion: never expose `contact_email`.

## blog_categories

- Purpose: organize blog posts.
- Columns: `id`, `name` JSON, `slug` JSON, `description` JSON nullable, `sort_order`, `is_active`, timestamps, soft deletes.
- Indexes: `is_active`, `sort_order`.

## tags

- Purpose: reusable blog tags.
- Columns: `id`, `name` JSON, `slug` JSON, timestamps.
- Unique constraints: localized slugs where practical.

## blog_posts

- Purpose: articles.
- Columns: `id`, `blog_category_id` nullable, `title` JSON, `slug` JSON, `excerpt` JSON nullable, `body` JSON, `cover_image_path` nullable, `status`, `is_featured`, `reading_time_minutes`, `published_at` nullable, timestamps, soft deletes.
- Indexes: `blog_category_id`, `status`, `is_featured`, `published_at`.

## blog_post_tag

- Purpose: many-to-many relation between posts and tags.
- Columns: `blog_post_id`, `tag_id`.
- Primary/unique: composite `blog_post_id`, `tag_id`.

## services

- Purpose: dashboard-managed services.
- Columns: `id`, `title` JSON, `slug` JSON, `description` JSON, `icon` nullable, `sort_order`, `is_active`, timestamps, soft deletes.
- Indexes: `is_active`, `sort_order`.

## experiences

- Purpose: resume timeline.
- Columns: `id`, `title` JSON, `company` JSON nullable, `location` JSON nullable, `description` JSON nullable, `starts_at`, `ends_at` nullable, `is_current`, `sort_order`, timestamps, soft deletes.

## skills

- Purpose: skill inventory.
- Columns: `id`, `name` JSON, `group` nullable, `level` nullable, `sort_order`, `is_active`, timestamps.

## contact_messages

- Purpose: private contact submissions.
- Columns: `id`, `name`, `email`, `phone` nullable, `company` nullable, `project_type` nullable, `budget_range` nullable, `message`, `status`, `admin_notes` nullable, `consented_at`, timestamps, soft deletes.
- Indexes: `status`, `created_at`.
- Privacy-sensitive fields: all submitter contact data.

## contact_attachments

- Purpose: securely stored contact form attachments.
- Columns: `id`, `contact_message_id`, `path`, `original_name`, `mime_type`, `file_size`, timestamps.
- Foreign keys: `contact_message_id` references `contact_messages.id` cascade delete.
- Privacy-sensitive fields: uploaded file metadata and content.

## site_settings

- Purpose: general website settings.
- Columns: `id`, `key`, `value` JSON nullable, timestamps.
- Unique constraints: `key`.

## social_links

- Purpose: footer/profile social links.
- Columns: `id`, `label`, `url`, `icon` nullable, `sort_order`, `is_active`, timestamps.

## seo_metadata

- Purpose: polymorphic SEO metadata.
- Columns: `id`, `seoable_type` nullable, `seoable_id` nullable, `page_key` nullable, `title` JSON nullable, `description` JSON nullable, `canonical_url` nullable, `og_title` JSON nullable, `og_description` JSON nullable, `og_image_path` nullable, `robots_index`, `robots_follow`, `structured_data` JSON nullable, `redirect_url` nullable, timestamps.
- Indexes: polymorphic pair, `page_key`, `robots_index`.

## analytics_events

- Purpose: privacy-conscious public analytics events where aggregate reporting needs raw event rows temporarily.
- Columns: `id`, `event_type`, `visitor_id_hash` nullable, `ip_hash` nullable, `user_agent_hash` nullable, `url`, `referrer_domain` nullable, `device_category` nullable, `browser` nullable, `country` nullable, `occurred_at`, timestamps.
- Indexes: `event_type`, `occurred_at`, `visitor_id_hash`.
- Privacy-sensitive fields: hashes and derived analytics attributes.

## analytics_daily_summaries

- Purpose: aggregated dashboard reporting.
- Columns: `id`, `date`, `metric`, `dimension` nullable, `value`, timestamps.
- Unique constraints: `date`, `metric`, `dimension`.
