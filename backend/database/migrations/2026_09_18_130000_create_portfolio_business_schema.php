<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('slug');
            $table->json('summary');
            $table->json('body');
            $table->json('role')->nullable();
            $table->json('duration')->nullable();
            $table->json('industry')->nullable();
            $table->json('challenge')->nullable();
            $table->json('solution')->nullable();
            $table->json('features')->nullable();
            $table->json('development_challenges')->nullable();
            $table->json('results')->nullable();
            $table->json('metrics')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_technology', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'technology_id']);
        });

        Schema::create('project_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('path');
            $table->string('poster_path')->nullable();
            $table->json('caption')->nullable();
            $table->json('alt_text')->nullable();
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('project_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_id_hash');
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->date('viewed_on')->index();
            $table->timestamp('viewed_at')->index();
            $table->timestamps();

            $table->index(['project_id', 'viewed_on']);
            $table->index(['project_id', 'visitor_id_hash']);
            $table->unique(['project_id', 'visitor_id_hash', 'viewed_on']);
        });

        Schema::create('project_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_id_hash');
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->timestamp('liked_at')->index();
            $table->timestamps();

            $table->index(['project_id', 'visitor_id_hash']);
            $table->unique(['project_id', 'visitor_id_hash']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->json('content');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('contact_email');
            $table->string('status')->default('pending')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('consented_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('slug');
            $table->json('excerpt')->nullable();
            $table->json('body');
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedSmallInteger('reading_time_minutes')->default(1);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'tag_id']);
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug');
            $table->json('description');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('company')->nullable();
            $table->json('location')->nullable();
            $table->json('description')->nullable();
            $table->date('starts_at')->index();
            $table->date('ends_at')->nullable()->index();
            $table->boolean('is_current')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('group')->nullable()->index();
            $table->unsignedTinyInteger('level')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('project_type')->nullable();
            $table->string('budget_range')->nullable();
            $table->text('message');
            $table->string('status')->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });

        Schema::create('contact_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('seoable');
            $table->string('page_key')->nullable()->index();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();
            $table->string('og_image_path')->nullable();
            $table->boolean('robots_index')->default(true)->index();
            $table->boolean('robots_follow')->default(true);
            $table->json('structured_data')->nullable();
            $table->string('redirect_url')->nullable();
            $table->timestamps();
        });

        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->index();
            $table->string('visitor_id_hash')->nullable()->index();
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->string('url');
            $table->string('referrer_domain')->nullable();
            $table->string('device_category')->nullable();
            $table->string('browser')->nullable();
            $table->string('country')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });

        Schema::create('analytics_daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('metric');
            $table->string('dimension')->nullable();
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamps();

            $table->unique(['date', 'metric', 'dimension']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_daily_summaries');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('contact_attachments');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('services');
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('project_likes');
        Schema::dropIfExists('project_views');
        Schema::dropIfExists('project_media');
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('project_categories');
    }
};
