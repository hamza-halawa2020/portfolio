<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'status',
        'is_featured',
        'reading_time_minutes',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'slug' => 'array',
            'excerpt' => 'array',
            'body' => 'array',
            'status' => PublicationStatus::class,
            'is_featured' => 'boolean',
            'reading_time_minutes' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublicationStatus::Published);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function scopeByCategory(Builder $query, BlogCategory|int $category): Builder
    {
        return $query->where('blog_category_id', $category instanceof BlogCategory ? $category->id : $category);
    }
}
