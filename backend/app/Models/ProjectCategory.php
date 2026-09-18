<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\ProjectCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCategory extends Model
{
    /** @use HasFactory<ProjectCategoryFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'slug' => 'array',
            'description' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
