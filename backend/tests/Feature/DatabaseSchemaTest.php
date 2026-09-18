<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_business_tables_are_created(): void
    {
        $tables = [
            'project_categories',
            'technologies',
            'projects',
            'project_technology',
            'project_media',
            'project_views',
            'project_likes',
            'testimonials',
            'blog_categories',
            'tags',
            'blog_posts',
            'blog_post_tag',
            'services',
            'experiences',
            'skills',
            'contact_messages',
            'contact_attachments',
            'site_settings',
            'social_links',
            'seo_metadata',
            'analytics_events',
            'analytics_daily_summaries',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }
    }

    public function test_translatable_content_uses_json_columns(): void
    {
        $jsonColumns = [
            'project_categories' => ['name', 'slug', 'description'],
            'projects' => ['title', 'slug', 'summary', 'body', 'challenge', 'solution'],
            'testimonials' => ['content'],
            'blog_categories' => ['name', 'slug', 'description'],
            'tags' => ['name', 'slug'],
            'blog_posts' => ['title', 'slug', 'excerpt', 'body'],
            'services' => ['title', 'slug', 'description'],
            'experiences' => ['title', 'company', 'location', 'description'],
            'skills' => ['name'],
            'seo_metadata' => ['title', 'description', 'og_title', 'og_description', 'structured_data'],
            'site_settings' => ['value'],
        ];

        foreach ($jsonColumns as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertContains(Schema::getColumnType($table, $column), ['json', 'text']);
            }
        }
    }

    public function test_privacy_sensitive_interaction_tables_store_hashes_not_raw_ip_addresses(): void
    {
        foreach (['project_views', 'project_likes', 'analytics_events'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'ip_hash'));
            $this->assertFalse(Schema::hasColumn($table, 'ip_address'));
            $this->assertFalse(Schema::hasColumn($table, 'raw_ip'));
        }
    }

    public function test_key_unique_constraints_are_present(): void
    {
        $indexes = collect(Schema::getIndexes('project_views'));
        $this->assertTrue($indexes->contains(fn (array $index): bool => $index['unique'] === true
            && $index['columns'] === ['project_id', 'visitor_id_hash', 'viewed_on']));

        $indexes = collect(Schema::getIndexes('project_likes'));
        $this->assertTrue($indexes->contains(fn (array $index): bool => $index['unique'] === true
            && $index['columns'] === ['project_id', 'visitor_id_hash']));

        $indexes = collect(Schema::getIndexes('analytics_daily_summaries'));
        $this->assertTrue($indexes->contains(fn (array $index): bool => $index['unique'] === true
            && $index['columns'] === ['date', 'metric', 'dimension']));
    }
}
