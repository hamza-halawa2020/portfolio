<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'summary',
        'body',
        'role',
        'duration',
        'industry',
        'challenge',
        'solution',
        'features',
        'development_challenges',
        'results',
        'metrics',
        'cover_image_path',
        'status',
        'is_featured',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'slug' => 'array',
            'summary' => 'array',
            'body' => 'array',
            'role' => 'array',
            'duration' => 'array',
            'industry' => 'array',
            'challenge' => 'array',
            'solution' => 'array',
            'features' => 'array',
            'development_challenges' => 'array',
            'results' => 'array',
            'metrics' => 'array',
            'status' => PublicationStatus::class,
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProjectView::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ProjectLike::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
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

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function scopeByCategory(Builder $query, ProjectCategory|int $category): Builder
    {
        return $query->where('project_category_id', $category instanceof ProjectCategory ? $category->id : $category);
    }

    public function scopeByTechnology(Builder $query, Technology|int $technology): Builder
    {
        $technologyId = $technology instanceof Technology ? $technology->id : $technology;

        return $query->whereHas('technologies', fn (Builder $query): Builder => $query->whereKey($technologyId));
    }
}
